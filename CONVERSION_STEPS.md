# Lazzat Laravel Conversion

This Laravel project was converted from the original static HTML/CSS/JS project.

## Step by step

1. Install PHP 8.2+ and Composer.
2. Open this folder in terminal:
   `cd lazzat-laravel`
3. Install PHP dependencies if needed:
   `composer install`
4. Copy the environment file if `.env` is missing:
   `copy .env.example .env`
5. Generate an app key if needed:
   `php artisan key:generate`
6. Start the Laravel server:
   `php artisan serve`
7. Open the URL shown by Laravel, usually `http://127.0.0.1:8000`.

## Laravel routes

- `/` Home
- `/recipes` Recipes
- `/recipe-detail?id=MEAL_ID` Recipe detail
- `/planner` Meal planner
- `/grocery` Grocery list
- `/login` Login
- `/register` Register
- `/dashboard` Dashboard

## Converted files

- Static CSS moved to `public/css/style.css`
- Static JavaScript moved to `public/js/translations.js` and `public/js/navbar.js`
- HTML pages converted to Blade views in `resources/views`
- Page URLs are defined in `routes/web.php`

The app still uses browser localStorage for login/planner/grocery behavior and TheMealDB API for recipes, just like the original static version.