<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSaleRequest;
use App\Http\Requests\UpdateSaleRequest;
use App\Models\Category;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SaleController extends Controller
{
    /** Halaman rekap: per kategori + total income (hanya sales aktif, tidak termasuk soft-deleted) */
    public function recap()
    {
        // whereNull('sales.deleted_at') memastikan soft-deleted tidak ikut dihitung
        // di raw LEFT JOIN (eloquent global scope tidak berlaku di raw join)
        $rekap = Category::leftJoin('sales', function ($join) {
                $join->on('sales.category_id', '=', 'categories.id')
                     ->whereNull('sales.deleted_at');
            })
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

    /** Daftar penjualan dengan filter + pagination (SoftDeletes otomatis exclude deleted) */
    public function index(Request $r)
    {
        // 1. Base query — hanya filter, tanpa orderBy
        //    SoftDeletes trait otomatis tambahkan WHERE deleted_at IS NULL
        $base = Sale::with('category')
            ->when($r->filled('category'), fn ($q) => $q->where('category_id', $r->category))
            ->when($r->filled('catatan'),  fn ($q) => $q->where('note', 'ilike', '%' . $r->catatan . '%'))
            ->when($r->filled('dari'),     fn ($q) => $q->whereDate('purchased_at', '>=', $r->dari))
            ->when($r->filled('sampai'),   fn ($q) => $q->whereDate('purchased_at', '<=', $r->sampai));

        // 2. Agregat dari clone bersih tanpa orderBy (aman di PostgreSQL)
        $sumQty    = (clone $base)->sum('qty');
        $sumIncome = (clone $base)->sum(DB::raw('qty * price'));

        // 3. Daftar dengan ordering + pagination
        $sales = (clone $base)
            ->orderByDesc('purchased_at')
            ->paginate(20)
            ->withQueryString();

        return view('sales.index', [
            'sales'      => $sales,
            'categories' => Category::withSum('sales', 'qty')->orderBy('id')->get(),
            'filter'     => $r->category,
            'catatan'    => $r->catatan,
            'dari'       => $r->dari,
            'sampai'     => $r->sampai,
            'sumQty'     => $sumQty,
            'sumIncome'  => $sumIncome,
        ]);
    }

    public function create()
    {
        $categories = Category::withSum('sales', 'qty')->orderBy('id')->get();
        return view('sales.create', compact('categories'));
    }

    /**
     * Store: purchased_at selalu now() di server.
     * JSON → 201 + sisa_kuota. HTML → redirect.
     */
    public function store(StoreSaleRequest $request)
    {
        $sale = DB::transaction(function () use ($request) {
            $data = $request->validated();

            $cat           = Category::lockForUpdate()->findOrFail($data['category_id']);
            $data['price'] = $cat->price;
            $data['purchased_at'] = now();

            return Sale::create($data);
        });

        if ($request->expectsJson()) {
            $sisaKuota = Category::withSum('sales', 'qty')
                ->get()
                ->mapWithKeys(fn ($c) => [
                    $c->id => $c->quota > 0
                        ? max(0, $c->quota - (int) ($c->sales_sum_qty ?? 0))
                        : null,
                ]);

            return response()->json([
                'message'    => 'Penjualan berhasil dicatat.',
                'sisa_kuota' => $sisaKuota,
            ], 201);
        }

        return redirect()->route('sales.index')
            ->with('ok', 'Penjualan berhasil dicatat.');
    }

    public function edit(Sale $sale)
    {
        $categories = Category::withSum('sales', 'qty')->orderBy('id')->get();
        return view('sales.edit', compact('sale', 'categories'));
    }

    public function update(UpdateSaleRequest $request, Sale $sale)
    {
        DB::transaction(function () use ($request, $sale) {
            $data = $request->validated();

            if ($data['category_id'] == $sale->category_id) {
                $data['price'] = $sale->price;
            } else {
                $cat           = Category::lockForUpdate()->findOrFail($data['category_id']);
                $data['price'] = $cat->price;
            }

            $sale->update($data);
        });

        return redirect()->route('sales.index')
            ->with('ok', 'Penjualan berhasil diperbarui.');
    }

    public function destroy(Request $request, Sale $sale)
    {
        $request->validate([
            'delete_reason' => 'required|string|max:500',
        ], [
            'delete_reason.required' => 'Alasan penghapusan wajib diisi.',
            'delete_reason.max'      => 'Alasan maksimal 500 karakter.',
        ]);

        DB::transaction(function () use ($request, $sale) {
            // Simpan alasan terlebih dahulu, baru soft-delete
            $sale->delete_reason = $request->delete_reason;
            $sale->save();
            $sale->delete(); // soft delete: mengisi deleted_at
        });

        $catName = $sale->category->name;
        $qty     = $sale->qty;

        return redirect()->route('sales.index')
            ->with('ok', "Transaksi dihapus (alasan: {$request->delete_reason}). {$qty} tiket {$catName} dikembalikan ke kuota.");
    }

    /** Riwayat penghapusan: hanya soft-deleted records */
    public function trash()
    {
        $sales = Sale::onlyTrashed()
            ->with('category')
            ->orderByDesc('deleted_at')
            ->paginate(20)
            ->withQueryString();

        return view('sales.trash', compact('sales'));
    }
}