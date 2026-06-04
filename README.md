# php-todo-app 🚀
A lightweight, high-performance To-Do application built with **Vanilla PHP**.

## Key Features
- **Data Management:** Uses JSON flat-file storage (`tasks.json`) for lightweight database handling.
- **Session-Based Feedback:** Implements flash messages (Success/Error) using PHP Sessions.
- **PRG Pattern:** Utilizes Post-Redirect-Get pattern to prevent duplicate form submissions.
- **Localization (RTL Support):** Fully optimized Persian (RTL) interface.
  - *Note: This application is localized for Persian users, with RTL layout support built into the CSS.*

## Technical Highlights
- **Backend:** Vanilla PHP (Logic core: 257 lines).
- **Frontend:** Custom RTL CSS (199 lines).
- **Storage:** JSON flat-file storage system.

## How to Run
1. Clone this repository.
2. Ensure your local PHP environment (e.g., XAMPP, Laragon) is running.
3. Open `index.php` in your browser.

## توضیحات فارسی
این پروژه یک اپلیکیشن مدیریت کار (To-Do) است که با PHP خام پیاده‌سازی شده است.

ویژگی‌های کلیدی:
- ذخیره اطلاعات در فایل tasks.json
- پشتیبانی کامل از زبان فارسی و چینش RTL
- جلوگیری از ارسال مجدد فرم (PRG Pattern)
- استفاده از Session برای پیام‌های موفقیت و خطا
- جلوگیری از حملات XSS با استفاده از htmlspecialchars
- بهینه شده برای زبان فارسی
نکته: این اپلیکیشن برای کاربران فارسی زبان محلی سازی شده است

## نحوه اجرا
۱- رپازیتوری رو کلون کنید
۲- اطمینان پیدا نمایید محیط PHP (xampp, wamp, ...) در حال اجرا است.
۳- فایل index.php رو در مرورگر بازنمایید
