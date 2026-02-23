<div align="center">
  
# OfficeManager

<picture>
  <source media="(prefers-color-scheme: dark)" srcset="https://raw.githubusercontent.com/rajatverma01/rajatverma01/main/public/dark-github.svg">
  <source media="(prefers-color-scheme: light)" srcset="https://raw.githubusercontent.com/rajatverma01/rajatverma01/main/public/github.svg">
  <img alt="Ask Me Anything" src="https://raw.githubusercontent.com/rajatverma01/rajatverma01/main/public/github.svg" width="450">
</picture>

**توضیح کامل پروژه: یک اپلیکیشن وب مدرن با Laravel backend و Blade frontend استایل‌شده با Tailwind CSS. بدون API جداگانه (SPA یا RESTful API)، فقط صفحات Blade خالص با Tailwind برای استایلینگ.**

[![Laravel](https://img.shields.io/badge/Laravel-12.x-F05340?style=flat&logo=laravel&logoColor=white)](https://laravel.com)
[![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-v4-3B82F6?style=flat&logo=tailwind-css&logoColor=white)](https://tailwindcss.com)
[![PHP](https://img.shields.io/badge/PHP-8.4+-777BB4?style=flat&logo=php&logoColor=white)](https://www.php.net)
[![License](https://img.shields.io/github/license/yourusername/your-repo.svg?style=flat&logo=github)](LICENSE)

</div>

## ✨ ویژگی‌ها

- **Laravel Backend**: مسیریابی، کنترلرها، مدل‌ها، مایگریشن‌ها و validation کامل
- **Blade Views**: صفحات frontend با Blade templates (بدون SPA یا React/Vue)
- **Tailwind CSS**: استایلینگ مدرن و responsive با Tailwind (نصب via npm)
- **Database**: MySQL با Eloquent ORM
- **Validation & Forms**: فرم‌های امن با Laravel validation


## 📋 نیازمندی‌ها

| مورد       | نسخه حداقل     |
|------------|----------------|
| PHP        | 8.4+          |
| Composer   | 2.7+          |
| Node.js    | 20+           |
| npm        | 10+           |
| MySQL      | 8.0+          |
| Git        | 2.30+         |

---

## 🚀 نصب و راه‌اندازی (گام به گام)
### Steps to Install

1. **Clone the Repository**

   First, clone the project from GitHub:

   ```bash
   git clone https://github.com/mohammadRfn/shamsa_crm.git
   cd Raufian-shamsa-crm
   
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

5. **Install Frontend Dependencies (Vue.js)**
    ```bash
    npm install

6. **Start the Development Servers**
    ```bash
    php artisan serve
    npm run dev

```bash
# کپی فایل محیطی
cp .env.example .env

# تولید Application Key
php artisan key:generate --force

# نصب PHP dependencies
composer install --optimize-autoloader --no-dev

# تنظیم مجوزها (Linux/Mac)
chmod -R 775 storage bootstrap/cache
sudo chown -R $USER:www-data storage bootstrap/cache

