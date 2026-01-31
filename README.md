# Secure Business Wallet Management

This repository contains a simplified internal wallet management system for a fintech use case. It provides admin tools for wallet control, transaction visibility, and team access management. The current scope supports one company, with a design that enables future scalability.

![Demo](screenshots/demo.png)

## Tech Stack

- **Backend**: Laravel (latest stable)
- **Frontend**: Vue 3 + Vuetify
- **API Style**: REST (JSON)
- **Auth**: Token-based (Laravel Sanctum preferred)
- **Database**: MySQL

## Demo

- **Live Demo**: [https://secure-wallet-jet.vercel.app/](https://secure-wallet-jet.vercel.app/)

## User Access for Testing

You can use the following credentials for both local and live environments:

Admin Account
- **Email**: `admin@gmail.com`
- **Password**: `12345678`

User Account
- **Email**: `euuser@gmail.com`
- **Password**: `12345678`

## Docker (Up & Running on Local)

As an industry best practice, this project comes pre-configured with Docker for easy development for both backend and frontend.

For detailed setup instructions, including prerequisites, commands, and troubleshooting, please refer to:
👉 **[Docker Setup Guide](docs/docker-setup.md)**

## Documentation & Architecture

I have provided detailed documentation for each part of the system:

### 1. Backend
- **[Backend Readme](backend/README.md)**: Detailed information about the backend technology, project structure, and how it works.

### 2. Frontend
- **[Frontend Readme](frontend/README.md)**: Detailed information about the frontend technology, project structure, and how it works.

### 3. API Documentation
- **[API Setup](docs/api-setup.md)**: Instructions on how to import the Postman collection and explore available APIs.

### 4. System Design
- **[System Design Overview](docs/system-design.md)**: Sequence diagrams and class diagrams explaining the core architecture.

### 5. Database Schema
- **[ER Diagram](docs/er-diagram.md)**: Entity Relationship Diagram showing the database structure.
