<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class MemberController extends Controller
{
    public function index()
    {
        // Ambil semua anggota dari database
        $members = User::where('role', 'user')->get();

        // Tampilkan halaman anggota dengan data anggota
        return view('admin.member', compact('members'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'         => 'required|string|max:255',
            'phone_number' => 'required|string|max:20',
            'email'        => 'required|email|unique:users,email',
            'address'      => 'nullable|string|max:255',
        ]);

        $phone = $request->phone_number;
        if (!str_starts_with($phone, '+62')) {
            $phone = '+62' . ltrim($phone, '0');
        }

        User::create([
            'name'         => $request->name,
            'phone_number' => $phone,
            'email'        => $request->email,
            'address'      => $request->address,
            'password'     => Hash::make('Qwerty123*'),
            'role'         => 'user',
        ]);

        return redirect()->back()->with('success', 'Member berhasil ditambahkan.');
    }

    public function update(Request $request)
    {
        $request->validate([
            'id'           => 'required|exists:users,id',
            'name'         => 'required|string|max:255',
            'phone_number' => 'required|string|max:20',
            'email'        => 'required|email|unique:users,email,' . $request->id,
            'address'      => 'nullable|string|max:255',
        ]);

        $user = User::findOrFail($request->id);
        $phone = $request->phone_number;
        if (!str_starts_with($phone, '+62')) {
            $phone = '+62' . ltrim($phone, '0');
        }
        $user->update([
            'name'         => $request->name,
            'phone_number' => $phone,
            'email'        => $request->email,
            'address'      => $request->address,
        ]);

        return redirect()->back()->with('success', 'Member berhasil diperbarui.');
    }

    public function getMemberByNumber($phone)
    {
        // Cari member berdasarkan nomor HP
        // Tambahkan awalan +62 jika belum ada
        if (!str_starts_with($phone, '+62')) {
            $phone = '+62' . ltrim($phone, '0');
        }
        $member = User::where('phone_number', $phone)->first();

        if ($member) {
            return response()->json([
                'success' => true,
                'member' => [
                    'id' => $member->id,
                    'name' => $member->name,
                    'email' => $member->email,
                    'phone' => $member->phone_number,
                ]
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Member not found.'
            ]);
        }
    }

    public function destroy($id)
    {
        $member = User::findOrFail($id);
        $member->delete();

        return redirect()->back()->with('success', 'Member deleted successfully');
    }
}
