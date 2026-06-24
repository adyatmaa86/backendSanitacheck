<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class SettingsController extends Controller
{
    /**
     * Show Edit Profile page.
     */
    public function profile()
    {
        return view('settings.profile');
    }

    /**
     * Update profile.
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name'         => 'required|string|max:255',
            'email'        => 'required|email|max:255|unique:users,email,' . $user->id,
            'password'     => 'nullable|min:6|confirmed',
            'phone_number' => 'required|string|max:20',
        ]);

        $cleanName = preg_replace('/\s*\((?:Admin|Petugas)\)\s*$/i', '', $request->name);
        $roleSuffix = ' (' . ucfirst($user->role) . ')';

        $user->name         = $cleanName . $roleSuffix;
        $user->email        = $request->email;
        $user->phone_number = $request->phone_number;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return redirect()->route('settings.profile')->with('success', 'Profil berhasil diperbarui.');
    }

    /**
     * Show Add Petugas form.
     */
    public function addPetugas()
    {
        if (Auth::user()->role !== 'admin') {
            abort(403);
        }
        return view('settings.add-petugas');
    }

    /**
     * Store new petugas.
     */
    public function storePetugas(Request $request)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403);
        }

        $request->validate([
            'name'         => 'required|string|max:255',
            'email'        => 'required|email|max:255|unique:users,email',
            'password'     => 'required|min:6|confirmed',
            'phone_number' => 'required|string|max:20',
        ]);

        User::create([
            'name'         => $request->name . ' (Petugas)',
            'email'        => $request->email,
            'password'     => Hash::make($request->password),
            'role'         => 'petugas',
            'phone_number' => $request->phone_number,
        ]);

        return redirect()->route('settings.add-petugas')->with('success', 'Akun petugas berhasil ditambahkan.');
    }

    /**
     * Show Add Admin form.
     */
    public function addAdmin()
    {
        if (Auth::user()->role !== 'admin') {
            abort(403);
        }
        return view('settings.add-admin');
    }

    /**
     * Store new admin.
     */
    public function storeAdmin(Request $request)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403);
        }

        $request->validate([
            'name'         => 'required|string|max:255',
            'email'        => 'required|email|max:255|unique:users,email',
            'password'     => 'required|min:6|confirmed',
            'phone_number' => 'required|string|max:20',
        ]);

        User::create([
            'name'         => $request->name . ' (Admin)',
            'email'        => $request->email,
            'password'     => Hash::make($request->password),
            'role'         => 'admin',
            'phone_number' => $request->phone_number,
        ]);

        return redirect()->route('settings.add-admin')->with('success', 'Akun admin berhasil ditambahkan.');
    }
}
