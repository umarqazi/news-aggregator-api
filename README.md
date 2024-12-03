# News Aggregator API

The News Aggregator API is a Laravel 11-based application designed to provide a comprehensive solution for managing user authentication, articles, user preferences, and data aggregation. This application adopts a clean architecture with separate service and repository layers for streamlined business logic and database interaction management.

---

## Features

### User Authentication
- Register a new user.
- Login and logout functionality.
- Forgot and reset password workflows.

### Article Management
- Fetch paginated articles.
- Search articles by keywords.
- View article details.

### User Preferences
- Set and retrieve user preferences.
- Generate a personalized news feed based on preferences.

### Data Aggregation
- Daily automated news article fetching using CRON jobs from various sources.

### API Documentation
- Comprehensive API documentation powered by tools like Swagger/OpenAPI.

---

## Libraries and Tools Used
- **[Laravel Sanctum](https://github.com/laravel/sanctum):** For API authentication.
- **[Scramble OpenAPI](https://github.com/dedoc/scramble):** For generating OpenAPI documentation.

---

## Project Environment Versions
The application is developed and tested with the following versions:
- **PHP:** 8.2
- **Composer:** 2.6.6
- **Laravel:** 11.33.2

---

## Architecture
The application follows an n-layer architecture:
- **Service Layer:** Handles business logic.
- **Repository Layer:** Manages database interactions.

This separation ensures a clean and maintainable codebase.

---

## Installation

### Pre-requisites
- Install Docker
- Install Docker Compose

### Setup Instructions
1. Clone the repository:
   ```bash
   git clone git@github.com:umarqazi/news-aggregator-api.git
2. Navigate to the project directory:
    ```bash
   cd news-aggregator-api
3. Copy the environment configuration file:
    ```bash
   cp .env.example .env
4. Update the .env file with the required values, including API keys for news sources.
5. Build and run the application using Docker:

For Docker Compose (v1):
   ```bash
    docker-compose up --build -d
   ```
   For Docker Compose (v2+):
   ```bash
    docker compose up --build -d
   ```
Once the build is complete, the application will be accessible locally.

---

## Accessing the Application

### API Documentation
- Open your browser and navigate to:
  - http://localhost:9001/docs/api

### Phpmyadmin
- Open your browser and navigate to:
  - http://localhost:8080
- Login using the database credentials specified in the .env file.

---

## Feature Testing
To perform the test cases run the below given command with the news-app Container.

With in the news_app container:
```bash
     php artisan test
```
Outside of the news_app container:
```bash
     docker exec -it news_app php artisan test
