<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;

class UserController extends Controller
{
    public function index()
    {
        $users = User::withCount('pengaduan')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(fn($u) => [
                'id_user'         => $u->id_user,
                'nama'            => $u->nama,
                'email'           => $u->email,
                'role'            => $u->role,
                'kelas'           => $u->kelas,
                'created_at'      => $u->created_at,
                'pengaduan_count' => $u->pengaduan_count,
            ]);

        return inertia('Admin/manajemen-user', [
            'users' => $users,
        ]);
    }
}