<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>


## Project Structure

The project follows a Domain-Driven Design (DDD) inspired structure within Laravel.

```
app/
├── Domain/                 # Core Business Logic & Models
│   ├── Auth/               
│   │   ├── Actions/        # Single Responsibility Actions
│   │   ├── DataTransferObjects/ # Strict DTOs
│   │   └── Services/       # Business Logic Services
│   ├── User/
│   │   ├── Models/         # Eloquent Models
│   │   └── ...
│   ├── Wallet/
│   │   ├── Models/
│   │   ├── Actions/
│   │   ├── Services/
│   │   └── DataTransferObjects/
│   └── Currency/
│       └── ...
├── Http/
│   ├── Controllers/        # API Controllers (Thin)
│   ├── Requests/           # Form Requests (Validation)
│   └── Resources/          # API Resources (Transformation)
├── Policies/               # Authorization Policies
└── Providers/              # Service Providers
```

# Wallet Module Architecture Flow

This diagram illustrates the data flow and strict typing implementation in the Wallet Module, demonstrating how the Controller, Action, Service, and Data Layers interact.

## Component Roles

1.  **Controller (`WalletController`)**:
    *   Handles HTTP inputs and validation (`StoreWalletRequest`).
    *   **Crucial Step**: Instantly converts validated array data into a strict `WalletData` DTO.
    *   Delegates the business operation to a specific **Action**.
    *   Formats the final response using `WalletResource`.

2.  **DTO (`WalletData`)**:
    *   Acts as a strict contract for data transfer.
    *   Ensures that strictly typed data streams flow into the Action and Service layers, replacing loose associative arrays.

3.  **Action (`CreateWalletAction`)**:
    *   Follows the Single Responsibility Principle.
    *   Orchestrates the operation by calling the `WalletService`.
    *   Strictly accepts `WalletData` as input.

4.  **Service (`WalletService`)**:
    *   Contains the core business logic (e.g., creating the wallet, handling initial balance transactions).
    *   Interacts directly with Eloquent Models.
    *   Strictly accepts `WalletData` as input.

5.  **Model (`Wallet`)**:
    *   Represents the database table structure.

## Interaction Flow

```mermaid
sequenceDiagram
    participant Client
    participant Controller as WalletController
    participant DTO as WalletData (DTO)
    participant Action as CreateWalletAction
    participant Service as WalletService
    participant Model as Wallet (Model)
    participant DB as Database

    Client->>Controller: POST /api/v1/wallets (JSON)
    Note over Controller: Validates Request (StoreWalletRequest)
    
    Controller->>DTO: fromRequest($validatedData)
    DTO-->>Controller: WalletData Object
    
    Controller->>Action: execute(WalletData $data)
    
    Action->>Service: create(WalletData $data)
    
    Note over Service: Business Logic (e.g. Transactions)
    Service->>Model: Wallet::create(...) / Transaction::create(...)
    Model->>DB: INSERT INTO wallets...
    DB-->>Model: Success
    Model-->>Service: Wallet Instance
    
    Service-->>Action: Wallet Instance
    
    Action-->>Controller: Wallet Instance
    
    Note over Controller: Transform to API Response
    Controller->>Client: WalletResource (JSON Response)
```

## System Design (Component Structure)

This diagram shows the static structure and dependencies between the classes in the Wallet Module.

```mermaid
classDiagram
    direction TB
    
    %% API Layer
    class WalletController {
        +index()
        +store(StoreWalletRequest)
        +show(id)
        +update(UpdateWalletRequest, id)
    }

    class StoreWalletRequest {
        +rules()
        +authorize()
    }
    
    %% Data Transfer Layer
    class WalletData {
        +string name
        +int currency_id
        +bool status
        +float initial_balance
        +fromRequest(array): self
        +toArray(): array
    }

    %% Business Logic Layer (Actions & Services)
    class CreateWalletAction {
        +execute(WalletData): Wallet
    }
    
    class UpdateWalletAction {
        +execute(Wallet, WalletData): Wallet
    }

    class WalletService {
        +create(WalletData): Wallet
        +update(Wallet, WalletData): Wallet
        +listWallets(User)
        +assignUsers(Wallet, array)
    }

    %% Domain Model Layer
    class Wallet {
        +id
        +name
        +currency_id
        +status
        +balance()
        +users()
        +transactions()
    }
    
    class Transaction {
        +id
        +wallet_id
        +amount
        +type
        +reference
    }

    %% Relationships
    WalletController ..> StoreWalletRequest : uses
    WalletController ..> WalletData : creates
    WalletController --> CreateWalletAction : invokes
    WalletController --> UpdateWalletAction : invokes
    
    CreateWalletAction --> WalletService : delegates
    UpdateWalletAction --> WalletService : delegates
    
    WalletService ..> WalletData : reads
    WalletService --> Wallet : manages
    WalletService --> Transaction : creates (initial balance)
    
    Wallet "1" -- "*" Transaction : has
```

## Database Schema (ERD)

Strict database structure for the Wallet Module.

```mermaid
erDiagram
    WALLETS ||--o{ TRANSACTIONS : has
    WALLETS ||--o{ WALLET_USER : "assigned to"
    CURRENCIES ||--o{ WALLETS : "denominated in"
    USERS ||--o{ WALLET_USER : "accesses"

    WALLETS {
        bigint id PK
        string name
        bigint currency_id FK
        boolean status "1=Active, 0=Frozen"
        timestamp created_at
        timestamp updated_at
    }

    TRANSACTIONS {
        bigint id PK
        bigint from_wallet_id FK "nullable"
        bigint to_wallet_id FK "nullable"
        enum type "credit, debit"
        decimal amount "15,2"
        string reference
        timestamp created_at
    }

    CURRENCIES {
        bigint id PK
        string code "USD, EUR"
        string name
        string symbol
        boolean status
    }
```
