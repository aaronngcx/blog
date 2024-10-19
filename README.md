# Laravel Inertia Blog

## Introduction

This project is a blog built using Laravel and Inertia.js, combining the power of Laravel for backend development with Vue.js for a modern frontend experience. Inertia.js allows for a seamless interaction between the server-side and client-side, making it easy to build single-page applications without losing the benefits of server-side routing.

## Server Requirements

Before you begin, ensure you have met the following requirements:

- PHP >= 8.2
- Composer
- Node.js >= 18
- NPM or Yarn
- A web server (e.g., Apache, Nginx)
- A database server (e.g., MySQL, PostgreSQL)

## Installation Guide

Follow these steps to set up your local development environment:

1. Clone the Repository:
   git clone https://github.com/aaronngcx/blog.git

2. composer install

3. cp .env.example .env

4. create database

5. php artisan key:generate

6. php artisan migrate

7. php artisan db:seed --class=UserSeeder

8. php artisan db:seed --class=StateSeeder

9 .php artisan storage:link

10. npm run build

11. php artisan serve

Sample User Credentials
After seeding the database, you can log in with the following credentials:

Email: admin@mail.com
Password: password


