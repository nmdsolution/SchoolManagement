# Yadiko

Yadiko School Management

### Setup Instructions

Clone the project

```bash
  https://github.com/Kluqs/Yadiko.git
```

Go to the project directory

```bash
  cd yadiko
```

Install dependencies

```bash
  composer install
```

Copy .env File

```bash
  cp .env.example .env
```

Configure ENV Variables

`DB_HOST`

`DB_PORT`

`DB_DATABASE`

`DB_USERNAME`

`DB_PASSWORD`

Run Migrations

```bash
  php artisan migrate
```

Run Database seeder to create Permissions & Roles

```bash
  php artisan db::seed
```

Start the server

```bash
  php artisan serve
```

Default Credentials for Super Admin

```bash
  superadmin@gmail.com
  superadmin
```