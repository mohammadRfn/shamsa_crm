<!DOCTYPE html>
<html dir="rtl" lang="fa">

<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: vazir, sans-serif;
            font-size: 13px;
            direction: rtl;
            margin: 20px;
        }

        .bismi {
            text-align: center;
            font-size: 12px;
            margin-bottom: 3px;
        }

        .title {
            text-align: center;
            font-size: 15px;
            font-weight: bold;
            border: 2px solid #000;
            padding: 6px;
            margin-bottom: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
        }

        td,
        th {
            border: 1px solid #000;
            padding: 6px 8px;
            vertical-align: middle;
            font-size: 13px;
        }

        .label {
            background: #f0f0f0;
            font-weight: bold;
            white-space: nowrap;
        }

        .cb-wrap {
            display: inline;
        }

        .cb-box {
            display: inline;
            border: 1px solid #000;
            padding: 1px 4px;
            font-size: 10px;
            background: #fff;
        }

        .cb-box-checked {
            display: inline;
            border: 1px solid #000;
            padding: 1px 4px;
            font-size: 10px;
            background: #000;
            color: #000;
        }

        .sig-table td {
            text-align: center;
            height: 65px;
            vertical-align: top;
            padding-top: 8px;
            font-size: 12px;
            font-weight: bold;
        }

        .text-area-td {
            height: 50px;
        }

        .workflow-td {
            height: 70px;
        }
    </style>
</head>

<body>

    <div class="bismi">به نام خدا</div>
    <div class="title">فرم گردش کار گروه مهندسی شمسا الکترونیک</div>

    <!-- اطلاعات اصلی -->
    <table>
        <tr>
            <td class="label" width="22%">شرح کار درخواستی :</td>
            <td width="38%" class="text-area-td">{{ $workrequest->work_description }}</td>
            <td class="label" width="18%">تاریخ درخواست/ورود :</td>
            <td width="22%">{{ $workrequest->request_date_jalali }}</td>
        </tr>
        <tr>
            <td class="label">شماره درخواست :</td>
            <td colspan="3">{{ $workrequest->request_number }}</td>
        </tr>
        <tr>
            <td class="label">شماره سریال دستگاه :</td>
            <td>{{ $workrequest->serial_number }}</td>
            <td class="label">مدل :</td>
            <td>{{ $workrequest->device_model }}</td>
        </tr>
        <tr>
            <td class="label">واحد درخواست کننده :</td>
            <td colspan="3">{{ $workrequest->request_unit }}</td>
        </tr>
    </table>

    <!-- نوع درخواست -->
    <table>
        <tr>
            <td class="label" width="22%">نوع درخواست :</td>
            <td colspan="3" style="padding-right: 15px;">
                <span class="{{ $workrequest->request_type == 'repair' ? 'cb-box-checked' : 'cb-box' }}">&nbsp;</span> تعمیرات &nbsp;&nbsp;&nbsp;
                <span class="{{ $workrequest->request_type == 'install' ? 'cb-box-checked' : 'cb-box' }}">&nbsp;</span> ساخت &nbsp;&nbsp;&nbsp;
                <span class="{{ $workrequest->request_type == 'service' ? 'cb-box-checked' : 'cb-box' }}">&nbsp;</span> سرویس و نصب &nbsp;&nbsp;&nbsp;
                <span class="{{ $workrequest->request_type == 'sale' ? 'cb-box-checked' : 'cb-box' }}">&nbsp;</span> فروش
            </td>
        </tr>
        <tr>
            <td class="label">مسئول پیگیری درخواست </td>
            <td>{{ $workrequest->contact_person }}</td>
            <td class="label">شماره تماس :</td>
            <td>{{ $workrequest->contact_phone }}</td>
        </tr>
    </table>

    <!-- شرح ایراد -->
    <table>
        <tr>
            <td class="label" width="22%">شرح ایراد اعلامی :</td>
            <td class="text-area-td">{{ $workrequest->issue_description }}</td>
        </tr>
    </table>

    <!-- اطلاعات مالی -->
    <table>
        <tr>
            <td class="label" width="25%">هزینه برآورد شده اولیه :</td>
            <td width="25%">{{ $workrequest->estimated_cost ? number_format($workrequest->estimated_cost) . ' ریال' : '' }}</td>
            <td class="label" width="25%">نتیجه اعلام قیمت اولیه :</td>
            <td width="25%">{{ $workrequest->initial_price_result }}</td>
        </tr>
        <tr>
            <td class="label">هزینه نهایی :</td>
            <td>{{ $workrequest->final_cost ? number_format($workrequest->final_cost) . ' ریال' : '' }}</td>
            <td class="label">وضعیت تصفیه حساب :</td>
            <!-- وضعیت تصفیه حساب -->
            <td>
                <span class="{{ $workrequest->payment_status == 'credit' ? 'cb-box-checked' : 'cb-box' }}">&nbsp;</span> اعتباری &nbsp;&nbsp;
                <span class="{{ $workrequest->payment_status == 'cash' ? 'cb-box-checked' : 'cb-box' }}">&nbsp;</span> نقدی &nbsp;&nbsp;
                <span class="{{ $workrequest->payment_status == 'documents' ? 'cb-box-checked' : 'cb-box' }}">&nbsp;</span> اسنادی
            </td>
        </tr>
    </table>

    <!-- شرح گردش کار -->
    <table>
        <tr>
            <td class="label" width="22%">شرح گردش کار :</td>
            <td class="workflow-td">{{ $workrequest->workflow_description }}</td>
        </tr>
    </table>

    <!-- مستندات -->
    <table>
        <tr>
            <td class="label" width="22%">مستندات حسابداری :</td>
            <td width="28%">فاکتور: {{ $workrequest->invoice_number }}</td>
            <td class="label" width="22%">سند حسابداری :</td>
            <td width="28%">{{ $workrequest->accounting_document }}</td>
        </tr>
        <tr>
            <td class="label">سند دریافت :</td>
            <td>{{ $workrequest->receipt_document }}</td>
            <td class="label">نام بانک :</td>
            <td>{{ $workrequest->bank_name }}</td>
        </tr>
    </table>

    <!-- امضاها -->
    <table class="sig-table">
        <tr>
            <td width="25%">امضا مسئول ثبت درخواست<br><br><small>خانم کجباف</small></td>
            <td width="25%">امضاء مسئول پیگیری<br><br><small>خانم کجباف</small></td>
            <td width="25%">امضاء مسئول مالی<br><small>آقای دباغ - آقای آخوندی
                    <small>کنترل حساب: <span class="cb-box">&nbsp;</span></small>
            <td width="25%">امضاء مدیر عامل<br><br><small>آقای درخشان</small></td>
        </tr>
    </table>

</body>

</html>