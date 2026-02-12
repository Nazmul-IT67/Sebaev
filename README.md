<p align="center">
    <h1 align="center">Bebloops</h1>
</p>

## Installation 🤷‍♂

To Install & Run This Project You Have To Follow the following Steps:

```sh
Open Terminal on project directory
```

```sh
composer update
```

```sh
npm install & npm run dev
```

Open your `.env` file and change the database name (`DB_DATABASE`) to whatever you have, the username (`DB_USERNAME`) and password (`DB_PASSWORD`) field correspond to your configuration

```sh
php artisan key:generate
```

```sh
php artisan migrate:fresh --seed
```

```sh
php artisan optimize:clear
```

```sh
php artisan queue:work --queue=messages,default
```

```sh
php artisan serve
```

Setup Reverb, Stripe, Jwt, Mail, Google Cloud configurations on .env file.

For Admin Login `http://127.0.0.1:8000/admin` <br>
Admin gmail = `admin@admin.com` & password = `12345678`

For User Login `http://127.0.0.1:8000/admin` <br>
Admin gmail = `user@user.com` & password = `12345678`
