<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('ceo');
    }

    public function index()
    {
        $users = User::orderBy('created_at', 'desc')->paginate(15);
        return view('users.index', compact('users'));
    }

    public function create()
    {
        return view('users.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:technician,reception,supply,ceo',
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
        ]);

        return redirect()->route('users.index')
            ->with('success', 'کاربر با موفقیت ایجاد شد.');
    }

    public function show(User $user)
    {
        $user->load(['reports', 'partOrders', 'workRequests']);
        return view('users.show', compact('user'));
    }

    public function edit(User $user)
    {
        return view('users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'role' => 'required|in:technician,reception,supply,ceo',
        ]);

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'] ? Hash::make($validated['password']) : $user->password,
            'role' => $validated['role'],
        ]);

        return redirect()->route('users.index')
            ->with('success', 'کاربر با موفقیت بروزرسانی شد.');
    }

    public function destroy(User $user)
    {
        // نمیشه خودت رو حذف کنی
        if ($user->id === auth()->id()) {
            return redirect()->route('users.index')
                ->with('error', 'شما نمی‌توانید خودتان را حذف کنید.');
        }

        $user->delete();

        return redirect()->route('users.index')
            ->with('success', 'کاربر با موفقیت حذف شد.');
    }
}
