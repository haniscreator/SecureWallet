# API Setup & Documentation

The backend provides a RESTful API built with Laravel. You can explore and test the API endpoints using Postman.

## Import into Postman

We have provided a comprehensive Postman collection to help you get started quickly.

1. **Locate the Collection File**:
   The Postman collection file is located at `docs/Korporatio_API.json`.

2. **Import**:
   - Open Postman.
   - Click the **"Import"** button in the top left corner.
   - Drag and drop the `docs/Korporatio_API.json` file into the import window, or select it from the file dialog.

3. **Environment Variables**:
   The collection uses environment variables for the base URL and authentication token.
   - **`{{base_url}}`**: Defaults to `http://localhost:8000`.
   - **`{{token}}`**: You will need to login first to get the token.

## Usage Guide

1. **Login**:
   - Open the **Auth > Login** request in the imported collection.
   - Send the request with the default credentials (or your own).
   - Copy the `token` from the response body.

   ![Token](/screenshots/token.png)

2. **Set Token**:
   - Set the `token` variable in your Postman environment or update the collection variable.
   - Alternatively, you can paste the token into the **Authorization** tab (Bearer Token) for protected requests.

3. **Explore Endpoints**:
   - Explore the folders for **Wallet**, **Transactions**, **User**, and **Currency** to see available operations.

## Available Modules

- **Auth**: Login and Logout.
- **Wallet**: Manage wallets, check balances, and update status.
- **Transactions**: View transaction history and wallet transfers.
- **Currency**: Manage supported currencies (Admin).
- **User**: Manage team members (Admin).

![List](/screenshots/list.png)
