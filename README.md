# 🚀 GetPayIn – Task Documentation

## Api Documentation

For detailed instructions, see [README_API.md](README_FLASH_SALE.md)

## 📌 Project Overview

This project is built using:

| Tech Stack                 | Version        |
| -------------------------- | -------------- |
| **PHP**                    | 8.2            |
| **Laravel**                | 12.x           |
| **MySQL**                  | locale         |
| **Redis**                  | via Docker     |
| **Docker & Shell Scripts** | for automation |

---

## 📘 General Information

1. 🐳 **Redis runs inside Docker**.  
   If you want another cache strategy, you must modify the codebase.  
   Recommended: use **Docker with WSL Ubuntu** or Linux.

2. ⚡ The `scripts` folder contains **fast command-line tools** to speed up your development.

3. 🔒 **Roles & Permissions** are _not implemented_.  
   You can add them later using `spatie/laravel-permission` or your own logic.

4. 🤖 ChatGPT is used for writing code fast, documentation, shell scripts, tasks, markdown, etc.

> 💡 Make shell scripts executable with:
>
> ```bash
> chmod +x Name.sh
> ```

---

## 🟥 Start Redis Service

```bash
./scripts/Docker.sh
```

---

## 🟩 Start Application

This script will:

- Install composer dependencies
- Run migrations
- Seed data
- Start the Laravel server on port **8000** (or your custom port)

- First time

```bash
./scripts/StartApp.sh
```

- Other times:

```bash
 php artisan serve
```

---

## scheduled task:

```bash
php artisan schedule:work
```

---

## Logs:

- You can use `LoggerTrait` and show the log's channel in `config/logging.php`

- Error logs in `storage/logs/errors.log`
- Payment logs in `storage/logs/payment.log`

---

## 🧪 Testing

### ▶ Run Parallel test:

- i use `composer require brianium/paratest --dev`

```bash
 php artisan test --parallel
```

### ▶ Run Test for one class:

```bash
php artisan test --filter=Class
```

### ▶ Run all tests:

```bash
php artisan test
```

### ▶ Push new feature to `dev` branch after successful tests:

```bash
./scripts/CI.sh
```

---

## 🛠 Create Services

You can create Domain Services (Redis, Payment, SMS, etc.)  
or Database Services (Repository Pattern: User, Order, Product…)

### 📌 Database Service:

```bash
php artisan make:service Table --model
```

or

```bash
php artisan make:service TableService --model
```

### 📌 Domain Service:

```bash
php artisan make:service Name
```

or

```bash
php artisan make:service NameService
```

---

## 📎 Notes

- This README contains icons, tables, and sections for better UX.
- Everything is formatted in one block so you can easily **copy all with one click**.
