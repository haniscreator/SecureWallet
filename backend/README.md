# Backend - Secure Wallet API

![Build Status](https://github.com/haniscreator/SecureWallet/actions/workflows/backend-ci.yml/badge.svg)

## Tech Stack

- **Framework**: Laravel 12 (PHP 8.4+)
- **Database**: MySQL / SQLite (for unit testing)
- **Architecture**: Domain-Driven Design (DDD)
- **Authentication**: Laravel Sanctum
- **Testing**: PHPUnit

## Up & Running (Local)

If you are not using Docker, you can run the backend locally:

1. **Install Dependencies**:
   ```bash
   composer install
   ```
2. **Setup Environment**:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
3. **Database Setup**:
   Configure your `.env` file with your database credentials, then run:
   ```bash
   php artisan migrate --seed
   ```
4. **Serve Application**:
   ```bash
   php artisan serve
   ```
   The API will be available at `http://localhost:8000`.

## Project Structure

The project follows a **Domain-Driven Design (DDD)** inspired structure to separate concerns and improve maintainability.

```
app/
├── Domain/                 # Core Business Logic & Models
│   ├── Auth/               
│   ├── User/
│   ├── Wallet/             # Wallet, Transactions, Logic
│   └── Currency/
├── Http/
│   ├── Controllers/        # Thin API Controllers
│   ├── Requests/           # Validation Layer
│   └── Resources/          # API Response Transformation
├── Policies/               # Authorization Policies
└── Providers/              # Service Providers
```

## How It Works

### Architecture Flow
The application uses a strict flow for handling requests:
1. **Controller**: Validates input and immediately converts it to a typed Data Transfer Object (DTO).
2. **Action**: Orchestrates the business logic using the DTO.
3. **Service**: Handles the core logic and database interactions.
4. **Outcome**: Returns the result back up the chain, which is transformed into a JSON response.

For detailed diagrams and architectural deep-dives, please see the **[System Design Documentation](../docs/system-design.md)**.

## API Documentation

For list of available endpoints and how to use them, refer to the **[API Setup Guide](../docs/api-setup.md)**.
