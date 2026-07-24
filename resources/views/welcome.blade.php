<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="rtl">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'شمسا') }} | سامانه جامع مدیریت تدارکات و گردش کار</title>

    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif

    <style>
        :root {
            --gold-1: #f5d06e;
            --gold-2: #c99733;
            --gold-3: #8a6a1e;
            --rose-1: #F27090;
            --rose-2: #E8476A;
            --rose-3: #A8224A;
            --ink: #1C1A18;
            --muted: #6B6660;
            --line: #E8E6E3;
            --cream: #F5F4F2;
            --panel: #FFFFFF;
        }

        * {
            box-sizing: border-box;
        }

        html,
        body {
            height: 100%;
        }

        body.shamsa-welcome {
            margin: 0;
            min-height: 100dvh;
            background:
                radial-gradient(ellipse 90% 60% at 50% 0%, #FFF6E6 0%, transparent 55%),
                radial-gradient(ellipse 80% 50% at 100% 100%, #FFF1F4 0%, transparent 55%),
                var(--cream);
            color: var(--ink);
            font-family: 'Vazirmatn', system-ui, sans-serif;
            overflow: hidden;
            position: relative;
            display: flex;
            flex-direction: column;
        }

        .circuit-texture {
            position: fixed;
            inset: 0;
            pointer-events: none;
            opacity: .05;
            background-image:
                linear-gradient(var(--line) 1px, transparent 1px),
                linear-gradient(90deg, var(--line) 1px, transparent 1px);
            background-size: 56px 56px;
            mask-image: radial-gradient(ellipse 75% 55% at 50% 20%, black 0%, transparent 75%);
        }

        .shamsa-shell {
            position: relative;
            z-index: 1;
            flex: 1;
            width: 100%;
            max-width: 980px;
            margin: 0 auto;
            padding: clamp(10px, 2.2dvh, 22px) 22px clamp(10px, 2dvh, 20px);
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
        }

        .shamsa-topbar {
            width: 100%;
            display: flex;
            justify-content: flex-start;
            margin-bottom: clamp(6px, 1.4dvh, 14px);
        }

        .shamsa-login-link {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 7px 16px;
            border-radius: 9999px;
            font-size: 12px;
            font-weight: 600;
            color: var(--muted);
            border: 1.5px solid var(--line);
            background: var(--panel);
            transition: all .2s ease;
            text-decoration: none;
        }

        .shamsa-login-link:hover {
            border-color: var(--rose-1);
            color: var(--rose-2);
            box-shadow: 0 3px 10px rgba(232, 71, 106, .12);
        }

        /* ── لوگو در قاب تیره ── */
        .logo-frame {
            width: clamp(150px, 20dvh, 210px);
            margin-bottom: clamp(10px, 1.8dvh, 18px);
        }

        .logo-frame img {
            width: 100%;
            height: auto;
            display: block;
            filter: drop-shadow(0 2px 10px rgba(28, 26, 24, .15));
        }

        .brand-eyebrow {
            font-size: 11.5px;
            font-weight: 700;
            letter-spacing: .14em;
            color: var(--gold-3);
            text-transform: uppercase;
        }

        .brand-rule {
            width: 56px;
            height: 2px;
            margin: clamp(8px, 1.2dvh, 12px) auto;
            background: linear-gradient(90deg, transparent, var(--gold-2), transparent);
            position: relative;
        }

        .brand-rule::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: var(--rose-2);
            box-shadow: 0 0 8px rgba(232, 71, 106, .6);
        }

        .brand-title {
            margin: 0 0 clamp(6px, 1dvh, 10px);
            font-size: clamp(20px, 3.2dvh, 32px);
            font-weight: 800;
            line-height: 1.4;
            background: linear-gradient(90deg, var(--gold-3) 0%, var(--gold-2) 35%, var(--rose-2) 75%, var(--rose-3) 100%);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }

        .brand-subtitle {
            max-width: 520px;
            margin: 0 auto clamp(14px, 2.4dvh, 26px);
            font-size: clamp(12px, 1.6dvh, 13.5px);
            line-height: 1.9;
            color: var(--muted);
        }

        /* ── مدار گردش کار (منظم، افقی، بدون تداخل) ── */
        .flow-hub {
            width: 100%;
            max-width: 820px;
            margin: 0 auto clamp(14px, 2.4dvh, 26px);
        }

        .flow-hub svg {
            width: 100%;
            height: auto;
            display: block;
            overflow: visible;
        }

        .flow-line {
            fill: none;
            stroke: var(--line);
            stroke-width: 1.5;
        }

        .flow-line-active {
            stroke: url(#flowGrad);
            stroke-width: 2;
            stroke-dasharray: 5 130;
            animation: flow-travel 3.6s linear infinite;
        }

        .flow-hub .l1 {
            animation-delay: 0s;
        }

        .flow-hub .l2 {
            animation-delay: .3s;
        }

        .flow-hub .l3 {
            animation-delay: .6s;
        }

        .flow-hub .l4 {
            animation-delay: .9s;
        }

        .flow-hub .l5 {
            animation-delay: 1.2s;
        }

        .flow-hub .l6 {
            animation-delay: 1.5s;
        }

        @keyframes flow-travel {
            to {
                stroke-dashoffset: -135;
            }
        }

        .flow-node-ring {
            fill: var(--panel);
            stroke: var(--gold-2);
            stroke-width: 1.5;
        }

        .flow-node-dot {
            fill: var(--gold-2);
            animation: node-pulse 2.4s ease-in-out infinite;
        }

        .flow-hub .n1 .flow-node-dot {
            animation-delay: 0s;
        }

        .flow-hub .n2 .flow-node-dot {
            animation-delay: .3s;
        }

        .flow-hub .n3 .flow-node-dot {
            animation-delay: .6s;
        }

        .flow-hub .n4 .flow-node-dot {
            animation-delay: .9s;
        }

        .flow-hub .n5 .flow-node-dot {
            animation-delay: 1.2s;
        }

        .flow-hub .n6 .flow-node-dot {
            animation-delay: 1.5s;
        }

        @keyframes node-pulse {

            0%,
            100% {
                opacity: 1;
                transform: scale(1);
            }

            50% {
                opacity: .4;
                transform: scale(.75);
            }
        }

        .flow-label {
            font-family: 'Vazirmatn', sans-serif;
            font-size: 12px;
            font-weight: 700;
            fill: var(--ink);
        }

        .flow-label-num {
            font-family: 'Vazirmatn', sans-serif;
            font-size: 9.5px;
            font-weight: 700;
            fill: var(--gold-3);
        }

        .flow-hub-core {
            fill: var(--rose-2);
            filter: drop-shadow(0 0 6px rgba(232, 71, 106, .5));
        }

        .flow-hub-label {
            font-family: 'Vazirmatn', sans-serif;
            font-size: 12.5px;
            font-weight: 800;
            fill: var(--rose-3);
        }

        /* ── دکمه‌ها ── */
        .shamsa-actions {
            display: flex;
            gap: 12px;
            justify-content: center;
            margin-bottom: clamp(14px, 2.4dvh, 26px);
        }

        .btn-hub-primary {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 11px 30px;
            border-radius: 12px;
            font-weight: 700;
            font-size: 13.5px;
            color: #fff;
            background: linear-gradient(135deg, var(--rose-1) 0%, var(--rose-3) 100%);
            box-shadow: 0 4px 16px rgba(232, 71, 106, .28);
            text-decoration: none;
            border: 1px solid rgba(168, 34, 74, .3);
            transition: all .2s ease;
        }

        .btn-hub-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(232, 71, 106, .36);
        }

        /* ── نوار امکانات ── */
        .shamsa-strip {
            width: 100%;
            max-width: 820px;
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1px;
            background: var(--line);
            border: 1px solid var(--line);
            border-radius: 14px;
            overflow: hidden;
            margin: 0 auto;
        }

        .shamsa-strip-item {
            background: var(--panel);
            padding: clamp(10px, 1.6dvh, 16px) 14px;
        }

        .shamsa-strip-item .num {
            font-size: 10.5px;
            font-weight: 700;
            color: var(--gold-2);
            letter-spacing: .06em;
            display: block;
            margin-bottom: 5px;
        }

        .shamsa-strip-item .txt {
            font-size: 11.5px;
            color: var(--muted);
            line-height: 1.75;
        }

        .shamsa-footer {
            margin-top: clamp(10px, 1.8dvh, 18px);
            font-size: 10.5px;
            color: #A8A296;
            letter-spacing: .03em;
        }

        .shamsa-footer b {
            color: var(--muted);
        }

        @media (max-height:700px) {
            .shamsa-strip {
                display: none;
            }
        }

        @media (max-width:640px) {
            .shamsa-strip {
                grid-template-columns: 1fr;
            }
        }

        @media (prefers-reduced-motion: reduce) {

            .flow-line-active,
            .flow-node-dot {
                animation: none;
            }
        }
    </style>
</head>

<body class="shamsa-welcome">
    <div class="circuit-texture"></div>

    <div class="shamsa-shell">

        <div class="shamsa-topbar">
            @if (Route::has('login'))
            @auth
            <a href="{{ url('/dashboard') }}" class="shamsa-login-link">ورود به داشبورد</a>
            @else
            <a href="{{ route('login') }}" class="shamsa-login-link">ورود به اکانت</a>
            @endauth
            @endif
        </div>

        <div class="logo-frame">
            <img src="{{ asset('images/shamsa-logo.png') }}" alt="گروه مهندسی شمسا">
        </div>

        <span class="brand-eyebrow">Shamsa Engineering Group</span>
        <div class="brand-rule"></div>

        <h1 class="brand-title">سامانه جامع مدیریت تدارکات و گردش کار شمسا</h1>
        <p class="brand-subtitle">
            از ثبت درخواست تا تحویل نهایی، گردش کار، سفارش قطعات، گزارش‌های فنی
            و پیشنهادهای تامین در یک بستر یکپارچه مدیریت می‌شوند.
        </p>

        {{-- عنصر امضادار: مدار افقی و مرتب شش‌مرحله گردش کار --}}
        <div class="flow-hub">
            <svg viewBox="0 0 820 140" xmlns="http://www.w3.org/2000/svg">
                <defs>
                    <linearGradient id="flowGrad" x1="0" y1="0" x2="0" y2="1">
                        <stop offset="0%" stop-color="#f5d06e" />
                        <stop offset="100%" stop-color="#E8476A" />
                    </linearGradient>
                </defs>

                {{-- ترتیب راست‌به‌چپ مطابق خوانش فارسی: پذیرش (راست) تا مالی (چپ) --}}
                {{-- خطوط اتصال هر گره به هاب مرکزی پایین --}}
                <path class="flow-line flow-line-active l1" d="M 760 40 C 700 90, 470 100, 410 108" />
                <path class="flow-line flow-line-active l2" d="M 608 40 C 580 85, 460 100, 410 108" />
                <path class="flow-line flow-line-active l3" d="M 456 40 C 445 75, 415 95, 410 108" />
                <path class="flow-line flow-line-active l4" d="M 364 40 C 375 75, 405 95, 410 108" />
                <path class="flow-line flow-line-active l5" d="M 212 40 C 240 85, 360 100, 410 108" />
                <path class="flow-line flow-line-active l6" d="M 60  40 C 120 90, 350 100, 410 108" />

                {{-- هاب مرکزی --}}
                <circle class="flow-hub-core" cx="410" cy="108" r="6" />
                <text x="410" y="130" text-anchor="middle" class="flow-hub-label">شمسا</text>

                {{-- گره ۱: پذیرش --}}
                <g class="n1">
                    <text x="760" y="10" text-anchor="middle" class="flow-label-num">۱</text>
                    <circle class="flow-node-ring" cx="760" cy="40" r="9" />
                    <circle class="flow-node-dot" cx="760" cy="40" r="4" />
                    <text x="760" y="66" text-anchor="middle" class="flow-label">پذیرش</text>
                </g>
                {{-- گره ۲: کارگاه --}}
                <g class="n2">
                    <text x="608" y="10" text-anchor="middle" class="flow-label-num">۲</text>
                    <circle class="flow-node-ring" cx="608" cy="40" r="9" />
                    <circle class="flow-node-dot" cx="608" cy="40" r="4" />
                    <text x="608" y="66" text-anchor="middle" class="flow-label">کارگاه</text>
                </g>
                {{-- گره ۳: برآورد --}}
                <g class="n3">
                    <text x="456" y="10" text-anchor="middle" class="flow-label-num">۳</text>
                    <circle class="flow-node-ring" cx="456" cy="40" r="9" />
                    <circle class="flow-node-dot" cx="456" cy="40" r="4" />
                    <text x="456" y="66" text-anchor="middle" class="flow-label">برآورد</text>
                </g>
                {{-- گره ۴: تایید --}}
                <g class="n4">
                    <text x="364" y="10" text-anchor="middle" class="flow-label-num">۴</text>
                    <circle class="flow-node-ring" cx="364" cy="40" r="9" />
                    <circle class="flow-node-dot" cx="364" cy="40" r="4" />
                    <text x="364" y="66" text-anchor="middle" class="flow-label">تایید</text>
                </g>
                {{-- گره ۵: تحویل --}}
                <g class="n5">
                    <text x="212" y="10" text-anchor="middle" class="flow-label-num">۵</text>
                    <circle class="flow-node-ring" cx="212" cy="40" r="9" />
                    <circle class="flow-node-dot" cx="212" cy="40" r="4" />
                    <text x="212" y="66" text-anchor="middle" class="flow-label">تحویل</text>
                </g>
                {{-- گره ۶: مالی --}}
                <g class="n6">
                    <text x="60" y="10" text-anchor="middle" class="flow-label-num">۶</text>
                    <circle class="flow-node-ring" cx="60" cy="40" r="9" />
                    <circle class="flow-node-dot" cx="60" cy="40" r="4" />
                    <text x="60" y="66" text-anchor="middle" class="flow-label">مالی</text>
                </g>
            </svg>
        </div>

        <div class="shamsa-actions">
            @if (Route::has('login'))
            @auth
            <a href="{{ url('/dashboard') }}" class="btn-hub-primary">ورود به سامانه ←</a>
            @else
            <a href="{{ route('login') }}" class="btn-hub-primary">ورود به سامانه ←</a>
            @endauth
            @endif
        </div>

        <div class="shamsa-strip">
            <div class="shamsa-strip-item">
                <span class="num">گردش کار</span>
                <span class="txt">پیگیری هر درخواست از پذیرش تا تسویه مالی، بدون گم‌شدن یک مرحله</span>
            </div>
            <div class="shamsa-strip-item">
                <span class="num">تدارکات</span>
                <span class="txt">سفارش قطعه و پیشنهاد تامین با تاییدیه نقش‌محور و شفاف</span>
            </div>
            <div class="shamsa-strip-item">
                <span class="num">گزارش‌گیری</span>
                <span class="txt">خروجی اکسل و گزارش کار فنی، آماده برای مدیریت و بایگانی</span>
            </div>
        </div>

        <div class="shamsa-footer">
            © {{ now()->translatedFormat('Y') }} <b>Shamsa Engineering Group</b> — کلیه حقوق محفوظ است
        </div>
    </div>
</body>

</html>