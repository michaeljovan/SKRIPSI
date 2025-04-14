<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\ModelNotFoundException;


class SuperAdminController extends Controller
{
    // Show form to create a new user
    public function createUserForm()
    {
        $users = User::latest()->get();
        return view('superadmin', compact('users')); // Pointing to superadmin.blade.php
    }

    // Handle the form submission to create the user
    public function storeUser(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return redirect()->back()->with('success', 'User created successfully!');
    }
    
    public function changePassword(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'new_password' => 'required|string|min:6|confirmed',
        ]);
    
        try {
            $user = User::findOrFail($request->user_id);
            $user->password = Hash::make($request->new_password);
            $user->save();
    
            return response()->json([
                'success' => true,
                'message' => 'Password berhasil diubah!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengubah password: ' . $e->getMessage()
            ], 500);
        }
    }

    public function deleteUser($userId)
    {
        try {
            $user = User::findOrFail($userId);
            
            // Prevent deleting yourself
            if ($user->id === auth()->id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak dapat menghapus akun sendiri!'
                ], 403);
            }
    
            $user->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'User berhasil dihapus!'
            ]);
            
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'User tidak ditemukan!'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus user: ' . $e->getMessage()
            ], 500);
        }
    }
}