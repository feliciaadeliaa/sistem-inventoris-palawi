<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $users = User::query()
            ->when($request->filled('q'), function ($q) use ($request) {
                $search = $request->q;
                $q->where(function ($sub) use ($search) {
                    $sub->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            // ?status=pending -> sudah verifikasi email, menunggu persetujuan admin
            ->when($request->status === 'pending', function ($q) {
                $q->where('is_active', false)->whereNotNull('email_verified_at');
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $pendingCount = User::where('is_active', false)
            ->whereNotNull('email_verified_at')
            ->count();

        return view('admin.users.index', compact('users', 'pendingCount'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    /**
     * Admin menambah user. Akun langsung dianggap terverifikasi
     * karena admin yang menjamin keabsahannya.
     */
    public function store(Request $request)
    {
        $request->merge([
            'name'  => trim($request->name),
            'email' => strtolower(trim($request->email)),
        ]);

        $emailRule = app()->isProduction() ? 'email:rfc,dns' : 'email';

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', $emailRule, 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', 'in:admin,user,senior_analis,gm'],
        ]);

        $user = new User([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'], // di-hash oleh cast 'hashed'
            'role' => $validated['role'],
            'is_active' => true,
        ]);

        // email_verified_at tidak ada di $fillable (sengaja), jadi diisi langsung.
        $user->email_verified_at = now();
        $user->save();

        return redirect()->route('admin.users.index')->with('success', 'User berhasil ditambahkan.');
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $request->merge([
            'name'  => trim($request->name),
            'email' => strtolower(trim($request->email)),
        ]);

        $emailRule = app()->isProduction() ? 'email:rfc,dns' : 'email';

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', $emailRule, 'max:255', 'unique:users,email,' . $user->id],
            'role' => ['required', 'in:admin,user,senior_analis,gm'],
        ]);

        // Admin tidak boleh menurunkan role dirinya sendiri (mencegah terkunci)
        if ($user->is($request->user())) {
            $validated['role'] = $user->role;
        }

        $user->update($validated);

        return redirect()->route('admin.users.index')->with('success', 'User berhasil diupdate.');
    }

    public function toggleActive(Request $request, User $user)
    {
        // Admin tidak boleh menonaktifkan akunnya sendiri
        if ($user->is($request->user())) {
            return back()->with('error', 'Anda tidak bisa menonaktifkan akun sendiri.');
        }

        $user->update(['is_active' => ! $user->is_active]);

        return back()->with('success', 'Status user berhasil diubah.');
    }
}