# Library Management System - Project Context

This document provides a comprehensive overview of the Library Management System project for AI assistants to understand the codebase efficiently.

## 🚀 Project Overview
A web-based Library Management System (Sistem Manajemen Perpustakaan) built with Laravel. It features a dual-interface: a standard Web UI for end-users/admins and a REST API for potential mobile or third-party integrations.

## 🛠 Tech Stack
- **Framework:** Laravel 9.19+
- **Language:** PHP 8.0+
- **Frontend:** Blade Templates, Vanilla CSS, Vite (JS/CSS bundling)
- **Database:** MySQL
- **Authentication:** 
    - Custom JWT Implementation (`App\Helpers\JwtHelper`)
    - Web: Session-based using JWT stored in sessions (`JwtSession` middleware)
    - API: Header-based JWT (`ApiJwt` middleware)
    - Social: Google Login (Laravel Socialite)
- **Tooling:** Composer, NPM/Vite

## 📂 Core Directory Structure
- `app/Http/Controllers`: Contains logic for Web and API controllers.
    - `API/`: RESTful controllers returning JSON.
    - `WebAuthController.php`: Handles login/register/logout and Google OAuth.
- `app/Models`: Eloquent models representing the database schema.
- `app/Helpers`: `JwtHelper.php` for manual JWT creation and validation.
- `app/Http/Middleware`:
    - `JwtSession.php`: Authenticates web requests using session tokens.
    - `ApiJwt.php`: Authenticates API requests using Bearer tokens.
    - `EnsureAdmin.php`: Restricts access to administrative features.
- `resources/views`: Blade templates for the Web UI.
- `routes/`:
    - `web.php`: Routes for the browser interface.
    - `api.php`: Routes for the REST API.

## 📊 Database Schema & Models
1. **User:** `id, name, email, password, role, google_id`.
    - Roles: `admin`, `user` (default).
2. **Category:** `id, name`. (Managed by Admin)
3. **Book:** `id, category_id, title, author, description, cover`.
    - Cover images are stored in `storage/app/public/covers/`.
4. **Member:** `id, name, email, phone, address`. (Managed by Admin)
5. **Loan:** `id, book_id, member_id, loan_date, return_date, status`. (Managed by Admin)

## 🔑 Authentication Logic
- The project does NOT use standard Laravel Auth guards for the custom logic.
- **JWT Creation:** `JwtHelper::create($user)` generates a token with `sub`, `name`, `email`, `role`, `iat`, and `exp` claims.
- **Web Login:** On successful login, the JWT is stored in `session('token')`. The `JwtSession` middleware decodes this on every request and calls `auth()->login($user)` to populate the user in the session.
- **API Login:** Returns a JSON response with the token. The `ApiJwt` middleware expects an `Authorization: Bearer <token>` header.

## 🌐 Key Endpoints (Web)
- `/login`, `/register`: Auth pages.
- `/auth/google`: Google Login redirect.
- `/`: Dashboard (Protected).
- `/books`: View and manage books.
- **Admin Only:** `/categories`, `/members`, `/loans`.

## 📡 Key Endpoints (API)
- `POST /api/login`, `POST /api/register`
- `GET /api/me`: Get current user info.
- `apiResource` for `categories`, `books`, `members`, `loans` (all protected by `api.jwt`).

## 🛠 Setup & Development
1. `composer install` & `npm install`
2. `.env` configuration (Database, `JWT_SECRET`, Google Client ID/Secret).
3. `php artisan migrate --seed`
4. `php artisan storage:link` (for book covers)
5. `php artisan serve` & `npm run dev`
