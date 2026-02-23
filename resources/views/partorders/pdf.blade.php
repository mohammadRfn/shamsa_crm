<!DOCTYPE html>
<html dir="rtl" lang="fa">
<head>
<meta charset="UTF-8">
<style>
    body {
        font-family: vazir, sans-serif;
        font-size: 11px;
        direction: rtl;
        margin: 15px;
    }
    .bismi { text-align: center; font-size: 11px; margin-bottom: 3px; }
    .company { text-align: center; font-size: 12px; font-weight: bold; margin-bottom: 3px; }
    .title { text-align: center; font-size: 14px; font-weight: bold; border: 2px solid #000; padding: 5px; margin-bottom: 10px; }
    table { width: 100%; border-collapse: collapse; margin-bottom: 8px; }
    td, th { border: 1px solid #000; padding: 5px 6px; vertical-align: middle; }
    th { background: #f0f0f0; text-align: center; font-weight: bold; font-size: 11px; }
    .parts-td { text-align: center; font-size: 11px; }
    .empty-row td { height: 22px; }
    .sig-table td { text-align: center; height: 60px; vertical-align: top; padding-top: 8px; font-size: 11px; font-weight: bold; }
    .footer-table td { border: 1px solid #000; padding: 5px 8px; font-size: 11px; }
    .footer-label { background: #f0f0f0; font-weight: bold; width: 30%; }
</style>
</head>
<body>

<div class="bismi">به نام خدا</div>
<div class="company">گروه مهندسی شمسا الکترونیک</div>
<div class="title">فرم سفارش قطعه</div>

<!-- اطلاعات -->
<table class="footer-table">
    <tr>
        <td class="footer-label">تاریخ :</td>
        <td>{{ $partorder->order_date_jalali }}</td>
        <td class="footer-label">شماره سفارش :</td>
        <td>{{ $partorder->order_number }}</td>
    </tr>
    <tr>
        <td class="footer-label">نام تجهیز تعمیراتی :</td>
        <td colspan="3">{{ $partorder->equipment_name }}</td>
    </tr>
</table>

<!-- جدول قطعات -->
<table>
    <tr>
        <th width="5%">ردیف</th>
        <th width="20%">نام قطعه</th>
        <th width="25%">مشخصات</th>
        <th width="10%">پکیج</th>
        <th width="8%">تعداد</th>
        <th width="32%">توضیحات</th>
    </tr>
    @php $parts = $partorder->part_name ?? []; $totalRows = max(count($parts), 5); @endphp
    @for($i = 0; $i < $totalRows; $i++)
    <tr class="{{ isset($parts[$i]) ? '' : 'empty-row' }}">
        <td class="parts-td">{{ $i + 1 }}</td>
        <td class="parts-td">{{ $parts[$i] ?? '' }}</td>
        <td class="parts-td">{{ ($partorder->specifications ?? [])[$i] ?? '' }}</td>
        <td class="parts-td">{{ ($partorder->package ?? [])[$i] ?? '' }}</td>
        <td class="parts-td">{{ ($partorder->quantity ?? [])[$i] ?? '' }}</td>
        <td class="parts-td">{{ ($partorder->description ?? [])[$i] ?? '' }}</td>
    </tr>
    @endfor
</table>

<!-- امضاها -->
<table class="sig-table">
    <tr>
        <td width="25%">امضا درخواست کننده<br><small>{{ $partorder->user->name }}</small></td>
        <td width="25%">امضا تائید کننده</td>
        <td width="25%">امضا مسئول تامین</td>
        <td width="25%">امضا مدیر عامل<br><small>آقای درخشان</small></td>
    </tr>
</table>

</body>
</html>