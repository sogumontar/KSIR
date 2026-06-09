# GEMINI.md - KSIR (Inventory Pro) Development Context & Guidelines

This document provides critical operational context, architectural rules, and development guidelines for the KSIR (Inventory Pro) application. **All future interactions and code modifications must strictly adhere to these mandates.**

---

## 1. Project Overview & Architecture

### Purpose
KSIR (Inventory Pro) is a modern web-based inventory and transaction management system designed for distinct **Admin** and **Regular User (Staff)** workflows. It allows administrators to manage users and oversee system metrics, while regular staff users can manage goods, log transactions, and track inventory/sales history.

### Tech Stack
- **Backend Framework:** PHP 8.3+ & Laravel 13.x
- **Frontend / Interactivity:** Livewire 4.x (using component-driven reactivity, `wire:navigate`, and lifecycle methods)
- **CSS Framework:** Tailwind CSS (configured in `resources/views/components/layouts/*` via Play CDN for development and combined with Tailwind v4/Vite in production)
- **Assets / Build Tool:** Vite 8.x with `@tailwindcss/vite` and `laravel-vite-plugin`
- **PDF Generation:** Barryvdh Laravel DOMPDF (`barryvdh/laravel-dompdf` ^3.1)

### Key Directories
- `app/Models/`: Eloquent Models utilizing PHP 8.3 attribute syntax.
- `app/Livewire/`: State-driven components handling frontend-backend coordination.
- `app/Http/Middleware/`: Role-based route protection.
- `resources/views/components/layouts/`: Base layouts (admin, user, guest) styled with customizable Tailwind configurations.
- `resources/views/livewire/`: Livewire Blade views containing inline templates and modular elements.
- `stitch-design/`: Original visual prototypes and design files for various screens. **Use these as references for UI styling and layout structure.**

---

## 2. Structural & Coding Conventions

### A. Eloquent Models with PHP 8.3 Attributes
To keep models concise, this codebase uses **declarative PHP 8.3 attributes** rather than traditional protected properties to define model metadata:
- **Mass Assignment:** Use `#[Fillable([...])]` attribute instead of `protected $fillable`.
- **Serialization:** Use `#[Hidden([...])]` attribute instead of `protected $hidden`.

*Example (`app/Models/User.php`):*
```php
namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Foundation\Auth\User as Authenticatable;

#[Fillable(['name', 'email', 'phone_number', 'password', 'is_admin'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    // ...
}
```

### B. Livewire Components (Livewire v4)
All interactive frontend views must be structured as Livewire components:
1. **Layout & Title:** Annotate components using `#[Layout(...)]` and `#[Title(...)]` attributes.
2. **State & Reactive Properties:** Declare standard public properties (e.g., `public string $search = '';`).
3. **Query Parameters:** Use the `#[Url]` attribute to bind component properties directly to the URL query string (e.g., `#[Url] public string $statusFilter = '';`).
4. **Pagination:** Include the `WithPagination` trait when rendering tables or lists.
5. **SPA Transitions:** Ensure navigation links use `wire:navigate` for fast, seamless page updates.

*Example (`app/Livewire/Admin/UserManagement.php`):*
```php
namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('components.layouts.admin')]
#[Title('User Management - Inventory Pro')]
class UserManagement extends Component
{
    // ...
}
```

### C. Database Schemas & Eloquent Relationships
Ensure model relationships are properly typed and utilize soft deletes where established:
- **Good Model:** Belongs to a `User` and HasMany `Transaction`s. Uses `SoftDeletes`.
- **Transaction Model:** Belongs to a `User` and Belongs to a `Good` (with `withTrashed()`).
- **Cascade Behavior:** The `Good` model features a `booted()` model observer. When a Good's `name` or `price` is updated, the changes automatically cascade to all associated active transactions' `item_name`, `price`, and `total_price` fields to preserve historical integrity. Always follow this pattern.

---

## 3. Security, Authentication & Role Management

The application is split into two distinct, isolated user portals governed by middleware:
- **Admin Portal (`/admin/*`):**
  - Protected by `admin` middleware alias (`EnsureUserIsAdmin`).
  - Restricts access to users with `is_admin === true`.
- **Regular User/Staff Portal (`/user/*`):**
  - Protected by `user` middleware alias (`EnsureUserIsRegularUser`).
  - Restricts access to users with `is_admin === false`.
- **Authentication Routes (`/`):**
  - Handled by `App\Livewire\Auth\Login`.
  - Supports authentication using either **Email** or **Username (name)**.

*Middleware Aliases registration in `bootstrap/app.php`:*
```php
$middleware->alias([
    'admin' => \App\Http\Middleware\EnsureUserIsAdmin::class,
    'user' => \App\Http\Middleware\EnsureUserIsRegularUser::class,
]);
```

---

## 4. Design, Styling & Mockups

- **Tailwind Config:** The frontend layouts (`resources/views/components/layouts/`) use Tailwind CDN with custom configurations defining the design system's colors (e.g., `primary`, `secondary`, `surface-container`), typography, and border radiuses.
- **Visual Reference (`stitch-design/`):** When implementing or modifying pages, refer to the high-fidelity HTML/CSS files in the `stitch-design/` directory. Ensure your implementations match these designs in colors, fonts, margins, and layouts to maintain a polished, highly aesthetic UI.
- **PDF Generation:** Use simple table structures and inline styles with standard font compatibility (e.g., `DejaVu Sans`) to ensure proper rendering with `dompdf`.

---

## 5. Build, Development & Test Workflows

### Prerequisites
- **PHP** ^8.3
- **Composer** (PHP dependency manager)
- **Node.js** & **npm**

### Key Scripts (from `composer.json` & `package.json`)
Manage the environment and build pipeline using the following command suite:

- **Full Setup:**
  ```bash
  composer run setup
  ```
  *Impact: Installs PHP and Node dependencies, copies `.env.example` to `.env`, generates the application key, runs migrations, and compiles static assets.*

- **Local Development Environment:**
  ```bash
  composer run dev
  ```
  *Impact: Spawns the local PHP server, queue listener, log tailing, and Vite dev server concurrently.*

- **Execute Test Suite:**
  ```bash
  composer run test
  ```
  *Impact: Clears configuration caches and executes PHPUnit test suites.*

---

## 6. Guidelines for AI Agents & Developers

- **Never Disable Safeguards:** Never bypass Laravel's security features, CSRF checks, or type safety.
- **Attribute Adherence:** When adding new Eloquent models, ensure you use the declarative PHP 8.3 attribute-based configuration (`#[Fillable]`, `#[Hidden]`) instead of standard protected properties.
- **Test-Driven Changes:** Always write or update corresponding tests in `tests/Feature/` or `tests/Unit/` for any bug fixes or features added.
- **Surgical Edits:** Keep modifications highly focused on the requested tasks. Avoid refactoring unrelated systems unless explicitly instructed.
- **Look at the Mockups:** Check `stitch-design/` files before updating layouts to maintain layout consistency.
