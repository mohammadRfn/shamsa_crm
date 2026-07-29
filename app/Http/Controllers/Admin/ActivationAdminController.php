<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class ActivationAdminController extends Controller
{
    public function index()
    {
        $requests = DB::table('activation_requests')->orderByDesc('id')->get();
        return view('admin.activation-requests', compact('requests'));
    }

    public function approve($id)
    {
        DB::table('activation_requests')->where('id', $id)->update([
            'status'      => 'approved',
            'reviewed_at' => now(),
        ]);

        return back()->with('success', 'درخواست تایید شد.');
    }

    public function reject($id)
    {
        DB::table('activation_requests')->where('id', $id)->update([
            'status'      => 'rejected',
            'reviewed_at' => now(),
        ]);

        return back()->with('success', 'درخواست رد شد.');
    }
}