<?php

namespace App\Http\Controllers;

use App\Enums\CategoryType;
use App\Http\Requests\StoreCategoryRequest;
use App\Models\Category;
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
            ->with(['children' => fn ($q) => $q->forUser($userId)])
            ->get();

        $incomeCategories = Category::forUser($userId)
            ->where('type', CategoryType::INCOME)
            ->whereNull('parent_id')
            ->get();

        return view('categories.index', compact('expenseParents', 'incomeCategories'));
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
