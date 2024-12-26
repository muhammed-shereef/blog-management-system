# blog-management-system
A simple Blog Management System built with Laravel

# blog-management-system
A simple Blog Management System built with Laravel


## Requirements
- PHP >= 8.0
- Composer
- MySQL 
- Laravel 11

## Setup Instructions

1. **Clone the repository**:

    git clone https://github.com/muhammed-shereef/blog-management-system.git



2. **Install dependencies** using Composer:

    ```bash
    composer install
    ```

3. **Set up the `.env` file**:
    - Copy the `.env.example` file to `.env`:
    
    ```bash
    cp .env.example .env
    ```

    - Update the `.env` file with your database credentials 
    DB_CONNECTION=mysql
 DB_HOST=127.0.0.1
 DB_PORT=3306
 DB_DATABASE=db_blog_management_system
 DB_USERNAME=root
 DB_PASSWORD=

4. **Generate application key**:

    ```bash
    php artisan key:generate
    
    ```

5. **Run migrations** to create the necessary database tables:

    ```bash
    php artisan migrate
    ```

6. **Serve the application** locally:

    ```bash
    php artisan serve
    npn install and npm run dev
    php artisan storage:link
    ```

    This will start the application at `http://127.0.0.1:8000`.


## Technologies Used
- **Laravel** 11
- **MySQL** (or your preferred database)
- **Blade** (for templating)
- **CKEditor/TinyMCE** (for rich text editing)
