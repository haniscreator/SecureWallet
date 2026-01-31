# Docker Setup Guide

This project is pre-configured with Docker to provide a consistent development environment for both the backend (Laravel) and frontend (Vue 3).

## Prerequisites

Before getting started, ensure you have the following installed on your machine:

- **Docker**: [Download Docker Desktop](https://www.docker.com/products/docker-desktop)
- **Docker Compose**: Included with Docker Desktop.

## Getting Started

### 1. Build and Start Containers

Run the following command in the root directory of the project to build and start the application services:

```bash
docker-compose up --build -d
```

- `--build`: Rebuilds the images (useful if you've made changes to the Dockerfile).
- `-d`: Runs the containers in detached mode (in the background).

### 2. Verify Running Deployment

Once the containers are up, you can access the application at the following URLs:

- **Frontend**: [http://localhost:3000](http://localhost:3000)
- **Backend API**: [http://localhost:8000](http://localhost:8000)

### 3. Login Credentials

You can use the same login credentials for both development and testing:

Admin Account
- **Email**: `admin@gmail.com`
- **Password**: `12345678`

User Account
- **Email**: `euuser@gmail.com`
- **Password**: `12345678`

## Useful Commands

### Stop Containers
To stop and remove the containers, networks, and volumes:
```bash
docker-compose down
```

### View Logs
To view logs for all services:
```bash
docker-compose logs -f
```

### Rebuild and Restart
If you make changes and need to rebuild:
```bash
docker-compose up --build -d
```

