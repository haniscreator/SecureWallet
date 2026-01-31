# SecureWallet - Tech Assignment

This is the SecureWallet project, consisting of a Laravel backend and a Vue 3 frontend.

## Tech Stack

- **Backend**: Laravel 12 (PHP 8.2+)
- **Frontend**: Vue 3 + Vuetify (Node.js LTS)
- **Database**: SQLite (for development/testing)

## Prerequisites

- PHP 8.2 or higher
- Composer
- Node.js & NPM
- Docker & Docker Compose (for Docker deployment)

## Docker Deployment (Recommended)

To run the application using Docker, follow these steps:

1. **Start Services**:
   ```bash
   docker-compose up --build -d
   ```
   This will spin up the backend, frontend, and database services. The database will be automatically seeded with the provided dummy data on the first run.
   - `-d`: Runs the containers in the background (detached mode).
   - `--build`: Rebuilds the images (recommended for first run).

2. **Access Application**:
   - **Frontend**: [http://localhost:3000](http://localhost:3000)
   - **Backend**: [http://localhost:8000](http://localhost:8000)

## Manual Setup Instructions

### Backend

1. Navigate to the backend directory:
   ```bash
   cd backend
   ```
2. Install dependencies:
   ```bash
   composer install
   ```
3. Set up environment:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
4. Run migrations:
   ```bash
   touch database/database.sqlite
   php artisan migrate
   ```
5. Serve the application:
   ```bash
   php artisan serve
   ```

### Frontend

1. Navigate to the frontend directory:
   ```bash
   cd frontend
   ```
2. Install dependencies:
   ```bash
   npm install
   ```
3. Run the development server:
   ```bash
   npm run dev
   ```

## Running Tests

### Backend Tests
```bash
cd backend
php artisan test
```

### Frontend Tests
```bash
cd frontend
npm run test:unit
```

## CI/CD Service

This project uses GitHub Actions for Continuous Integration.
- **Backend Workflow**: Runs Laravel tests on every push/PR to `main`.
- **Frontend Workflow**: Runs Vue unit tests on every push/PR to `main`.
