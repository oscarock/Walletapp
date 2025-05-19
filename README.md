# Wallet App 🪙

[![PHP Version](https://img.shields.io/badge/php-%3E%3D8.1-8892BF.svg)](https://php.net)
[![Laravel Version](https://img.shields.io/badge/laravel-11.x-FF2D20.svg)](https://laravel.com)
[![Lumen Version](https://img.shields.io/badge/lumen-10.x-E74430.svg)](https://lumen.laravel.com)
[![License](https://img.shields.io/badge/license-MIT-green.svg)](LICENSE)

**Wallet App** is a web application designed to manage digital wallets. It features a REST API that acts as a client to a robust SOAP API, providing seamless integration and efficient wallet operations.

## Table of Contents

- [About](#about)
- [Architecture Overview](#architecture-overview)
- [Features](#features)
- [Technologies](#technologies)
- [Prerequisites](#prerequisites)
- [Installation](#installation)
  - [Wallet SOAP Service (Laravel)](#wallet-soap-service-laravel)
  - [Wallet REST Service (Lumen)](#wallet-rest-service-lumen)
- [Running the Applications](#running-the-applications)
  - [1. Start the SOAP Service](#1-start-the-soap-service)
  - [2. Start the REST Service](#2-start-the-rest-service)
- [API Endpoints](#api-endpoints)
  - [REST API (`wallet-rest`)](#rest-api-wallet-rest)
  - [SOAP API (`wallet-soap`)](#soap-api-wallet-soap)
- [Contributing](#contributing)
- [License](#license)
- [Contact](#contact)

## About

Wallet App provides a backend solution for managing user wallets. It exposes a user-friendly REST API which, under the hood, communicates with a comprehensive SOAP API responsible for the core wallet logic and data management. This setup allows modern clients to interact via REST while leveraging a potentially pre-existing or specialized SOAP backend.

## Architecture Overview

The application is composed of two main services:

1.  **`wallet-soap/`**: A Laravel-based application that provides the core SOAP web service for all wallet operations. It handles business logic, data validation, and database interactions.
2.  **`wallet-rest/`**: A Lumen-based application that provides a RESTful API. This service acts as a client to the `wallet-soap` service, translating REST requests into SOAP calls and formatting SOAP responses back into JSON for the end-user.

```
+-----------------+     +----------------------+     +--------------------+
|   End User /    | --> |  wallet-rest (Lumen) | --> | wallet-soap (Laravel)|
| Client App (JSON)|     |  (REST API Client)   |     | (SOAP API Server)  |
+-----------------+     +----------------------+     +--------------------+
                                                         |
                                                         V
                                                     +----------+
                                                     | Database |
                                                     +----------+
```

## Features

-   REST API for easy integration with modern clients.
-   SOAP API for robust backend wallet operations.
-   Clear separation of concerns between REST interface and SOAP core logic.
-   User registration and wallet creation.
-   Wallet balance top-up.
-   Secure payment processing with token-based confirmation.
-   Balance inquiry.
-   Modular and scalable architecture.

## Technologies

-   **`wallet-soap` (SOAP Service Backend):**
    -   PHP 8.1+
    -   Laravel 11.x
    -   SOAP Server
-   **`wallet-rest` (REST API Client):**
    -   PHP 8.1+
    -   Lumen 10.x
-   **Database:** MySQL / MariaDB (configurable in `.env`)
-   **Templating (for `wallet-soap` welcome page):** Laravel Blade

## Prerequisites

Before you begin, ensure you have the following installed:
-   PHP (version 8.1 or higher)
-   Composer
-   A database server (e.g., MySQL, MariaDB)

## Installation

Follow these steps to set up both services:

### Wallet SOAP Service (Laravel)

This service handles the core wallet logic.

1.  **Navigate to the `wallet-soap` directory:**
    ```bash
    cd wallet-soap
    ```
2.  **Install PHP dependencies:**
    ```bash
    composer install
    ```
3.  **Create your environment file:**
    ```bash
    cp .env.example .env
    ```
4.  **Configure your `.env` file:**
    *   Set up your database connection details (`DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`).
    *   Ensure `APP_URL` is set correctly (e.g., `APP_URL=http://localhost:8000`).
5.  **Generate application key:**
    ```bash
    php artisan key:generate
    ```
6.  **Run database migrations:**
    ```bash
    php artisan migrate
    ```

### Wallet REST Service (Lumen)

This service provides the RESTful interface.

1.  **Navigate to the `wallet-rest` directory:**
    ```bash
    cd wallet-rest 
    # If you were in wallet-soap, use: cd ../wallet-rest
    ```
2.  **Install PHP dependencies:**
    ```bash
    composer install
    ```
3.  **Create your environment file:**
    ```bash
    cp .env.example .env
    ```
4.  **Configure your `.env` file:**
    *   The most important setting here is `SOAP_URL`. By default, the `WalletController.php` in `wallet-rest` points to `http://127.0.0.1:8000/api/wsdl`. Ensure this matches where your `wallet-soap` service will be running and serving its WSDL. The actual SOAP endpoint used by the client will be `http://127.0.0.1:8000/api/soap-wallet` as per the WSDL's service port address.
    *   Lumen's `APP_KEY` is typically set directly in the `.env` file. You can generate one using an online tool or copy it from the `wallet-soap` `.env` if you wish, though it's less critical for this service as it's primarily a client.

## Running the Applications

You need to run both services, preferably starting the SOAP service first.

### 1. Start the SOAP Service (`wallet-soap`)

This service needs to be accessible for the REST service to function.

```bash
cd wallet-soap
php artisan serve --port=8000
```
This will typically start the SOAP service at `http://localhost:8000`. The WSDL will be available at `http://localhost:8000/api/wsdl`.

### 2. Start the REST Service (`wallet-rest`)

Once the SOAP service is running, you can start the REST service.

```bash
cd wallet-rest
php -S localhost:8001 -t public
```
This will typically start the REST service at `http://localhost:8001`.

## API Endpoints

### REST API (`wallet-rest`)

All REST endpoints are prefixed with the base URL of the `wallet-rest` service (e.g., `http://localhost:8001`).

| Endpoint             | Method | Description                                  | Request Body Parameters                               |
|----------------------|--------|----------------------------------------------|-------------------------------------------------------|
| `/registro-cliente`  | POST   | Registers a new client and creates a wallet. | `document`, `name`, `email`, `phone`                  |
| `/recargar-billetera`| POST   | Tops up a client's wallet balance.           | `document`, `phone`, `valor` (amount)                 |
| `/pagar`             | POST   | Initiates a payment from a client's wallet.  | `document`, `amount`                                  |
| `/confirmar-pago`    | POST   | Confirms a pending payment using a token.    | `sessionId`, `token`                                  |
| `/consultar-saldo`   | POST   | Retrieves the current balance of a wallet.   | `document`, `phone`                                   |

**Example `curl` for registering a client:**
```bash
curl -X POST http://localhost:8001/registro-cliente \
-H "Content-Type: application/json" \
-d '{
    "document": "123456789",
    "name": "John Doe",
    "email": "john.doe@example.com",
    "phone": "5551234567"
}'
```

### SOAP API (`wallet-soap`)

-   **WSDL:** Available at `http://localhost:8000/api/wsdl` (once `wallet-soap` is running).
-   **Endpoint:** `http://localhost:8000/api/soap-wallet`

**Operations:**

-   `registroCliente(string $document, string $name, string $email, string $phone)`
    -   Registers a new client.
    -   Returns: `success` (boolean), `cod` (string), `message` (string).
-   `recargaBilletera(string $document, string $phone, float $valor)`
    -   Adds funds to a client's wallet.
    -   Returns: `code` (int), `message` (string). (Note: WSDL says `code`, `WalletController` expects `success`, `cod`, `message`. The actual SOAP server returns `success`, `cod`, `message` for this one too, based on its `response` method.)
-   `pagar(string $document, float $amount)`
    -   Initiates a payment, returns a session ID for confirmation.
    -   Returns: `cod` (int), `message` (string), `session_id` (string).
-   `confirmarPago(string $sessionId, string $token)`
    -   Confirms a payment using the session ID and a token.
    -   Returns: `cod` (int), `message` (string).
-   `consultarSaldo(string $document, string $phone)`
    -   Checks the wallet balance.
    -   Returns: `cod` (int), `balance` (float).

*(Note: There's a slight discrepancy in the `recargaBilleteraResponse` definition in the WSDL (`code`, `message`) versus the common response structure (`success`, `cod`, `message`) used by the `WalletSoapServer`'s internal `response` method and expected by the `wallet-rest` client. The implementation uses `success`, `cod`, `message`.)*

## Contributing

Contributions are welcome! Please fork the repository and create a pull request with your improvements.
1.  Fork the Project
2.  Create your Feature Branch (`git checkout -b feature/AmazingFeature`)
3.  Commit your Changes (`git commit -m 'Add some AmazingFeature'`)
4.  Push to the Branch (`git push origin feature/AmazingFeature`)
5.  Open a Pull Request

## License

This project is licensed under the MIT License. See the [`LICENSE`](LICENSE) file for details (assuming a `LICENSE` file exists or will be added).

## Contact

Project Link: [https://github.com/oscarock/wallet-app](https://github.com/oscarock/wallet-app) (Replace with your actual project link if different)

Your Name / Organization - your.email@example.com
