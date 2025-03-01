# Laravel Project

<p align="center">
    <a href="https://laravel.com" target="_blank">
        <img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo">
    </a>
</p>

## About the Project
This project is a Laravel-based API that provides user authentication, project management, timesheet tracking, and dynamic attributes using an EAV (Entity-Attribute-Value) system.

## Features
- User authentication (register, login, logout)
- Project CRUD operations
- Timesheet management
- Dynamic attributes with an EAV system
- Flexible filtering for projects

---

## Installation

### **Step 1: Clone the Repository**
```bash
 git clone https://github.com/your-repo/project-name.git
 cd project-name
```

### **Step 2: Install Dependencies**
```bash
composer install
```

### **Step 3: Copy the Environment File**
```bash
cp .env.example .env
```

### **Step 4: Configure Environment**
Update the `.env` file with your database details:
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_database_user
DB_PASSWORD=your_database_password
```

### **Step 5: Generate Application Key**
```bash
php artisan key:generate
```

### **Step 6: Run Migrations and Seed Database**
```bash
php artisan migrate --seed
```

### **Step 7: Install Passport for API Authentication**
```bash
php artisan passport:install
```

### **Step 8: Serve the Application**
```bash
php artisan serve
```
Your API will be available at `http://127.0.0.1:8000`

---

## API Documentation

### **Postman Collection**
You can find the Postman collection for testing API endpoints here:
[Postman Collection](https://www.postman.com/your-postman-link)

---

## Running Tests
To run tests for the project, execute:
```bash
php artisan test
```

---

## Migrations & Seeders
The project includes database migrations and seeders.

### **Run Migrations Only**
```bash
php artisan migrate
```

### **Run Seeders**
```bash
php artisan db:seed
```
The seeders will create sample users, projects, timesheets, and attributes.

---

## License
This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

