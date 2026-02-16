# Transfer System Architecture

This document outlines the high-level architecture for the transfer feature, including the interaction between frontend components, backend services, and the database.

## System Architecture Diagram

```mermaid
graph TD
    subgraph Frontend [Vue.js Frontend]
        W_UI[Wallet Details UI]
        T_Form[Transfer Form]
        Approvals[Pending Approvals UI]
    end

    subgraph Backend [Laravel API]
        TC[TransferController]
        TS[TransferService]
        
        %% Eloquent Models are used directly
        M_User[User Model]
        M_Wallet[Wallet Model]
        M_Ext[ExternalWallet Model]
        M_Tx[Transaction Model]
        M_Set[Setting Model]

        subgraph Future [Future Expansion]
            FX[FX Service]:::future
        end
    end

    subgraph Database [MySQL / SQLite]
        Users_T[Users Table]
        Wallets_T[Wallets Table]
        Tx_T[Transactions Table]
        Ext_T[ExternalWallets Table]
        Set_T[Settings Table]
    end

    %% Interactions
    W_UI --> T_Form
    T_Form -- POST /transfers --> TC
    Approvals -- POST /transfers/{id}/approve --> TC
    
    TC --> TS
    
    %% Service Logic
    TS -- Check Role --> M_User
    TS -- Validate Source/Target --> M_Wallet
    TS -- Resolve External --> M_Ext
    TS -- Check Limits --> M_Set
    TS -- Create/Update --> M_Tx
    
    %% Future FX
    TS -.-> FX
    FX -.-> M_Set
    
    %% Model to Database
    M_User <--> Users_T
    M_Wallet <--> Wallets_T
    M_Ext <--> Ext_T
    M_Tx <--> Tx_T
    M_Set <--> Set_T
    
    classDef future fill:#f9f,stroke:#333,stroke-dasharray: 5 5;
```

## Component Description

### Frontend
- **Transfer Form**: Handles user input for internal and external transfers.
- **Pending Approvals UI**: Interface for managers to view and approve/reject pending transactions.

### Backend
- **TransferController**: Handles HTTP requests for initiating, approving, and rejecting transfers.
- **TransferService**: Contains the core business logic for transfers, including validation, balance checks, limit enforcement, and transaction creation.
- **Models**: Eloquent models (`Wallet`, `Transaction`, `ExternalWallet`, `User`, `Setting`) are used for database interaction.

### Database
- Stores user data, wallet balances, transaction records, external wallet details, and system settings.
