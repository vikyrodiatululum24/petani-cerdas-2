<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Http\Requests\User\UserIndexRequest;
use App\Http\Resources\User\UserResource;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index(UserIndexRequest $request): Response
    {
        $filters = $request->validated();
        $perPage = (int) ($filters['per_page'] ?? 10);

        $users = User::query()
            ->with('roles:id,name')
            ->when(
                ! empty($filters['search']),
                fn ($query) => $query->where(function ($inner) use ($filters): void {
                    $search = (string) $filters['search'];
                    $inner->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                })
            )
            ->when(
                ! empty($filters['role']),
                fn ($query) => $query->role($filters['role'])
            )
            ->latest()
            ->paginate($perPage)
            ->withQueryString();

        return Inertia::render('users/Index', [
            'users' => UserResource::collection($users->getCollection())->resolve(),
            'pagination' => [
                'current_page' => $users->currentPage(),
                'last_page' => $users->lastPage(),
                'per_page' => $users->perPage(),
                'total' => $users->total(),
                'from' => $users->firstItem(),
                'to' => $users->lastItem(),
                'links' => $users->linkCollection()->toArray(),
            ],
            'roles' => Role::query()->orderBy('name')->pluck('name')->values(),
            'filters' => [
                'search' => $filters['search'] ?? '',
                'role' => $filters['role'] ?? null,
                'per_page' => $perPage,
            ],
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('users/Create', [
            'roles' => Role::query()->orderBy('name')->pluck('name')->values(),
        ]);
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $role = $data['role'];
        unset($data['role']);

        $user = User::create($data);
        $user->syncRoles([$role]);

        return redirect()->route('users.index')->with('success', 'Pengguna berhasil dibuat.');
    }

    public function show(User $user): Response
    {
        return Inertia::render('users/Show', [
            'user' => UserResource::make($user->load('roles:id,name'))->resolve(),
        ]);
    }

    public function edit(User $user): Response
    {
        return Inertia::render('users/Edit', [
            'user' => UserResource::make($user->load('roles:id,name'))->resolve(),
            'roles' => Role::query()->orderBy('name')->pluck('name')->values(),
        ]);
    }

    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $data = $request->validated();
        $role = $data['role'];
        unset($data['role']);

        if (empty($data['password'])) {
            unset($data['password']);
        }

        $user->update($data);
        $user->syncRoles([$role]);

        return redirect()->route('users.index')->with('success', 'Pengguna berhasil diperbarui.');
    }

    public function destroy(User $user): RedirectResponse
    {
        if (Auth::id() === $user->id) {
            return back()->withErrors([
                'user' => 'Anda tidak dapat menghapus akun Anda sendiri.',
            ]);
        }

        $user->delete();

        return redirect()->route('users.index')->with('success', 'Pengguna berhasil dihapus.');
    }
}
