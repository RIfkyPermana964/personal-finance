<?php

namespace App\Http\Controllers;

use App\Enums\CategoryType;
use App\Http\Requests\StoreCategoryRequest;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(Request $request): View
    {
        $userId = $request->user()->id;

        $expenseParents = Category::forUser($userId)
            ->where('type', CategoryType::EXPENSE)
            ->whereNull('parent_id')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->with(['children' => fn ($q) => $q->forUser($userId)->orderBy('sort_order')->orderBy('id')])
            ->get();

        $incomeCategories = Category::forUser($userId)
            ->where('type', CategoryType::INCOME)
            ->whereNull('parent_id')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        return view('categories.index', compact('expenseParents', 'incomeCategories'));
    }

    public function reorder(Request $request): JsonResponse
    {
        $request->validate([
            'items' => ['required', 'array'],
            'items.*.id' => ['required', 'integer'],
            'items.*.sort_order' => ['required', 'integer'],
            'items.*.parent_id' => ['nullable'],
        ]);

        $userId = $request->user()->id;

        foreach ($request->input('items') as $item) {
            $category = Category::where('id', $item['id'])
                ->where(function ($q) use ($userId) {
                    $q->where('user_id', $userId)->orWhereNull('user_id');
                })
                ->first();

            if ($category) {
                $updateData = ['sort_order' => $item['sort_order']];
                if (array_key_exists('parent_id', $item)) {
                    $updateData['parent_id'] = $item['parent_id'] ? (int) $item['parent_id'] : null;
                }
                $category->update($updateData);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Urutan kategori berhasil diperbarui!',
        ]);
    }

    public function store(StoreCategoryRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['user_id'] = $request->user()->id;
        $data['is_active'] = true;

        Category::create($data);

        return redirect()->route('categories.index')->with('success', 'Kategori berhasil ditambahkan!');
    }

    public function update(StoreCategoryRequest $request, Category $category): RedirectResponse
    {
        // Boleh edit semua kategori termasuk sistem bawaan
        $data = $request->validated();
        $category->update($data);

        return redirect()->route('categories.index')->with('success', "Kategori '{$category->name}' berhasil diperbarui!");
    }

    public function destroy(Request $request, Category $category): RedirectResponse
    {
        if ($category->transactions()->exists()) {
            $category->update(['is_active' => false]);
            $msg = "Kategori '{$category->name}' dinonaktifkan karena sudah memiliki riwayat transaksi.";
        } else {
            // Hapus subkategori juga
            $category->children()->delete();
            $category->delete();
            $msg = "Kategori '{$category->name}' berhasil dihapus.";
        }

        return redirect()->route('categories.index')->with('success', $msg);
    }
}
