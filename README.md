# CRRMS - Campus Resource Reservation and Management System

A comprehensive web application for managing campus facilities, equipment, reservations, and resource allocation.

## Requirements

- PHP 8.3+
- PostgreSQL 14+
- Composer
- Node.js & NPM
- PHP Extensions: `pdo_pgsql`, `pgsql`

## Database Setup

This project uses PostgreSQL. Ensure you have PostgreSQL installed and configured:

1. Create a database:
```sql
CREATE DATABASE crrms;
```

2. Configure your `.env` file:
```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=crrms
DB_USERNAME=postgres
DB_PASSWORD=your_password
```

3. Run migrations:
```bash
php artisan migrate
```

4. Run seeders:
```bash
php artisan db:seed
```

## Installation

```bash
composer install
npm install
```

## Development Server

To run the application locally, you need to start three services:

1. **Laravel Application Server** (Terminal 1):
```bash
php artisan serve
```
The application will be available at http://127.0.0.1:8000

2. **Vite Development Server** (Terminal 2):
```bash
npm run dev
```
Vite will run at http://localhost:5173

3. **Reverb WebSocket Server** (Terminal 3):
```bash
php artisan reverb:start
```
Reverb will run on port 8081

## Livewire Components

This project uses Livewire 4.3 for reactive UI components. Livewire is used for:

- **Listing Pages**: All index pages use Livewire for reactive search, filtering, and pagination
  - Facility reservations
  - Equipment borrow requests
  - Service requests
  - Maintenance logs
  - Notifications
  - Users
  - Buildings
  - Inventory
  - Announcements
  - Calendar events
  - Chat

- **Global Search**: Cross-resource search component for users, buildings, facilities, equipment, and reservations

- **Real-Time Updates**: Livewire components integrate with Laravel Reverb for real-time updates without page refresh

Livewire components follow the existing layered MVC + Service Layer architecture, reusing service classes for business logic.

## Real-Time Features (Laravel Reverb)

This project uses Laravel Reverb for real-time WebSocket communication. To enable real-time features:

1. Configure Reverb in your `.env` file:
```env
BROADCAST_CONNECTION=reverb
REVERB_APP_KEY=your-app-key
REVERB_APP_SECRET=your-app-secret
REVERB_APP_ID=your-app-id
REVERB_HOST=127.0.0.1
REVERB_PORT=8081
REVERB_SCHEME=http
REVERB_SERVER_HOST=0.0.0.0
REVERB_SERVER_PORT=8081
```

2. Start the Reverb server:
```bash
php artisan reverb:start
```

3. The application will now broadcast real-time updates for:
   - Facility reservation status changes
   - Equipment borrowing request status changes
   - Service request status changes
   - Maintenance request status changes
   - New notifications

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

In addition, [Laracasts](https://laracasts.com) contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

You can also watch bite-sized lessons with real-world projects on [Laravel Learn](https://laravel.com/learn), where you will be guided through building a Laravel application from scratch while learning PHP fundamentals.

## Agentic Development

Laravel's predictable structure and conventions make it ideal for AI coding agents like Claude Code, Cursor, and GitHub Copilot. Install [Laravel Boost](https://laravel.com/docs/ai) to supercharge your AI workflow:

```bash
composer require laravel/boost --dev

php artisan boost:install
```

Boost provides your agent 15+ tools and skills that help agents build Laravel applications while following best practices.

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
