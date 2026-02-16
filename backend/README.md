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

The project follows a **Domain-Driven Design (DDD)** structure to separate concerns and improve maintainability. Code is organized into Modules (Domains) rather than by type.

```
app/
├── Domain/                 # Core Business Logic (Modules)
│   ├── Auth/               # Authentication
│   ├── Currency/           # Currency Management
│   ├── Setting/            # System Settings
│   ├── Transaction/        # Transactions & Transfers
│   ├── User/               # User & Member Management
│   ├── Wallet/             # Wallet & External Wallet Logic
│   └── {Module}/
│       ├── Actions/        # Single-responsibility business actions
│       ├── DataTransferObjects/ # Typed data arrays (DTOs)
│       ├── Models/         # Eloquent Models
│       ├── Requests/       # Form Request Validation
│       ├── Resources/      # JSON Response Transformations
│       └── Services/       # Complex business logic services
├── Http/
│   ├── Controllers/        # Thin API Controllers
│   └── Middleware/         # HTTP Middleware
├── Policies/               # Authorization Policies
└── Providers/              # Service Providers
```

## Testing Coverage

This project includes comprehensive testing coverage using **PHPUnit**:

-   **Unit Tests**: Covers individual service functions and domain logic.
-   **Feature Tests**: Validates end-to-end API flows, including authentication, wallet transfers, and role-based access control.

Test execution is enforced during the Git push process to ensure code quality.

![Build Status](https://github.com/haniscreator/SecureWallet/actions/workflows/backend-ci.yml/badge.svg)


## How It Works

### Architecture Flow

The application processes requests through the following components:

1.  **Requests**: `app/Domain/{Module}/Requests`
    -   Validates incoming HTTP input rules (validation layer).
2.  **Policies**: `app/Policies`
    -   Authorizes the user action (e.g., "Can this user approve this transfer?").
3.  **Controllers**: `app/Http/Controllers`
    -   Accepts the validated request and delegates to Actions or Services.
4.  **DataTransferObjects (DTOs)**: `app/Domain/{Module}/DataTransferObjects`
    -   Carries data between processes in a structured, typed format.
5.  **Actions**: `app/Domain/{Module}/Actions`
    -   Single-task classes that orchestrate the process.
6.  **Services**: `app/Domain/{Module}/Services`
    -   Contains the core business logic and handles database interactions.
7.  **Models**: `app/Domain/{Module}/Models`
    -   Represents database tables and handles relationships.
8.  **Resources**: `app/Domain/{Module}/Resources`
    -   Transforms the resulting data into a standard JSON response format.

For detailed diagrams and architectural deep-dives for Transfers, please see the **[Transfer System Design Documentation](../docs/part-2/transfer-system-design.md)**.

## API Documentation

For the full API definition, please refer to the **[Korporatio API Design](../docs/Korporatio_API.json)** file (Postman Collection).
