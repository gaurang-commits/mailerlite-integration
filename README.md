
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

Start the local development server
```bash
php artisan serve
```

You can now access the server at http://localhost:8000



## Folders

- `app` - Contains all the Eloquent models
- `app/Http/Controllers/Api` - Contains all the api controllers
- `app/Http/Requests/Api` - Contains all the api form requests
- `app/HTTP/Services/MailerLiteService` - Contains MailerLite API integration
- `app/Transformers/SubscriberTransformer` - Contains the required parameters to show on datatable
- `app/Helpers/Helper` - Contains the helper functions
- `config` - Contains all the application configuration files
- `routes` - Contains all the api routes defined in api.php file
- `tests` - Contains all the application tests
- `tests/Feature` - Contains all the api tests
`tests/Dara` - Contains the required API response for faking http requests for testing

## Environment variables
- `MAILERLITE_API_Key` - MailerLite API key
- `MAILERLITE_API_ENDPOINT` - MailerLite API endpoint for performing reqeusts
- `MAILERLITE_VALIDATION_ENDPOINT` - For validation of API	 key

### Testing
```bash
composer test
```