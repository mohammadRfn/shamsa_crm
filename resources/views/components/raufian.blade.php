<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1600 500">
  <defs>
    <!-- گرادیانت پس‌زمینه سمت چپ (تیره و لوکس) -->
    <linearGradient id="leftBg" x1="0%" y1="0%" x2="100%" y2="100%">
      <stop offset="0%" stop-color="#050816"/>
      <stop offset="100%" stop-color="#111827"/>
    </linearGradient>

    <!-- گرادیانت پس‌زمینه سمت راست (بنفشِ ثروتمندانه) -->
    <linearGradient id="rightBg" x1="0%" y1="0%" x2="100%" y2="0%">
      <stop offset="0%" stop-color="#1f2937"/>
      <stop offset="100%" stop-color="#3b0764"/>
    </linearGradient>

    <!-- گرادیانت متن بنفش براق -->
    <linearGradient id="textGradient" x1="0%" y1="0%" x2="100%" y2="100%">
      <stop offset="0%" stop-color="#e9d5ff"/>
      <stop offset="40%" stop-color="#c4b5fd"/>
      <stop offset="100%" stop-color="#a855f7"/>
    </linearGradient>

    <!-- گرادیانت براق روی کل بنر -->
    <linearGradient id="gloss" x1="0%" y1="0%" x2="0%" y2="100%">
      <stop offset="0%" stop-color="#ffffff" stop-opacity="0.45"/>
      <stop offset="50%" stop-color="#ffffff" stop-opacity="0.02"/>
      <stop offset="100%" stop-color="#ffffff" stop-opacity="0"/>
    </linearGradient>

    <!-- سایه نرم برای آیکن و متن -->
    <filter id="softShadow" x="-20%" y="-20%" width="140%" height="140%">
      <feDropShadow dx="0" dy="18" stdDeviation="24" flood-color="#000000" flood-opacity="0.45"/>
    </filter>
  </defs>

  <!-- پس‌زمینه: 50% چپ و 50% راست -->
  <rect x="0" y="0" width="800" height="500" fill="url(#leftBg)"/>
  <rect x="800" y="0" width="800" height="500" fill="url(#rightBg)"/>

  <!-- لایه براق روی کل بنر -->
  <path d="M0,0 L1600,0 L1600,200 Q800,320 0,200 Z" fill="url(#gloss)"/>

  <!-- آیکن کوچک برند CRM (چندضلعی + نمودار رشد) -->
  <g transform="translate(520, 250)" filter="url(#softShadow)">
    <!-- بدنه شش‌ضلعی -->
    <polygon
      points="-70,0 -35,-60 35,-60 70,0 35,60 -35,60"
      fill="none"
      stroke="#a855f7"
      stroke-width="4"
      stroke-linejoin="round"
    />
    <!-- ستون‌های نمودار -->
    <rect x="-30" y="10" width="16" height="30" rx="4" fill="#a855f7" opacity="0.75"/>
    <rect x="-4" y="-5" width="16" height="45" rx="4" fill="#c4b5fd" opacity="0.9"/>
    <rect x="22" y="-25" width="16" height="65" rx="4" fill="#e9d5ff"/>
    <!-- فلش رشد -->
    <polyline
      points="-35,20 -10,-5 10,-15 30,-30"
      fill="none"
      stroke="#e9d5ff"
      stroke-width="3"
      stroke-linecap="round"
      stroke-linejoin="round"
    />
  </g>

  <!-- متن اصلی RaufianCRM -->
  <g filter="url(#softShadow)">
    <text
      x="860"
      y="265"
      text-anchor="start"
      fill="url(#textGradient)"
      font-size="96"
      font-family="'Poppins', system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif"
      font-weight="700"
      letter-spacing="6"
    >
      RaufianCRM
    </text>
  </g>

  <!-- زیرتیتر خیلی ظریف (اختیاری، می‌تونی حذفش کنی) -->
  <text
    x="860"
    y="320"
    text-anchor="start"
    fill="#e5e7eb"
    font-size="26"
    font-family="'Poppins', system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif"
    letter-spacing="3"
  >
    Enterprise CRM Development Studio
  </text>
</svg>
