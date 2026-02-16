# Transfer Sequence Diagram

This document illustrates the sequence of interactions between the User, Frontend, Backend API, Database, and Manager during a fund transfer.

## Sequence Diagram

```mermaid
sequenceDiagram
    actor User
    participant FE as Frontend
    participant API as Backend API
    participant DB as Database
    actor Mgr as Manager

    User->>FE: Init Transfer (Amount, Ref)
    FE->>API: POST /transfers
    API->>DB: Check Wallets (Frozen?)
    API->>DB: Check Balance (Reserved?)
    API->>DB: Get Limit Setting
    
    alt Amount <= Limit
        API->>DB: Create Transaction (COMPLETED)
        API->>DB: Update Balances
        API-->>FE: Success (Executed)
    else Amount > Limit
        API->>DB: Create Transaction (PENDING)
        API-->>FE: Success (Pending Approval)
        
        Mgr->>FE: View Pending Approvals
        FE->>API: POST /transactions/search {status: pending}
        API-->>FE: List Pending Transactions
        
        Mgr->>FE: Approve Transaction
        FE->>API: POST /transfers/{id}/approve
        API->>DB: Re-check Source Wallet (Frozen?)
        
        alt Source Frozen
            API-->>FE: Error (Wallet Suspended)
        else Source Active
            API->>DB: Update Balances
            API->>DB: Update Status (COMPLETED)
            API-->>FE: Success (Approved)
        end
    end
```

## Detailed Steps

1.  **Initiation**: The user submits a transfer request via the Frontend.
2.  **API Processing**: The Backend API (`TransferController`) receives the request.
3.  **Validation**: `TransferService` validates the source wallet (not frozen), checks for sufficient available balance, and retrieves the global transfer limit setting.
4.  **Auto-Approval**: If the amount is within the limit, the transaction is immediately marked as `COMPLETED`, balances are updated, and success is returned.
5.  **Pending Approval**: If the amount exceeds the limit, the transaction is created with `PENDING` status.
6.  **Manager Review**: A Manager views pending transactions (via `POST /transactions/search`).
7.  **Approval Action**: The Manager approves a specific transaction (`POST /transfers/{id}/approve`).
8.  **Re-Validation**: The system *re-checks* if the source wallet is frozen to ensure compliance.
9.  **Completion**: If active, the transaction is `COMPLETED`, balances are updated, and the approval is recorded.
