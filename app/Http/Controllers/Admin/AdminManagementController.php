<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AdminManagementController extends Controller
{
    /**
     * Display a listing of admin accounts.
     */
    public function index(Request $request): View
    {
        $query = User::where('role', 'admin');

        if ($request->filled('q')) {
            $search = '%'.strtolower($request->query('q')).'%';
            $query->where(function ($q) use ($search) {
                $q->whereRaw('LOWER(name) LIKE ?', [$search])
                    ->orWhereRaw('LOWER(email) LIKE ?', [$search]);
            });
        }

        if ($request->filled('level') && in_array($request->query('level'), ['super_admin', 'library_admin', 'article_editor'])) {
            $query->where('admin_level', $request->query('level'));
        }

        $admins = $query->latest('id')->paginate(10)->withQueryString();

        $stats = [
            'total' => User::where('role', 'admin')->count(),
            'super_admin' => User::where('role', 'admin')->where('admin_level', 'super_admin')->count(),
            'library_admin' => User::where('role', 'admin')->where('admin_level', 'library_admin')->count(),
            'article_editor' => User::where('role', 'admin')->where('admin_level', 'article_editor')->count(),
        ];

        return view('admin.admins.index', compact('admins', 'stats'));
    }

    /**
     * Store a newly created admin account.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:8',
            'admin_level' => 'required|in:super_admin,library_admin,article_editor',
        ], [
            'name.required' => 'Nama admin wajib diisi.',
            'email.required' => 'Alamat email wajib diisi.',
            'email.unique' => 'Alamat email ini sudah terdaftar.',
            'password.required' => 'Kata sandi wajib diisi minimal 8 karakter.',
            'password.min' => 'Kata sandi minimal berisi 8 karakter.',
            'admin_level.required' => 'Tingkat hak akses admin wajib dipilih.',
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'admin',
            'admin_level' => $validated['admin_level'],
            'membership_status' => 'approved',
        ]);

        return redirect()->route('admin.admins.index')
            ->with('success', "Akun pengelola baru ({$validated['name']}) berhasil ditambahkan!");
    }

    /**
     * Update the specified admin account.
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        $admin = User::where('role', 'admin')->findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($admin->id)],
            'password' => 'nullable|string|min:8',
            'admin_level' => 'required|in:super_admin,library_admin,article_editor',
        ], [
            'name.required' => 'Nama admin wajib diisi.',
            'email.required' => 'Alamat email wajib diisi.',
            'email.unique' => 'Alamat email ini sudah dipakai.',
            'password.min' => 'Kata sandi baru minimal berisi 8 karakter.',
            'admin_level.required' => 'Tingkat hak akses admin wajib dipilih.',
        ]);

        $updateData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'admin_level' => $validated['admin_level'],
        ];

        if (!empty($validated['password'])) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        $admin->update($updateData);

        return redirect()->route('admin.admins.index')
            ->with('success', "Data pengelola ({$admin->name}) berhasil diperbarui!");
    }

    /**
     * Remove the specified admin account.
     */
    public function destroy(int $id): RedirectResponse
    {
        $admin = User::where('role', 'admin')->findOrFail($id);

        if ($admin->id === Auth::id()) {
            return redirect()->route('admin.admins.index')
                ->with('error', 'Anda tidak dapat menghapus akun Anda sendiri saat sedang masuk.');
        }

        if ($admin->email === 'admin@cakrawala.id') {
            return redirect()->route('admin.admins.index')
                ->with('error', 'Akun Administrator Utama tidak dapat dihapus demi keamanan sistem.');
        }

        $name = $admin->name;
        $admin->delete();

        return redirect()->route('admin.admins.index')
            ->with('success', "Akun pengelola {$name} berhasil dihapus.");
    }
}
