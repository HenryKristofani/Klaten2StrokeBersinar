<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        return view('categories', ['categories' => Category::orderBy('id')->get()]);
    }

    public function store(Request $r)
    {
        Category::create($this->data($r));
        return back()->with('ok', 'Kategori ditambahkan');
    }

    public function update(Request $r, Category $category)
    {
        $category->update($this->data($r));
        return back()->with('ok', 'Kategori diperbarui');
    }

    public function destroy(Category $category)
    {
        $category->delete(); // penjualan di kategori ini ikut terhapus
        return back()->with('ok', 'Kategori dihapus');
    }

    private function data(Request $r): array
    {
        $d = $r->validate([
            'name'  => 'required|string|max:100',
            'price' => 'required|integer|min:0',
            'quota' => 'nullable|integer|min:0',
        ]);
        $d['quota'] = $d['quota'] ?? 0;
        return $d;
    }
}