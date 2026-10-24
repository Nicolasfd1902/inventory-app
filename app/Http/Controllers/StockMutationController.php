<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\StockMutation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StockMutationController extends Controller
{
    public function getData()
    {
        $mutations = StockMutation::with('item')->latest()->get();
        return response()->json($mutations);
    }

    public function store(Request $request)
    {
        $request->validate([
            'item_id' => 'required|exists:items,id',
            'type'    => 'required|in:masuk,keluar',
            'qty'     => 'required|integer|min:1',
            'reason'  => 'required|string',
        ]);

        $item = Item::findOrFail($request->item_id);

        // Validasi jika stok keluar melebihi stok yang ada
        if ($request->type === 'keluar' && $item->stock < $request->qty) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Stok tidak mencukupi untuk transaksi keluar!'
            ], 422);
        }

        // Mulai Database Transaction[cite: 1]
        DB::beginTransaction();
        try {
            // 1. Update jumlah stok di tabel items
            if ($request->type === 'masuk') {
                $item->increment('stock', $request->qty);
            } else {
                $item->decrement('stock', $request->qty);
            }

            // 2. Simpan log mutasi stok[cite: 1]
            StockMutation::create([
                'item_id' => $request->item_id,
                'type'    => $request->type,
                'qty'     => $request->qty,
                'reason'  => $request->reason,
            ]);

            DB::commit(); // Simpan permanen jika semua sukses

            return response()->json([
                'status'  => 'success',
                'message' => 'Transaksi stok berhasil dicatat!'
            ]);
        } catch (\Exception $e) {
            DB::rollBack(); // Batal seluruh perubahan jika ada error

            return response()->json([
                'status'  => 'error',
                'message' => 'Gagal memproses transaksi: ' . $e->getMessage()
            ], 500);
        }
    }
}
