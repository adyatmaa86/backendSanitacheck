<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\JenisFasilitas;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class JenisFasilitasController extends Controller
{
    public function store(Request $request)
    {
        if (Auth::user()->role !== 'admin') {
            return response()->json(['message' => 'Unauthorized action.'], 403);
        }

        $request->validate([
            'nama_jenis' => 'required|string|max:255|unique:jenis_fasilitas,nama_jenis',
        ]);

        JenisFasilitas::create([
            'nama_jenis' => $request->nama_jenis,
            'slug' => Str::slug($request->nama_jenis),
        ]);

        return redirect()->back()->with('success', 'Jenis fasilitas berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        if (Auth::user()->role !== 'admin') {
            return response()->json(['message' => 'Unauthorized action.'], 403);
        }

        $jenis = JenisFasilitas::findOrFail($id);

        $request->validate([
            'nama_jenis' => 'required|string|max:255|unique:jenis_fasilitas,nama_jenis,' . $jenis->id,
        ]);

        $jenis->update([
            'nama_jenis' => $request->nama_jenis,
            'slug' => Str::slug($request->nama_jenis),
        ]);

        return redirect()->back()->with('success', 'Jenis fasilitas berhasil diperbarui.');
    }

    public function destroy($id)
    {
        if (Auth::user()->role !== 'admin') {
            return response()->json(['message' => 'Unauthorized action.'], 403);
        }

        $jenis = JenisFasilitas::findOrFail($id);
        $jenis->delete();

        return redirect()->back()->with('success', 'Jenis fasilitas berhasil dihapus.');
    }
}
