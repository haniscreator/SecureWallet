# Frontend - Secure Wallet Dashboard

![Build Status](https://github.com/haniscreator/SecureWallet/actions/workflows/frontend-ci.yml/badge.svg)

## Tech Stack

- **Framework**: Vue 3 (Composition API)
- **Tooling**: Vite
- **UI Component Library**: Vuetify 3
- **State Management**: Pinia
- **Language**: TypeScript
- **Testing**: Vitest

## Up & Running (Local)

If you are not using Docker, you can run the frontend locally:

1. **Install Dependencies**:
   ```bash
   npm install
   ```
2. **Setup Environment**:
   Ensure the backend is running at `http://localhost:8000`.
   Create a `.env` file if necessary to configure the API base URL.
   
3. **Run Development Server**:
   ```bash
   npm run dev
   ```
   Access the dashboard at `http://localhost:5173` (or the port shown in your terminal).

## Project Structure

The frontend is organized by modules to keep related code together.

```
src/
├── assets/             # Static assets (images, global css)
├── modules/            # Feature-based modules
│   ├── Auth/           # Login views and logic
│   ├── Currency/       # Currency Management
│   ├── Setting/        # System Settings
│   ├── Transaction/    # Transaction & Transfer logic
│   ├── User/           # User & Member Management
│   └── Wallet/         # Dashboard, Wallet Details, Transaction List
├── shared/             # Shared components and utilities
│   ├── components/     # Reusable UI components
│   ├── http/           # API client (Axios wrapper)
│   └── ...
├── plugins/            # Vue plugins (Vuetify, Pinia, Router)
├── router/             # Vue Router configuration
├── styles/             # Global styles (SCSS/CSS)
├── App.vue             # Main App Component
└── main.ts             # Entry point
```

## Introduce Composables

We use **Composables** to encapsulate and reuse stateful logic, particularly in the **Transaction** / **Transfer** modules. Instead of mixing complex business logic directly into the Vue components, we extract it into composable functions (e.g., `useTransferForm.ts`). 

This approach allows us to:
-   **Reuse Logic**: Share validation, API calls, and state management across multiple components.
-   **Test Implementation**: Isolate logic for easier unit testing.
-   **Clean Components**: Keep UI components focused on rendering and user interaction.

## Testing Coverage

This frontend includes UI tests for dashboard widgets using Vitest.
Test execution is enforced during the Git push process to ensure UI stability. The application is ready for CI/CD pipelines and server deployment.

![Build Status](https://github.com/haniscreator/SecureWallet/actions/workflows/frontend-ci.yml/badge.svg)


## How It Works

- **Views**: Each page corresponds to a View component in its respective module.
- **API Integration**: API calls are centralized in module-specific API files or stores.
- **UI Framework**: extensive use of Vuetify components for a responsive and modern design.

For more details on the overall system architecture, check the **[System Design Documentation](../docs/system-design.md)**.
