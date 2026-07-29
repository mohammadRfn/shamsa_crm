<?php

namespace App\Http\Controllers;

use App\Services\ActivationClientService;
use App\Services\FingerprintService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class ActivationController extends Controller
{
    public function showForm()
    {
        return view('activation.request');
    }

    public function requestActivation(FingerprintService $fp, ActivationClientService $client)
    {
        $rawFingerprint = $fp->generate();

        $uuid = $client->sendFingerprint($rawFingerprint);

        if (!$uuid) {
            return back()->with('error', 'ارتباط با سرور برقرار نشد. لطفاً با پشتیبانی هماهنگ کنید.');
        }

        Session::put('activation_request_uuid', $uuid);

        return redirect()->route('license.waiting');
    }

    public function waiting()
    {
        if (!Session::has('activation_request_uuid')) {
            return redirect()->route('license.activate.form');
        }

        return view('activation.waiting');
    }

    public function pollStatus(ActivationClientService $client)
    {
        $uuid = Session::get('activation_request_uuid');

        if (!$uuid) {
            return response()->json(['status' => 'unknown']);
        }

        $result = $client->checkStatus($uuid);

        return response()->json($result);
    }

    public function showEnterLicense()
    {
        $uuid = Session::get('activation_request_uuid');

        // این مرحله فقط وقتی UI باز میشه که poll جواب approved داده باشه (چک فرانت)
        // ولی چک واقعی، فقط verify امضا هست که در submitLicense اتفاق می‌افته
        return view('activation.enter-license');
    }

    public function submitLicense(Request $request, FingerprintService $fp, \App\Services\LicenseVerifyService $verifier)
    {
        $request->validate(['license_key' => 'required|string']);

        $fingerprint = $fp->generate();
        $payload = $verifier->verify($request->license_key, $fingerprint);

        if (!$payload) {
            return back()->with('error', 'لایسنس نامعتبر است یا با این سیستم مطابقت ندارد.');
        }

        DB::table('licenses')->insert([
            'machine_fingerprint'     => $fingerprint,
            'license_key'             => $request->license_key,
            'activation_request_uuid' => Session::get('activation_request_uuid'),
            'payload_json'            => json_encode($payload),
            'activated_at'            => now(),
            'created_at'              => now(),
        ]);

        Session::forget('activation_request_uuid');

        return redirect()->route('dashboard')->with('success', 'اپ با موفقیت فعال شد.');
    }
}
