<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class UserController extends Controller
{
    /**
     * Constructor - فقط CEO دسترسی داره
     */
    // public function __construct()
    // {
    // }

    /**
     * نمایش لیست کاربران
     */
    public function index()
    {
        $users = User::latest()->paginate(15);
        return view('users.index', compact('users'));
    }

    /**
     * فرم ایجاد کاربر جدید
     */
    public function create()
    {
        return view('users.create');
    }

    /**
     * ذخیره کاربر جدید
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', 'in:technician,reception,supply,ceo'],
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

    /**
     * نمایش جزئیات کاربر
     */
    public function show(User $user)
    {
        return view('users.show', compact('user'));
    }

    /**
     * فرم ویرایش کاربر
     */
    public function edit(User $user)
    {
        return view('users.edit', compact('user'));
    }

    /**
     * آپدیت کاربر
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', 'in:technician,reception,supply,ceo'],
        ]);

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
        ]);

        // فقط اگر پسورد وارد شده باشه آپدیت کن
        if ($request->filled('password')) {
            $user->update([
                'password' => Hash::make($validated['password']),
            ]);
        }

        return redirect()->route('users.index')
            ->with('success', 'کاربر با موفقیت بروزرسانی شد.');
    }

    /**
     * حذف کاربر
     */
    public function destroy(User $user)
    {
        // جلوگیری از حذف خودش
        if ($user->id === auth()->id()) {
            return back()->with('error', 'شما نمی‌توانید خودتان را حذف کنید.');
        }

        $user->delete();

        return redirect()->route('users.index')
            ->with('success', 'کاربر با موفقیت حذف شد.');
    }
}
