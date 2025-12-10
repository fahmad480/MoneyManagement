<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;
use Illuminate\Validation\ValidationException;
use Exception;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with('roles');
        
        // Search
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%')
                  ->orWhere('phone', 'like', '%' . $request->search . '%');
            });
        }
        
        // Filter by role
        if ($request->filled('role')) {
            $query->whereHas('roles', function($q) use ($request) {
                $q->where('name', $request->role);
            });
        }
        
        // Filter by email verification status
        if ($request->filled('verified')) {
            if ($request->verified === 'yes') {
                $query->whereNotNull('email_verified_at');
            } elseif ($request->verified === 'no') {
                $query->whereNull('email_verified_at');
            }
        }
        
        // Sort
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);
        
        // Pagination
        $perPage = $request->get('per_page', 15);
        $users = $query->paginate($perPage)->appends($request->all());
        
        $roles = \Spatie\Permission\Models\Role::all();
        
        return view('admin.users.index', compact('users', 'roles'));
    }

    public function create()
    {
        $roles = Role::all();
        return view('admin.users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|min:8|confirmed',
                'phone' => 'nullable|string|max:20',
                'address' => 'nullable|string',
                'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
                'role' => 'required|exists:roles,name',
            ]);

            $userData = [
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'phone' => $validated['phone'] ?? null,
                'address' => $validated['address'] ?? null,
            ];

            if ($request->hasFile('profile_photo')) {
                $userData['profile_photo'] = $request->file('profile_photo')->store('profile-photos', 'public');
            }

            $user = User::create($userData);
            $user->assignRole($validated['role']);

            return redirect()->route('admin.users.index')
                ->with('success', 'User berhasil ditambahkan!');
                
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }

    public function edit($id)
    {
        try {
            $user = User::with('roles')->findOrFail($id);
            $roles = Role::all();
            return view('admin.users.edit', compact('user', 'roles'));
        } catch (Exception $e) {
            return redirect()->route('admin.users.index')->with('error', 'User tidak ditemukan!');
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $user = User::findOrFail($id);
            
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email,' . $id,
                'password' => 'nullable|min:8|confirmed',
                'phone' => 'nullable|string|max:20',
                'address' => 'nullable|string',
                'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
                'role' => 'required|exists:roles,name',
                'email_verification_status' => 'nullable|in:verified,unverified',
            ]);

            $userData = [
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'] ?? null,
                'address' => $validated['address'] ?? null,
            ];

            if ($request->filled('password')) {
                $userData['password'] = Hash::make($validated['password']);
            }

            // Handle email verification status
            if ($request->filled('email_verification_status')) {
                if ($validated['email_verification_status'] === 'verified') {
                    $userData['email_verified_at'] = $user->email_verified_at ?? now();
                } else {
                    $userData['email_verified_at'] = null;
                }
            }

            if ($request->hasFile('profile_photo')) {
                if ($user->profile_photo) {
                    Storage::disk('public')->delete($user->profile_photo);
                }
                $userData['profile_photo'] = $request->file('profile_photo')->store('profile-photos', 'public');
            }

            $user->update($userData);
            $user->syncRoles([$validated['role']]);

            return redirect()->route('admin.users.index')
                ->with('success', 'User berhasil diupdate!');
                
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy($id)
    {
        try {
            $user = User::findOrFail($id);
            
            /** @var \App\Models\User $authUser */
            $authUser = Auth::user();
            
            if ($user->id === $authUser->id) {
                return back()->with('error', 'Tidak bisa menghapus user sendiri!');
            }
            
            if ($user->profile_photo) {
                Storage::disk('public')->delete($user->profile_photo);
            }
            
            $user->delete();

            return redirect()->route('admin.users.index')
                ->with('success', 'User berhasil dihapus!');
        } catch (Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function toggleEmailVerification($id)
    {
        try {
            /** @var \App\Models\User $user */
            $user = User::findOrFail($id);
            
            /** @var \App\Models\User $authUser */
            $authUser = Auth::user();
            
            if ($user->id === $authUser->id) {
                return back()->with('error', 'Tidak bisa mengubah status verifikasi email sendiri!');
            }
            
            // Toggle verification status
            if (is_null($user->email_verified_at)) {
                $user->email_verified_at = now();
                $message = 'Email user berhasil diverifikasi!';
            } else {
                $user->email_verified_at = null;
                $message = 'Verifikasi email user berhasil dibatalkan!';
            }
            
            $user->save();

            return back()->with('success', $message);
        } catch (Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
