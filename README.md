# SmartSpend

SmartSpend is a personal finance tracker and analytics dashboard built as the CIS 3296 (Software Design) capstone project. It lets a user record income and expense transactions, set monthly spending limits per category, create savings goals and contribute deposits toward them, and view spending habits through interactive charts — all scoped privately to their own account.

## Features

- **Authentication** — account registration, login, logout, and profile management, built on Laravel Breeze.
- **Transactions** — add income/expense transactions with a title, date, amount, type, and category; view full transaction history.
- **Salary Auto-Fill** — save a monthly salary amount on the Profile page; selecting the "Salary" category in Add Transaction automatically fills in the amount (and sets the type to Income), so recording a paycheck takes one click.
- **Savings Goals** — create a goal with a target amount, deposit into it directly, or link a deposit automatically when adding a transaction (a single "Add Transaction" action can record everyday spending and contribute toward a goal at the same time).
- **Budget Limits** — set a monthly spending limit per category and see amount spent, percent used, and over-budget status calculated live from real transaction data.
- **Home Dashboard** — monthly income, expenses, remaining budget, and recent activity at a glance.
- **Analytics** — income vs. expense trend, spending by category, and cumulative breakdown, visualized with Chart.js.
- **Private per-account data** — every user only ever sees their own transactions, goals, and budgets.

## Tech Stack

- **Backend:** PHP 8.4, Laravel 13 (Eloquent ORM, Blade templating)
- **Database:** MySQL
- **Authentication:** Laravel Breeze
- **Frontend:** SB Admin 2 (Bootstrap 4 admin dashboard template), Chart.js
- **Tooling:** Composer, npm/Vite

## Getting Started

### Prerequisites

- PHP 8.4+
- Composer
- Node.js and npm
- MySQL

### Installation

1. Clone the repository and install dependencies:

   ```bash
   git clone <this-repository-url>
   cd smartspend
   composer install
   npm install
   ```

2. Copy the example environment file and generate an app key:

   macOS / Linux:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

   Windows (Command Prompt):
   ```cmd
   copy .env.example .env
   php artisan key:generate
   ```

   Windows (PowerShell):
   ```powershell
   Copy-Item .env.example .env
   php artisan key:generate
   ```

3. Create a MySQL database (for example `smartspend`) and set your database credentials in `.env`:

   ```
   DB_DATABASE=smartspend
   DB_USERNAME=root
   DB_PASSWORD=your_password
   ```

4. Run the migrations to create the schema:

   ```bash
   php artisan migrate
   ```

5. Build front-end assets and start the dev server:

   ```bash
   npm run dev
   ```

   In a separate terminal, start the Laravel app (use whichever fits your setup — Laravel Herd, or the built-in server):

   ```bash
   php artisan serve
   ```

6. Visit `http://localhost:8000` (or your Herd URL), register a new account, and start using SmartSpend.

## Project Structure

- `app/Models/` — `User`, `Transaction`, `Goal`, `Budget` Eloquent models
- `app/Http/Controllers/` — `TransactionController`, `GoalController`, `BudgetController`, `ProfileController`
- `resources/views/` — Blade views for Home, Analytics, Budget Limit, Savings Goals, and Transactions, plus shared `partials/` (sidebar, topbar, modals)
- `database/migrations/` — schema for users, transactions, goals, and budgets, including the nullable `goal_id` foreign key that links a transaction to a goal deposit, and a nullable `salary` column on `users`

## Author

Kimi Yamamoto — CIS 3296 Software Design, Summer 2026

## Acknowledgments

- [SB Admin 2](https://startbootstrap.com/theme/sb-admin-2) — free Bootstrap 4 admin dashboard template used for the UI
- [Chart.js](https://www.chartjs.org/) — charting library used for the Analytics page
- [Laravel Breeze](https://laravel.com/docs/starter-kits) — authentication scaffolding