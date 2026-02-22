<?php

namespace App\Helpers;

use Morilog\Jalali\Jalalian;

class JalaliHelper
{
    /**
     * 
     * @param string|null $gregorianDate تاریخ میلادی (Y-m-d یا Carbon instance)
     * @param string $format فرمت خروجی (پیش‌فرض: Y/m/d)
     * @return string|null
     */
    public static function toJalali($gregorianDate, string $format = 'Y/m/d'): ?string
    {
        if (empty($gregorianDate)) {
            return null;
        }

        try {
            $jalali = Jalalian::fromDateTime($gregorianDate)->format($format);
            return self::convertToFarsiNumbers($jalali);
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * تبدیل تاریخ شمسی به میلادی
     * 
     * @param string|null $jalaliDate تاریخ شمسی (مثلاً: 1403/11/28 یا ۱۴۰۳/۱۱/۲۸)
     * @return string|null تاریخ میلادی به فرمت Y-m-d
     */
    public static function toGregorian($jalaliDate): ?string
    {
        if (empty($jalaliDate)) {
            return null;
        }

        try {
            // اول اعداد فارسی رو به انگلیسی تبدیل کن
            $jalaliDate = self::convertToEnglishNumbers($jalaliDate);
            
            // جدا کردن سال، ماه، روز
            $parts = preg_split('/[-\/]/', $jalaliDate);
            
            if (count($parts) !== 3) {
                return null;
            }

            [$year, $month, $day] = $parts;
            
            // تبدیل به میلادی
            $gregorian = Jalalian::fromFormat('Y/m/d', "$year/$month/$day")->toCarbon();
            
            return $gregorian->format('Y-m-d');
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * تاریخ و زمان امروز به شمسی
     * 
     * @param string $format
     * @return string
     */
    public static function now(string $format = 'Y/m/d H:i:s'): string
    {
        $jalali = Jalalian::now()->format($format);
        return self::convertToFarsiNumbers($jalali);
    }

    /**
     * تاریخ امروز به شمسی (بدون ساعت)
     * 
     * @return string
     */
    public static function today(): string
    {
        return self::now('Y/m/d');
    }

    /**
     * تبدیل اعداد انگلیسی به فارسی
     * 
     * @param string $string
     * @return string
     */
    public static function convertToFarsiNumbers(string $string): string
    {
        $englishNumbers = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
        $farsiNumbers = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
        
        return str_replace($englishNumbers, $farsiNumbers, $string);
    }

    /**
     * تبدیل اعداد فارسی به انگلیسی
     * 
     * @param string $string
     * @return string
     */
    public static function convertToEnglishNumbers(string $string): string
    {
        $farsiNumbers = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
        $englishNumbers = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
        
        // اعداد عربی هم ممکنه وارد بشه
        $arabicNumbers = ['٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩'];
        
        $string = str_replace($farsiNumbers, $englishNumbers, $string);
        $string = str_replace($arabicNumbers, $englishNumbers, $string);
        
        return $string;
    }

    /**
     * فرمت کامل با نام روز و ماه فارسی
     * مثال: پنج‌شنبه ۲۸ بهمن ۱۴۰۳
     * 
     * @param string|null $gregorianDate
     * @return string|null
     */
    public static function toJalaliFull($gregorianDate): ?string
    {
        if (empty($gregorianDate)) {
            return null;
        }

        try {
            $jalali = Jalalian::fromDateTime($gregorianDate);
            
            $dayName = self::getDayName($jalali->format('w'));
            $day = self::convertToFarsiNumbers($jalali->format('d'));
            $monthName = self::getMonthName($jalali->format('m'));
            $year = self::convertToFarsiNumbers($jalali->format('Y'));
            
            return "$dayName $day $monthName $year";
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * نام روز هفته به فارسی
     */
    private static function getDayName(int $dayNumber): string
    {
        $days = [
            0 => 'یک‌شنبه',
            1 => 'دوشنبه',
            2 => 'سه‌شنبه',
            3 => 'چهارشنبه',
            4 => 'پنج‌شنبه',
            5 => 'جمعه',
            6 => 'شنبه',
        ];
        
        return $days[$dayNumber] ?? '';
    }

    /**
     * نام ماه به فارسی
     */
    private static function getMonthName(int $monthNumber): string
    {
        $months = [
            1 => 'فروردین',
            2 => 'اردیبهشت',
            3 => 'خرداد',
            4 => 'تیر',
            5 => 'مرداد',
            6 => 'شهریور',
            7 => 'مهر',
            8 => 'آبان',
            9 => 'آذر',
            10 => 'دی',
            11 => 'بهمن',
            12 => 'اسفند',
        ];
        
        return $months[$monthNumber] ?? '';
    }
}