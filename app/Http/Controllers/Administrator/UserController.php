<?php

namespace App\Http\Controllers\Administrator;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\VerifyEmailWithCode;
use App\Helpers\ActivityLogHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $role = $request->query('role');
        $search = $request->query('search');

        $users = User::when($role, fn ($query) => $query->where('role', $role))
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'ilike', "%{$search}%")
                    ->orWhere('email', 'ilike', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(12)
            ->withQueryString();

        $roleCounts = [
            'all'           => User::count(),
            'user'          => User::where('role', 'user')->count(),
            'student'       => User::where('role', 'student')->count(),
            'instructor'    => User::where('role', 'instructor')->count(),
            'administrator' => User::where('role', 'administrator')->count(),
        ];

        return view('administrator.users.index', compact('users', 'roleCounts', 'role'));
    }

    public function create()
    {
        return view('administrator.users.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'role'     => ['required', Rule::in(['student', 'instructor'])], 
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = User::create([
            'name'              => $validated['name'],
            'email'             => $validated['email'],
            'role'              => $validated['role'],
            'password'          => Hash::make($validated['password']),
            'email_verified_at' => now(),
        ]);

        ActivityLogHelper::log('created', 'user', "created account '{$user->name}' ({$user->role})");

        return redirect()->route('administrator.users.index')
            ->with('success', 'Account created successfully.');
    }

    public function show(User $user)
    {
        return view('administrator.users.show', compact('user'));
    }

    public function edit(User $user)
    {
       // Prevent editing another Administrator
        if ($user->isAdministrator() && !$user->is(auth()->user())) {
            return back()->with('error', 'You cannot edit another Administrator account.');
        }

        return view('administrator.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        if ($user->isAdministrator() && !$user->is(auth()->user())) {
            return back()->with('error', 'You cannot edit another Administrator account.');
        }

        $validated = $request->validate([
            'name'  => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'role'  => ['nullable', Rule::in(['student', 'instructor'])], 
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        $updateData = [
            'name'  => $validated['name'],
            'email' => $validated['email'],
        ];

        // Only update role if provided (not for Administrators)
        if (isset($validated['role'])) {
            $updateData['role'] = $validated['role'];
        }

        if ($validated['password']) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        $user->update($updateData);

        return redirect()->route('administrator.users.index')
            ->with('success', 'Account updated successfully.');
    }

   public function updateRole(Request $request, User $user)
    {
        $validated = $request->validate([
            'role' => ['required', Rule::in(['student', 'instructor', 'administrator'])],
        ]);

        // Protect the original Administrator (env account) from being demoted
        $originalFaculty = User::where('role', 'administrator')->oldest()->first();
        if ($user->is($originalFaculty) && $validated['role'] !== 'administrator') {
            return back()->with('error', 'The original Administrator account cannot be demoted.');
        }

        if ($user->is(auth()->user()) && $validated['role'] !== 'administrator') {
            return back()->with('error', 'You cannot remove your own Administrator access.');
        }

        $user->update(['role' => $validated['role']]);
        ActivityLogHelper::log('changed', 'user', "changed {$user->name}'s role to " . ($validated['role'] === 'instructor' ? 'Instructor' : ($validated['role'] === 'administrator' ? 'Administrator' : 'Student')));

        return back()->with('success', 'User role updated successfully.');
    }

    public function destroy(User $user)
    {
        if ($user->is(auth()->user())) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        if ($user->isAdministrator()) {
            return back()->with('error', 'Administrator accounts cannot be removed from this screen.');
        }

        $user->delete();
        ActivityLogHelper::log('deleted', 'user', "deleted account '{$user->name}'");

        return back()->with('success', 'Account removed successfully.');
    }
    public function warn(Request $request, User $user)
        {
            if ($user->isAdministrator()) {
                return back()->with('error', 'You cannot warn an Administrator account.');
            }

            $request->validate(['reason' => 'required|string|max:255']);
            
            $user->addWarning($request->reason);
            
            ActivityLogHelper::log('warned', 'user', "warned {$user->name}: {$request->reason}");
            
            $msg = $user->isBanned() 
                ? 'User has been warned and auto-banned (3 warnings).' 
                : 'Warning issued. User now has ' . $user->warning_count . ' warning(s).';
            
            return back()->with($user->isBanned() ? 'error' : 'success', $msg);
    }

    public function ban(Request $request, User $user)
    {
        if ($user->isAdministrator()) {
            return back()->with('error', 'You cannot ban an Administrator account.');
        }

        $request->validate([
            'reason' => 'required|string|max:255',
            'ban_until' => 'nullable|date',
        ]);
        
        $user->ban($request->reason, $request->ban_until);
        
        ActivityLogHelper::log('banned', 'user', "banned {$user->name}: {$request->reason}");
        
        return back()->with('success', 'User has been banned.');
    }

    public function unban(User $user)
    {
        $user->unban();
        
        ActivityLogHelper::log('unbanned', 'user', "unbanned {$user->name}");
        
        return back()->with('success', 'User has been unbanned.');
    }
}