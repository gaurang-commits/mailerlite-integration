
 <img src="https://developers.mailerlite.com/logo.svg" width="20%"/>

# Laravel Mailerlite Integration

## Getting started

### Installation
Clone the repository
```bash
git clone https://github.com/gaurang-commits/mailerlite-integration.git .
```
Install all the dependencies using composer

```bash
composer install
```

Copy the example env file and make the required configuration changes in the .env file

```bash
cp .env.example .env
```

Generate a new application key
```bash

php artisan key:generate
```
Import the database dump (**Set the database connection in .env before migrating**)

```sql
mysql -u username -p mailerlite < mailerlite.sql
```

** SET MAILERLITE_API_KEY in .env **



```bash
php artisan key:generate
```

Start the local development server
```bash
php artisan serve
```

You can now access the server at http://localhost:8000

**TL;DR command list**

    git clone https://github.com/gaurang-commits/mailerlite-integration.git .
    composer install
    cp .env.example .env
    php artisan key:generate

## Folders

- `app` - Contains all the Eloquent models
- `app/Http/Controllers/Api` - Contains all the api controllers
- `app/Http/Requests/Api` - Contains all the api form requests
- `app/HTTP/Services/MailerLiteService` - Contains MailerLite API integration
- `app/Transformers/SubscriberTransformer` - Contains the required parameters to show on datatable
- `app/Helpers/Helper` - Contains the helper functions
- `tests/Data` - Contains the required API response for faking http requests for testing

## Environment variables
- `MAILERLITE_API_KEY` - MailerLite API key
- `MAILERLITE_API_ENDPOINT` - MailerLite API endpoint for performing reqeusts
- `MAILERLITE_VALIDATION_ENDPOINT` - For validation of API	 key

### Testing
```bash
composer test
```