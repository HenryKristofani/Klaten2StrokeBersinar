<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Sale;
use Illuminate\Http\Request;

class SaleController extends Controller
{
    // Halaman rekap: per kategori + total income semua kategori
    public function recap()
    {
        $rekap = Category::leftJoin('sales', 'sales.category_id', '=', 'categories.id')
            ->selectRaw('categories.*, COALESCE(SUM(sales.qty),0) as total_qty, COALESCE(SUM(sales.qty * sales.price),0) as income')
            ->groupBy('categories.id')
            ->orderBy('categories.id')
            ->get();

        return view('recap', [
            'rekap'       => $rekap,
            'totalIncome' => $rekap->sum('income'),
            'totalQty'    => $rekap->sum('total_qty'),
        ]);
    }

    // Daftar penjualan + form input, bisa filter per kategori
    public function index(Request $r)
    {
        $sales = Sale::with('category')
            ->when($r->category, fn ($q) => $q->where('category_id', $r->category))
            ->latest('purchased_at')
            ->get();

        return view('sales', [
            'sales'      => $sales,
            'categories' => Category::orderBy('id')->get(),
            'filter'     => $r->category,
            'sumQty'     => $sales->sum('qty'),
            'sumIncome'  => $sales->sum(fn ($s) => $s->qty * $s->price),
        ]);
    }

    public function store(Request $r)
    {
        $d = $r->validate([
            'category_id'  => 'required|exists:categories,id',
            'buyer_name'   => 'required|string|max:100',
            'qty'          => 'required|integer|min:1',
            'price'        => 'nullable|integer|min:0',
            'purchased_at' => 'required|date',
            'note'         => 'nullable|string|max:500',
        ]);

        $cat = Category::findOrFail($d['category_id']);

        if ($cat->quota > 0) {
            $terjual = $cat->sales()->sum('qty');
            if ($terjual + $d['qty'] > $cat->quota) {
                return back()->withInput()->withErrors([
                    'qty' => "Kuota {$cat->name} tersisa " . max(0, $cat->quota - $terjual),
                ]);
            }
        }

        $d['price'] = $d['price'] ?? $cat->price; // kosong = pakai harga kategori
        Sale::create($d);

        return back()->with('ok', 'Penjualan dicatat');
    }

    public function destroy(Sale $sale)
    {
        $sale->delete();
        return back()->with('ok', 'Penjualan dihapus');
    }
}