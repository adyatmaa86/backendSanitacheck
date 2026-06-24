<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PetugasController extends Controller
{
    /**
     * Display a listing of registered petugas.
     */
    public function index(Request $request)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }

        $search = $request->input('search');
        $query = User::where('role', 'petugas');

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $petugas = $query->orderBy('name', 'asc')->paginate(5);

        if ($request->ajax()) {
            return view('petugas.partials.table', compact('petugas'))->render();
        }

        return view('petugas.index', compact('petugas', 'search'));
    }

    /**
     * Remove the specified petugas from storage.
     */
    public function destroy($id)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }

        $user = User::where('role', 'petugas')->findOrFail($id);
        
        // Remove or clear relation in facilities penanggung_jawab
        \App\Models\Fasilitas::where('penanggung_jawab', $user->id)->update(['penanggung_jawab' => null]);
        
        $user->delete();

        return redirect()->route('petugas.index')->with('success', 'Petugas berhasil dihapus.');
    }
}
