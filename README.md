
# Overview
The News Aggregator application is an API based application developed using laravel 11 version, that allows users to input a URL, fetch the website
data (including the title and meta description), capture a snapshot of the website, and navigate through suggestions.
The application uses a service and repository pattern to handle business logic and database interactions.

### Features
- Fetch website data including title and meta description.
- Capture and display a snapshot of the website.
- Edit the title of fetched suggestions.
- Navigate between previous and next suggestions.
- Display a loading overlay while fetching data.
- Proper validation and error handling.

### Libraries Used in Project
- PHP - GuzzleHttp - https://github.com/guzzle/guzzle
- PHP - Browsershot - https://spatie.be/docs/browsershot/v4/introduction
- NODE - Puppeteer https://www.npmjs.com/package/puppeteer

### Project Environments Versions
Below are versions I have used to complete this Task.
- PHP version 8.2
- Composer version 2.6.6
- Laravel version 11.8.0

## Backend Code
For Back-end I have followed the n-layer Architecture in Laravel. I have created a Service and a Repository Layer.
Service Layer Handles the Business Logic whereas Repo layer handles the data.

# Project Setup using Docker
- Build project using docker with below given command.
  - docker compose up --build
- 

# Project Environment Configurations
- Copy the .env.example file to .env
- Go to phpmyadmin/ any database manager and create a new database and update the DB Configurations in .env file.
- Run a command "which node" and add the binary filepath on a new environment variable "NODE_BINARY_FILE_PATH".
- Run a command "which npm" and add the binary filepath on a new environment variable "NPM_BINARY_FILE_PATH".

### Backend: Commands to run to make site Up
Go to project folder and run the following commands.

- composer install (if needed)
- composer dump-autoload
- php artisan migrate:fresh --seed
- php artisan:serve --port=8000
- php artisan storage:link

### Frontend: Commands to run to make site Up
Go to project folder and run the following commands.

- npm install (if needed)

## When Setup is Done
When everything is set Up goto given link "http://localhost:8000/" in your favourite browser.
