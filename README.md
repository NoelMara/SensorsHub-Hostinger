# SensorHub

SensorHub is our group's educational platform for discovering sensors, exploring projects, and supporting hands-on learning. It combines public learning resources with class management tools for students, instructors, and administrators.

This README is a quick guide for the SensorHub team. It explains the purpose of the project, the main user experiences, and how to run the application locally.


## Features

- Browse sensors, projects, videos, and products
- Save projects for later
- Share suggestions, comments, and feedback
- Join classes and view announcements and learning modules
- Complete assessments and quizzes
- Instructor tools for managing classes and course content
- Administrator tools for managing users and platform content
- Authenticated AI chat assistance
- Email verification and content reporting

## How the platform works

SensorHub has public learning content and role-based areas. Guests can explore the platform without an account, while registered users receive access to personal features. Instructors and administrators have separate tools based on their assigned roles.

## Built with

- **Backend:** PHP 8.2+, Laravel 12
- **Frontend:** Blade, Tailwind CSS, JavaScript, Vite
- **Database:** SQLite by default; MySQL and other Laravel-supported databases are supported
- **Testing:** PHPUnit
- **Services:** Cloudinary for images, Supabase Storage for module files, Brevo for verification email, Gemini for AI chat, and Google reCAPTCHA for registration

## Current integrations

- **Brevo:** Sends email verification codes through the custom `BrevoChannel`.
- **Cloudinary:** Stores profile images and administrator-managed content images.
- **Supabase Storage:** Stores module files uploaded by instructors and administrators.
- **Gemini:** Provides authenticated AI chat.
- **Google reCAPTCHA:** Protects registration.

Some administrator features currently use PostgreSQL-specific queries, including user search and database backups. If the database is changed to MySQL, those areas must be updated and tested.

## Requirements

- PHP 8.2 or newer
- Composer
- Node.js and npm
- SQLite or MySQL
- Git

## Installation

Clone the project and install its dependencies:

```bash
git clone <repository-url>
cd SensorHub
composer install
npm install
```

Create the environment file.

**PowerShell:**

```powershell
Copy-Item .env.example .env
```

**macOS/Linux:**

```bash
cp .env.example .env
```

Generate the application key:

```bash
php artisan key:generate
```

Configure the database and external services in `.env`. The default configuration uses SQLite. If needed, create the SQLite database file:

```powershell
New-Item database\database.sqlite -ItemType File
```

Run the migrations and seed the database:

```bash
php artisan migrate --seed
```

Build the frontend assets:

```bash
npm run build
```

## Running locally

Start the Laravel server:

```bash
php artisan serve
```

Open [http://127.0.0.1:8000](http://127.0.0.1:8000) in your browser.

For development, `composer run dev` starts the application server, queue worker, logs, and Vite together:

```bash
composer run dev
```

## User roles and access

### Guest

Guest can browse sensors, projects, videos, products, and the public community area. An account is required for saved projects, suggestions, comments, classes, assessments, and other personal features.

### Registered users and students

Registered users can manage their profile, save projects, submit suggestions and feedback, comment on community content, and report content. Students can also join classes, view announcements and modules, complete assessments, and take quizzes.

### Instructors

Instructors can create and manage classes, approve or reject students, publish announcements, organize modules, create assessments and quizzes, and review class activity.

### Administrators

Administrators can manage users, profiles, platform content, suggestions, reports, and other system-level features.

Access is controlled by authentication and role-based middleware. Users should only receive the role required for their responsibilities.

## Testing

```bash
php artisan test
```

Or:

```bash
composer test
```

## Useful commands

```bash
php artisan route:list       # View registered routes
php artisan migrate          # Apply database migrations
php artisan db:seed          # Seed application data
php artisan storage:link     # Create the public storage link when needed
npm run build                # Build production assets
```

## Project structure

```text
app/          Application logic, controllers, models, and validation rules
database/     Migrations, factories, seeders, and database files
public/       Public entry point and static assets
resources/    Blade views, stylesheets, and JavaScript
routes/       Web and application routes
tests/        Feature and unit tests
```

## Environment and security

Copy `.env.example` to `.env` and provide the values required by your environment. Never commit `.env`, API keys, passwords, or other credentials.

The database seeder creates or updates an administrator account using `ADMIN_EMAIL`, `ADMIN_NAME`, and `ADMIN_PASSWORD`. Set these values before running `php artisan migrate --seed`, and replace the example password with a strong password.

Administrator database backups are generated locally in `storage/app/backups/`. They are not automatically uploaded to another service, so downloaded backups should be stored separately.

## Team notes

- Keep private credentials in `.env`, not in source files.
- Use feature branches when making changes.
- Run the test suite before submitting work:

  ```bash
  php artisan test
  ```

- Keep migrations, models, controllers, and views organized with the existing project structure.
- Document major feature changes so other group members can understand and maintain them.


