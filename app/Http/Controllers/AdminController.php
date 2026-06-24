<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    public function index(Request $request)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }

        $search = $request->input('search');
        $query = User::where('role', 'admin')
            ->where('id', '!=', Auth::id());

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $admins = $query->orderBy('name', 'asc')->paginate(5);

        if ($request->ajax()) {
            return view('admin.partials.table', compact('admins'))->render();
        }

        $totalAdmins = User::where('role', 'admin')->where('id', '!=', Auth::id())->count();
        $totalPetugas = User::where('role', 'petugas')->count();

        return view('admin.index', compact('admins', 'search', 'totalAdmins', 'totalPetugas'));
    }

    public function destroy($id)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }

        $user = User::where('role', 'admin')->findOrFail($id);

        $user->delete();

        return redirect()->route('admin.index')->with('success', 'Admin berhasil dihapus.');
    }
}
