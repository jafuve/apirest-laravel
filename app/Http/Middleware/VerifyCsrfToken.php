<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        //
        'http://localhost:8080/apirest-laravel/public/registro',
        'http://localhost:8080/apirest-laravel/public/cursos',
        'http://localhost:8080/apirest-laravel/public/cursos/*',
    ];
}
