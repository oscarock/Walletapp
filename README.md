
# Wallet App

**Wallet App** is a web application designed to manage digital wallets, providing REST and SOAP APIs for seamless integration and efficient wallet operations.

## Table of Contents

- [About](#about)
- [Features](#features)
- [Technologies](#technologies)
- [Installation](#installation)
- [Usage](#usage)
- [API Endpoints](#api-endpoints)
- [Contributing](#contributing)
- [License](#license)
- [Contact](#contact)

## About

Wallet App provides a robust backend solution for managing user wallets, supporting both RESTful and SOAP web services. It is built with PHP and uses Blade templating for the frontend views.

## Features

- REST API for wallet operations
- SOAP API support for legacy integrations
- User-friendly interface with Blade templates
- Secure wallet transactions and management
- Modular and scalable architecture

## Technologies

- PHP
- Laravel Blade (templating engine)
- REST and SOAP web services

## Installation

1. Clone the repository:
    ```bash
    git clone https://github.com/oscarock/wallet-app.git
    cd wallet-app
    ```
2. Install dependencies using Composer:
    ```bash
    composer install
    ```
3. Set up your environment variables:
    ```bash
    cp .env.example .env
    ```
    Configure your database and other settings in the `.env` file.
4. Generate application key:
    ```bash
    php artisan key:generate
    ```
5. Run migrations:
    ```bash
    php artisan migrate
    ```
6. Serve the application:
    ```bash
    php artisan serve
    ```

## Usage

- Access the app via the browser at `http://localhost:8000`.
- Use the REST API endpoints under `/wallet-rest`.
- Use the SOAP API endpoints under `/wallet-soap`.

## API Endpoints

### REST API

| Endpoint               | Method | Description                  |
|------------------------|--------|------------------------------|
| `/wallet-rest/balance` | GET    | Get wallet balance           |
| `/wallet-rest/transfer`| POST   | Transfer funds between wallets|

*(Add more endpoints as per your implementation)*

### SOAP API

- WSDL available at `/wallet-soap?wsdl`
- Supports wallet operations such as balance inquiry, transfer, transaction history.

## Contributing

Contributions are welcome! Please fork the repository and create a pull request with your improvements.

## License

This project is licensed under the MIT License. See the [LICENSE](LICENSE) file for details.

