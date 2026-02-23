<!DOCTYPE html>
<html dir="rtl" lang="fa">

<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: vazir, sans-serif;
            font-size: 12px;
            direction: rtl;
            margin: 15px;
        }

        .bismi {
            text-align: left;
            font-size: 11px;
            margin-bottom: 5px;
        }

        .title {
            text-align: center;
            font-size: 14px;
            font-weight: bold;
            border: 2px solid #000;
            padding: 6px;
            margin-bottom: 8px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 6px;
        }

        td {
            border: 1px solid #000;
            padding: 5px 7px;
            vertical-align: top;
        }

        .label {
            font-size: 10px;
            color: #333;
        }

        .value {
            font-size: 12px;
            font-weight: bold;
        }

        .section-label {
            background: #f0f0f0;
            font-weight: bold;
            font-size: 11px;
            width: 30%;
        }

        .text-area {
            min-height: 50px;
        }

        .parts-table th {
            background: #f0f0f0;
            text-align: center;
            font-size: 11px;
            padding: 4px;
        }

        .parts-table td {
            text-align: center;
            font-size: 11px;
        }

        .sig-table td {
            text-align: center;
            height: 70px;
            vertical-align: top;
            padding-top: 8px;
            font-size: 11px;
        }

        .sig-title {
            font-weight: bold;
            margin-bottom: 5px;
        }

        .sig-name {
            color: #444;
            font-size: 10px;
        }
    </style>
</head>

<body>

    <div style="text-align: center; font-size: 12px; margin-bottom: 8px;">به نام خدا</div>

    <div class="title">فرم گزارش کار گروه مهندسی شمسا الکترونیک</div>

    <!-- ردیف اول: تاریخ و شرح کار -->
    <table>
        <tr>
            <td width="35%" class="section-label">شرح کار درخواستی :</td>
            <td width="35%">{{ $report->part_name }}</td>
            <td width="15%" class="section-label">تاریخ درخواست :</td>
            <td width="15%">{{ $report->request_date_jalali }}</td>
        </tr>
        <tr>
            <td class="section-label">شماره درخواست :</td>
            <td colspan="3">{{ $report->request_number }}</td>
        </tr>
        <tr>
            <td class="section-label">شماره سریال دستگاه :</td>
            <td>{{ $report->serial_number }}</td>
            <td style="background: #f0f0f0;
                font-weight: bold;
                font-size: 11px;
                width: 5%;">مدل :</td>
            <td>{{ $report->device_model }}</td>
        </tr>
    </table>

    <!-- شرح ایراد -->
    <table>
        <tr>
            <td width="30%" class="section-label">شرح ایراد اعلامی :</td>
            <td class="text-area">{{ $report->issue_description }}</td>
        </tr>
    </table>

    <!-- گزارش فعالیت -->
    <table>
        <tr>
            <td width="30%" class="section-label">گزارش فعالیت های انجام شده :</td>
            <td class="text-area">{{ $report->activity_report }}</td>
        </tr>
    </table>

    <!-- قطعات -->
    <table class="parts-table">
        <tr>
            <th colspan="4">لیست قطعات مصرف شده</th>
        </tr>
        @php $chunks = array_chunk($parts, 3); @endphp
        @forelse($parts as $index => $part)
        <tr>
            <td width="5%">{{ $index + 1 }}</td>
            <td>{{ $part }}</td>
            @if(isset($parts[$index + 3]))
            <td width="5%">{{ $index + 4 }}</td>
            <td>{{ $parts[$index + 3] }}</td>
            @else
            <td></td>
            <td></td>
            @endif
        </tr>
        @if($index >= 2) @break @endif
        @empty
        <tr>
            <td colspan="4" style="text-align:center">قطعه‌ای مصرف نشده است</td>
        </tr>
        @endforelse
    </table>

    <!-- نفر ساعت و تاریخ پایان -->
    <table>
        <tr>
            <td width="30%" class="section-label">نفر ساعت انجام کار :</td>
            <td>{{ $report->hours_per_worker }} ساعت / {{ $report->workers_count }} نفر</td>
            <td width="25%" class="section-label">تاریخ پایان کار :</td>
            <td>{{ $report->end_date_jalali }}</td>
        </tr>
    </table>

    <!-- امضاها -->
    <table class="sig-table">
        <tr>
            <td width="33%">
                <div class="sig-title">امضاء کارشناس فنی</div>
                <div class="sig-name">{{ $report->user->name }}</div>
            </td>
            <td width="34%">
                <div class="sig-title">امضاء مسئول ثبت درخواست</div>
                <div class="sig-name">خانم کجباف</div>
            </td>
            <td width="33%">
                <div class="sig-title">امضاء مدیر عامل</div>
                <div class="sig-name">آقای درخشان</div>
            </td>
        </tr>
    </table>

</body>

</html>