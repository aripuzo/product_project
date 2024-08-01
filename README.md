# Symfony RESTful API

## Project Setup

### Prerequisites

- Docker
- Docker Compose

### Installation

1. Clone the repository:
    ```bash
    git clone https://github.com/your-repo/symfony-rest-api.git
    cd symfony-rest-api
    ```

2. Build and start the Docker containers:
    ```bash
    docker-compose up --build
    ```

3. Run database migrations:
    ```bash
    docker-compose exec php bin/console doctrine:migrations:migrate
    ```

## API Documentation

### Endpoints

- `GET /api/products` - List all products.
- `POST /api/products` - Create a new product.
- `GET /api/products/{id}` - Get details of a single product.
- `PUT /api/products/{id}` - Update an existing product.
- `DELETE /api/products/{id}` - Delete a product.

### Request and Response Examples

#### GET /api/products

Request:
```http
GET /api/products HTTP/1.1
Host: localhost:9000
Authorization: Bearer your_jwt_token# product_project
