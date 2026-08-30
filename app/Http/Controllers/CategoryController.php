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
        $incomeCategories = Category::forUser($userId)->where('type', CategoryType::INCOME)->get();
        $expenseParents = Category::with('children')->forUser($userId)->where('type', CategoryType::EXPENSE)->whereNull('parent_id')->get();

        return view('categories.index', compact('incomeCategories', 'expenseParents'));
    }

    public function store(StoreCategoryRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['user_id'] = $request->user()->id;
        $data['is_active'] = $request->boolean('is_active', true);

        Category::create($data);

        return redirect()->route('categories.index')->with('success', 'Kategori baru berhasil ditambahkan!');
    }

    public function update(StoreCategoryRequest $request, Category $category): RedirectResponse
    {
        if ($category->user_id && $category->user_id !== $request->user()->id) {
            abort(403);
        }

        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active', true);

        $category->update($data);

        return redirect()->route('categories.index')->with('success', 'Kategori berhasil diperbarui!');
    }

    public function destroy(Request $request, Category $category): RedirectResponse
    {
        if ($category->user_id && $category->user_id !== $request->user()->id) {
            abort(403);
        }

        // Deactivate if has transactions or is system default, otherwise soft/safe delete
        if ($category->transactions()->exists() || is_null($category->user_id)) {
            $category->update(['is_active' => false]);
            $msg = 'Kategori dinonaktifkan untuk menjaga integritas data historis transaksi.';
        } else {
            $category->delete();
            $msg = 'Kategori berhasil dihapus.';
        }

        return redirect()->route('categories.index')->with('success', $msg);
    }
}