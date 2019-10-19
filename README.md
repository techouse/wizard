<div align="center">
    <img width="150" height="150" src="resources/images/laravel.svg">
    <strong style="font-size: 90px">+</strong>
    <img width="150" height="150" src="resources/images/vuejs.svg">
    <strong style="font-size: 90px">+</strong>
    <img width="150" height="150" src="resources/images/elementui.svg">
    <strong style="font-size: 90px">=</strong>
    <img width="150" height="150" src="resources/images/logo.svg">
</div>

# :tophat: Wizard :rabbit:

## :construction_worker: Setup

```bash
composer install
php artisan migrate
php artisan key:generate --ansi
npm ci
```

**DO NOT YET BUILD THE JAVASCRIPT BUNDLES!**

## :passport_control: Passport Configuration

In order to get Laravel Passport running you need to do a few steps:

- run these commands
```bash
php artisan passport:keys
php artisan passport:client --password
```

- run this SQL query:
```sql
SELECT id     AS API_CLIENT_ID,
       secret AS API_CLIENT_SECRET
FROM oauth_clients
WHERE password_client = 1
AND revoked = 0
```

- edit the `.env` file and copy the values from the SQL query above into these corresponding fields:
```dotenv
API_CLIENT_ID=
API_CLIENT_SECRET=
```

- only **AFTER** you have done the 3 steps above you can run:
```bash
npm run dev
```

## :crystal_ball: CRUD Wizard

In order to speed up your workflow I wrote a small CRUD wizard.

Say you want to create a CRUD endpoint for the model named `Animal`

```bash
php artisan make:migration create_animals_table --create=animals
php artisan wizard:crud Animal
```

The wizard will then create these PHP files:

- `app/Animal.php`
- `app/Http/Resources/AnimalResource.php`
- `app/Http/Resources/AnimalsResource.php`
- `app/Policies/AnimalPolicy.php`
- `app/Http/Controllers/Api/AnimalController.php`

You will you have to register the policy in `app/Providers/AuthServiceProvider.php` and register
the `ApiResource` routes inside `routes/api.php`.

In addition the wizard will also create JavaScript resources:

 - `resources/js/models/Animal.js`
 - `resources/js/store/modules/animal.js`
 - `resources/js/router/routes/animals.js`
 - `resources/js/pages/Animals/create.vue`
 - `resources/js/pages/Animals/edit.vue`
 - `resources/js/pages/Animals/index.vue`
 - `resources/js/pages/Animals/list.vue`

You will then have to register the Vue router routes inside `/resources/js/router/index.js`.

You might also want to make a menu entry in `resources/js/pages/Admin.vue`.
