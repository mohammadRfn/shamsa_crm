document.addEventListener('DOMContentLoaded', function () {
    $('.jalali-datepicker').persianDatepicker({
        format: 'YYYY/MM/DD',
        autoClose: true,
        initialValue: true
    });
});