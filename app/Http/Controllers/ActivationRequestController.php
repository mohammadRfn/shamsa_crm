<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ActivationRequestController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'request_uuid'    => 'required|string|max:64',
            'raw_fingerprint' => 'required|string|max:255',
            'customer_note'   => 'nullable|string|max:255',
        ]);

        $exists = DB::table('activation_requests')
            ->where('request_uuid', $request->request_uuid)
            ->exists();

        if (!$exists) {
            DB::table('activation_requests')->insert([
                'request_uuid'    => $request->request_uuid,
                'raw_fingerprint' => $request->raw_fingerprint,
                'customer_note'   => $request->customer_note,
                'status'          => 'pending',
                'created_at'      => now(),
            ]);
        }

        return response()->json(['ok' => true]);
    }

    public function status(string $uuid)
    {
        $row = DB::table('activation_requests')->where('request_uuid', $uuid)->first();

        if (!$row) {
            return response()->json(['status' => 'unknown']);
        }

        return response()->json(['status' => $row->status]);
    }
}