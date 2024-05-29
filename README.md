

<p align="center">
    <p align="center">
        <a href="https://github.com/riley19280/code-stencil/actions"><img alt="GitHub Workflow Status (master)" src="https://img.shields.io/github/actions/workflow/status/riley19280/persistent-seeders/run-tests.yml?branch=main&label=Tests"></a>
        <a href="https://packagist.org/packages/riley19280/persistent-seeders"><img alt="Total Downloads" src="https://img.shields.io/packagist/dt/riley19280/persistent-seeders"></a>
        <a href="https://packagist.org/packages/riley19280/persistent-seeders"><img alt="Latest Version" src="https://img.shields.io/packagist/v/riley19280/persistent-seeders"></a>
        <a href="https://packagist.org/packages/riley19280/persistent-seeders"><img alt="License" src="https://img.shields.io/packagist/l/riley19280/persistent-seeders"></a>
    </p>
</p>


# Persistent Seeders

Persistent Seeders allow you to easily maintain your seeded data in a central location, and ensures that your data only gets inserted once.

A prime use case for this is for Roles. At the start of an application, you probably have a few roles that you want to support

```php
class RoleSeeder extends PersistentSeeder {
    #[SeederId('6f7b4e00-97d9-4cb0-8a6b-c092e73755e3')]
    function initialRoles() {
        Role::create(['name' => 'Admin']);
        Role::create(['name' => 'User']);
    }
}
```

As development continues, you realize that you need more roles. The usual process for this would be to update your regular seeder, 
and also manually write a migration to insert the new role, and then run it in production. However, with Persistent Seeders this entire process can be automated. 
All you need to do is add a new function in the **same** file! It now looks like this:

```php
class RoleSeeder extends PersistentSeeder {
    #[SeederId('6f7b4e00-97d9-4cb0-8a6b-c092e73755e3')]
    function initialRoles() {
        Role::create(['name' => 'Admin']);
        Role::create(['name' => 'User']);
    }
    
    #[SeederId('ef97efe9-40bd-4948-a1cd-feeb8fb1b27f')]
    function managerRole() {
        Role::create(['name' => 'Manager']);
    }
}
```

When the seeder runs again, only the new `Manager` role will be created. No messing with migrations, no searching for other roles that exist. 
Just a straightforward understanding of your applications' data.

## Installation

You can install the package via composer:

```bash
composer require riley19280/persistent-seeders
```

And then publish the migrations

```php
php artisan vendor:publish --tag=persistent-seeder-migrations
```

## Usage

Create a new database seeder and extend from `PersistentSeeders\PersistentSeeder`.

Then create your seed function, and add the `SeederId` attribute to the method.
This attribute marks the method as a seeder, and relies on the uuid passed to it to prevent duplicate runs. 
If this id changes, the function will be run again.

```bash
php artisan make:seeder
```

```php
class RoleSeeder extends PersistentSeeder {
    #[SeederId('6f7b4e00-97d9-4cb0-8a6b-c092e73755e3')]
    function initialRoles() {
        Role::create(['name' => 'Admin']);
    }
}
```

## Extra Configuration

By default, the record of seed function run is stored in the `seeders` table. If you would like to change that location,
you can publish the config file and change the `table_name` property.

```php
php artisan vendor:publish --tag=persistent-seeder-config
```

```php
return [
    'table_name' => 'my_custom_table_name',
];
```

## Production Usage

This package is perfectly suited for production usage, even though you may be hesitant to run "seeders" in production.

The most straightforward approach is to create a `ProductionSeeder` class as a regular seeder,
and then run it as part of your deploy process.

```php
class ProductionSeeder extends Seeder
{
    public function run(): void
    {
        // Each of these is a PersistentSeeder
        $this->call(TenantSeeder::class);
        $this->call(RoleSeeder::class);
        $this->call(UserSeeder::class);
    }
}
```

```bash
php artisan db:seed --class=ProductionSeeder --force
```

Alternatively, you can run each individually

```bash
php artisan db:seed --class=TenantSeeder --force
php artisan db:seed --class=RoleSeeder --force
php artisan db:seed --class=UserSeeder --force
```
