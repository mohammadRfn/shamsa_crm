<div align="center">
  
# Project Name

<picture>
  <source media="(prefers-color-scheme: dark)" srcset="https://raw.githubusercontent.com/rajatverma01/rajatverma01/main/public/dark-github.svg">
  <source media="(prefers-color-scheme: light)" srcset="https://raw.githubusercontent.com/rajatverma01/rajatverma01/main/public/github.svg">
  <img alt="Ask Me Anything" src="https://raw.githubusercontent.com/rajatverma01/rajatverma01/main/public/github.svg" width="450">
</picture>

**توضیح کامل پروژه: یک اپلیکیشن وب مدرن با Laravel backend و Blade frontend استایل‌شده با Tailwind CSS. بدون API جداگانه (SPA یا RESTful API)، فقط صفحات Blade خالص با Tailwind برای استایلینگ.**

[![Laravel](https://img.shields.io/badge/Laravel-10.x-F05340?style=flat&logo=laravel&logoColor=white)](https://laravel.com)
[![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-v3.4-3B82F6?style=flat&logo=tailwind-css&logoColor=white)](https://tailwindcss.com)
[![PHP](https://img.shields.io/badge/PHP-8.4+-777BB4?style=flat&logo=php&logoColor=white)](https://www.php.net)
[![License](https://img.shields.io/github/license/yourusername/your-repo.svg?style=flat&logo=github)](LICENSE)

</div>

## ✨ ویژگی‌ها

- **Laravel Backend**: مسیریابی، کنترلرها، مدل‌ها، مایگریشن‌ها و validation کامل
- **Blade Views**: صفحات frontend با Blade templates (بدون SPA یا React/Vue)
- **Tailwind CSS**: استایلینگ مدرن و responsive با Tailwind (نصب via npm)
- **Database**: MySQL/PostgreSQL با Eloquent ORM
- **Validation & Forms**: فرم‌های امن با Laravel validation

## 📋 نیازمندی‌ها

| مورد       | نسخه حداقل          |
|------------|---------------------|
| PHP        | 8.4.7              |
| Composer   | 2.7+               |
| Node.js    | 20+                |
| npm        | 10+                |
| MySQL      | 8.0+               |
| Git        | 2.30+              |

---

## 🚀 نصب و راه‌اندازی کامل (گام به گام)

### 1️⃣ Clone Repository و Setup اولیه Laravel

# Clone پروژه
1. git clone https://github.com/yourusername/your-repo.git
    ```bash
    cd your-repo

2. **Install Backend Dependencies (Laravel)**

   ```bash
   composer install

3. **Setup Environment File**
   ```bash
   cp .env.example .env
   php artisan key:generate

4. **Run Database Migrations**
   ```bash
   php artisan migrate

5. **Install Frontend Dependencies (blade)**
    ```bash
    npm install

6. **Start the Development Servers**
    ```bash
    php artisan serve
    npm run dev

# کپی فایل محیطی
cp .env.example .env

# نصب dependencies PHP (Composer)
composer install --optimize-autoloader --no-dev

# تولید Application Key
php artisan key:generate --force

# تنظیم مجوزهای فولدرها (Linux/Mac)
chmod -R 775 storage bootstrap/cache
sudo chown -R $USER:www-data storage bootstrap/cache

# پاک کردن cache (اختیاری)
php artisan optimize:clear
