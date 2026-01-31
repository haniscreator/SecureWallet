# System Design & Architecture

This document outlines the architectural design of the Secure Wallet application, focusing on the Wallet Module. The system follows a Domain-Driven Design (DDD) inspired structure.

## Overview

The system is designed to be modular, scalable, and strictly typed. It separates concerns into Controllers, Actions, Services, Data Transfer Objects (DTOs), and Models.

## Wallet Module Architecture Flow

This diagram illustrates the data flow and strict typing implementation in the Wallet Module.

### Interaction Flow (Sequence Diagram)

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

### Component Roles

1.  **Controller (`WalletController`)**: Handles HTTP requests, validation, and response formatting. Instantly converts input to DTOs.
2.  **DTO (`WalletData`)**: Ensures strict data contracts between layers, replacing loose associative arrays.
3.  **Action (`CreateWalletAction`)**: Single Responsibility class that orchestrates the business operation.
4.  **Service (`WalletService`)**: Contains the core business logic and interacts with Eloquent Models.
5.  **Model (`Wallet`)**: Represents the database table and relationships.

## Class Structure (Class Diagram)

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
        +updateStatus(UpdateWalletStatusRequest, id)
        +assignUser(AssignUserToWalletRequest, id)
    }

    class StoreWalletRequest {
        +rules()
        +authorize()
    }

    class UpdateWalletStatusRequest {
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

    class UpdateWalletStatusAction {
        +execute(Wallet, bool): Wallet
    }

    class AssignWalletAction {
        +execute(Wallet, array): void
    }

    class WalletService {
        +create(WalletData): Wallet
        +update(Wallet, WalletData): Wallet
        +updateStatus(Wallet, bool): Wallet
        +listWallets(User, array)
        +getWallet(Wallet)
        +assignUsers(Wallet, array)
    }

    %% Domain Model Layer
    class Wallet {
        +id
        +name
        +currency_id
        +status
        +balance (accessor)
        +users()
        +incomingTransactions()
        +outgoingTransactions()
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
    WalletController ..> UpdateWalletStatusRequest : uses
    WalletController ..> WalletData : creates
    WalletController --> CreateWalletAction : invokes
    WalletController --> UpdateWalletAction : invokes
    WalletController --> UpdateWalletStatusAction : invokes
    WalletController --> AssignWalletAction : invokes
    
    CreateWalletAction --> WalletService : delegates
    UpdateWalletAction --> WalletService : delegates
    UpdateWalletStatusAction --> WalletService : delegates
    AssignWalletAction --> WalletService : delegates
    
    WalletService ..> WalletData : reads
    WalletService --> Wallet : manages
    WalletService --> Transaction : creates (initial balance)
    
    Wallet "1" -- "*" Transaction : has
```
