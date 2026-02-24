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
            text-align: center;
            font-size: 11px;
            margin-bottom: 2px;
        }

        .title {
            text-align: center;
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 0;
        }

        td {
            border: 1px solid #000;
            padding: 5px 7px;
            vertical-align: middle;
            font-size: 12px;
        }

        .lbl {
            font-weight: bold;
            background: #fff;
            white-space: nowrap;
        }

        .h-tall {
            height: 55px;
            vertical-align: top;
            padding-top: 6px;
        }

        .h-med {
            height: 35px;
        }

        .h-workflow {
            height: 80px;
            vertical-align: top;
            padding-top: 6px;
        }

        .cb {
            width: 14px;
            height: 14px;
            border: 1.5px solid #000;
            padding: 0;
            background: #fff;
        }

        .cb-on {
            width: 14px;
            height: 14px;
            border: 1.5px solid #000;
            background: #000;
            padding: 0;
        }

        .cbl {
            border: 0;
            padding: 2px 10px 2px 2px;
            font-size: 12px;
        }

        .cbg {
            border: 0;
            padding: 0;
            width: 6px;
        }

        .cbt {
            border-collapse: collapse;
            width: auto;
            margin: 0;
        }

        .cbt td {
            font-size: 12px;
        }

        .sig td {
            text-align: center;
            height: 70px;
            vertical-align: top;
            padding-top: 8px;
            font-size: 11px;
            font-weight: bold;
            border: 1px solid #000;
        }
    </style>
</head>

<body>

    <div class="bismi">به نام خدا</div>
    <div class="title">فرم گردش کار گروه مهندسی شمسا الکترونیک</div>

    <table>
        <tr>
            <td class="lbl" width="18%" rowspan="2">شرح کار درخواستی :</td>
            <td width="32%" rowspan="2" class="h-tall">{{ $workrequest->work_description }}</td>
            <td class="lbl" width="20%">تاریخ درخواست / ورود :</td>
            <td width="30%">{{ $workrequest->request_date_jalali }}</td>
        </tr>
        <tr>
            <td class="lbl">شماره درخواست :</td>
            <td>{{ $workrequest->request_number }}</td>
        </tr>
    </table>

    <table>
        <tr>
            <td class="lbl" width="18%">شماره سریال دستگاه :</td>
            <td width="32%">{{ $workrequest->serial_number }}</td>
            <td class="lbl" width="20%">مدل :</td>
            <td width="30%">{{ $workrequest->device_model }}</td>
        </tr>
    </table>

    <table>
        <tr>
            <td class="lbl" width="18%">واحد درخواست کننده :</td>
            <td colspan="3">{{ $workrequest->request_unit }}</td>
        </tr>
        <tr>
            <td class="lbl">نوع درخواست :</td>
            <td colspan="3">
                <table class="cbt">
                    <tr>
                        <td class="{{ $workrequest->request_type == 'repair' ? 'cb-on' : 'cb' }}">&nbsp;</td>
                        <td class="cbl">تعمیرات</td>
                        <td class="cbg"></td>
                        <td class="{{ $workrequest->request_type == 'install' ? 'cb-on' : 'cb' }}">&nbsp;</td>
                        <td class="cbl">ساخت</td>
                        <td class="cbg"></td>
                        <td class="{{ $workrequest->request_type == 'service' ? 'cb-on' : 'cb' }}">&nbsp;</td>
                        <td class="cbl">سرویس و نصب</td>
                        <td class="cbg"></td>
                        <td class="{{ $workrequest->request_type == 'sale' ? 'cb-on' : 'cb' }}">&nbsp;</td>
                        <td class="cbl">فروش</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <table>
        <tr>
            <td class="lbl" width="18%">مسئول پیگیری درخواست :</td>
            <td width="32%">{{ $workrequest->contact_person }}</td>
            <td class="lbl" width="20%">شماره تماس :</td>
            <td width="30%">{{ $workrequest->contact_phone }}</td>
        </tr>
    </table>

    <table>
        <tr>
            <td class="lbl" width="18%">شرح ایراد اعلامی :</td>
            <td class="h-med">{{ $workrequest->issue_description }}</td>
        </tr>
    </table>

    <table>
        <tr>
            <td class="lbl" width="18%">هزینه برآورد شده اولیه :</td>
            <td width="32%">{{ $workrequest->estimated_cost ? number_format($workrequest->estimated_cost) . ' ریال' : '' }}</td>
            <td class="lbl" width="20%">نتیجه اعلام قیمت اولیه :</td>
            <td width="30%">{{ $workrequest->initial_price_result }}</td>
        </tr>
        <tr>
            <td class="lbl">هزینه نهایی :</td>
            <td colspan="3">{{ $workrequest->final_cost ? number_format($workrequest->final_cost) . ' ریال' : '' }}</td>
        </tr>
    </table>

    <table>
        <tr>
            <td class="lbl" width="18%">شرح گردش کار :</td>
            <td class="h-workflow">{{ $workrequest->workflow_description }}</td>
        </tr>
    </table>

    <table>
        <tr>
            <td class="lbl" width="18%">وضعیت تصفیه حساب :</td>
            <td width="32%">
                <table class="cbt">
                    <tr>
                        <td class="{{ $workrequest->payment_status == 'credit' ? 'cb-on' : 'cb' }}">&nbsp;</td>
                        <td class="cbl">اعتباری</td>
                        <td class="cbg"></td>
                        <td class="{{ $workrequest->payment_status == 'cash' ? 'cb-on' : 'cb' }}">&nbsp;</td>
                        <td class="cbl">نقدی</td>
                        <td class="cbg"></td>
                        <td class="{{ $workrequest->payment_status == 'documents' ? 'cb-on' : 'cb' }}">&nbsp;</td>
                        <td class="cbl">اسنادی</td>
                    </tr>
                </table>
            </td>
            <td class="lbl" width="20%">مستندات حسابداری :</td>
            <td width="30%">فاکتور: {{ $workrequest->invoice_number }}<br>سند حسابداری: {{ $workrequest->accounting_document }}</td>
        </tr>
        <tr>
            <td class="lbl">سند دریافت :</td>
            <td>{{ $workrequest->receipt_document }}</td>
            <td class="lbl">نام بانک :</td>
            <td>{{ $workrequest->bank_name }}</td>
        </tr>
    </table>

    <table class="sig">
        <tr>
            <td width="25%">امضا مسئول ثبت درخواست<br><br>خانم کجباف</td>
            <td width="25%">امضاء مسئول پیگیری<br><br>خانم کجباف</td>
            <td width="25%">
                امضاء مسئول مالی<br><br>
                <table class="cbt" style="margin: 0 auto; font-size:10px;">
                    <tr>
                        <td style="border:0;padding:1px 6px 1px 2px;font-size:10px;">آقای دباغ</td>
                        <td style="border:0;width:4px;"></td>
                        <td style="border:0;padding:1px 6px 1px 2px;font-size:10px;">آقای آخوندی</td>
                    </tr>
               
                    <tr>
                        <td colspan="4" style="border:0;padding:1px 2px;font-size:10px;">کنترل حساب</td>
                    </tr>
                </table>
            </td>
            </td>
            <td width="25%">امضاء مدیر عامل<br><br>آقای درخشان</td>
        </tr>
    </table>

</body>

</html>