<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Models\Category;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::withCount('sales')
            ->withSum('sales', 'qty')
            ->orderBy('id')
            ->get();

        return view('categories.index', compact('categories'));
    }

    public function create()
    {
        return view('categories.create');
    }

    public function store(StoreCategoryRequest $request)
    {
        Category::create($request->validated());
        return redirect()->route('categories.index')
            ->with('ok', 'Kategori berhasil ditambahkan.');
    }

    public function edit(Category $category)
    {
        $terjual = $category->sales()->sum('qty');
        return view('categories.edit', compact('category', 'terjual'));
    }

    public function update(UpdateCategoryRequest $request, Category $category)
    {
        $category->update($request->validated());
        return redirect()->route('categories.index')
            ->with('ok', 'Kategori berhasil diperbarui.');
    }

    public function destroy(Category $category)
    {
        $category->delete();
        return redirect()->route('categories.index')
            ->with('ok', 'Kategori dan seluruh penjualannya berhasil dihapus.');
    }
}