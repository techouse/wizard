<div align="center">
    <img width="200" height="200" src="resources/images/logo.svg">
</div>

# Wizard

## Setup

```bash
composer install
php artisan migrate
php artisan key:generate --ansi
npm ci
```

**DO NOT YET BUILD THE JAVASCRIPT BUNDLES!**

## Passport Configuration

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
npm run prod
```
