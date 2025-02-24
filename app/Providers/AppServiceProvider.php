<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use PDO;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $options = [
            PDO::MYSQL_ATTR_SSL_CA   => env('DB_SSL_CA'),
            PDO::MYSQL_ATTR_SSL_CERT => env('DB_SSL_CERT'),
            PDO::MYSQL_ATTR_SSL_KEY  => env('DB_SSL_KEY'),
        ];

        if (!extension_loaded('pdo_mysql') || empty(array_filter($options))) {
            throw new \Exception('La conexión a MySQL requiere SSL, pero los certificados no están configurados correctamente.');
        }
    }
}
