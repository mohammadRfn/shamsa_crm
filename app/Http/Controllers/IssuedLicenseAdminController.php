<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class IssuedLicenseAdminController extends Controller
{
    public function index()
    {
        $licenses = DB::table('issued_licenses')->orderByDesc('id')->get();
        return view('admin.licenses', compact('licenses'));
    }

    public function revoke($id)
    {
        DB::table('issued_licenses')->where('id', $id)->update(['revoked' => 1]);
        return back()->with('success', 'لایسنس با موفقیت غیرفعال شد.');
    }

    public function reactivate($id)
    {
        DB::table('issued_licenses')->where('id', $id)->update(['revoked' => 0]);
        return back()->with('success', 'لایسنس با موفقیت فعال‌سازی مجدد شد.');
    }
}
