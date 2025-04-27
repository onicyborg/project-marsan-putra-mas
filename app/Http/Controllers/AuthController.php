<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function store(Request $request)
    {
        // Validasi input
        $validated = $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'nullable|string|max:100',
            'phone' => 'required|numeric|digits_between:9,13|unique:users,phone_number',
            'address' => 'nullable|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
        ]);

        // Gabungkan nama depan dan belakang
        $fullName = trim($validated['first_name'] . ' ' . $validated['last_name']);

        // Simpan user baru
        User::create([
            'name' => $fullName,
            'phone_number' => '+62' . ltrim($validated['phone'], '0'),
            'address' => $validated['address'] ?? '-',
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'user',
        ]);

        // Redirect ke login
        return redirect()->route('login.get')->with('success', 'Registration successful. Please login.');
    }

    public function login(Request $request)
    {
        // Validasi input
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        // Ambil data input
        $credentials = $request->only('email', 'password');

        // Coba autentikasi
        if (Auth::attempt($credentials, $request->filled('remember'))) {
            return redirect()->intended('/'); // arahkan ke halaman utama atau dashboard
        }

        // Jika gagal login
        return back()->with('error', 'Email atau password tidak sesuai')->withInput();
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'email'         => 'required|email|unique:users,email,' . $user->id,
            'name'          => 'required|string|max:255',
            'phone_number'  => 'required|numeric|digits_between:9,13|unique:users,phone_number,' . $user->id,
            'address'       => 'required|string|max:500',
        ]);

        // Tambahkan awalan +62 jika belum ada
        $phone = $request->phone_number;
        if (!str_starts_with($phone, '+62')) {
            $phone = '+62' . ltrim($phone, '0');
        }

        $user->update([
            'email'         => $validated['email'],
            'name'          => $validated['name'],
            'phone_number'  => $phone,
            'address'       => $validated['address'],
        ]);

        return redirect()->back()->with('success', 'Profile updated successfully.');
    }

    public function changePassword(Request $request)
    {
        // Validasi input
        $request->validate([
            'old_password' => ['required'],
            'new_password' => ['required', 'min:8', 'confirmed'], // 'confirmed' otomatis cek new_password_confirmation
        ]);

        $user = Auth::user();

        // Cek apakah old_password cocok dengan password sekarang
        if (!Hash::check($request->old_password, $user->password)) {
            return back()->withErrors(['old_password' => 'Old password is incorrect.']);
        }

        // Update password baru
        $user->password = Hash::make($request->new_password);
        $user->save();

        return back()->with('success', 'Password has been changed successfully.');
    }


    public function logout()
    {
        Auth::logout();
        return redirect('/');
    }
}
