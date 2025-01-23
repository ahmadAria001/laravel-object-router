<?php

namespace App\Utils\Base;

use Error;
use Illuminate\Support\Facades\Route;

abstract class BaseRoutes
{
    static string $PREFIX;

    public function __construct(string $prefix)
    {
        if (!$prefix) throw new Error('Routes Does not have Attribute Prefix');
        self::$PREFIX = $prefix;
    }

    public function define()
    {
        Route::prefix(self::$PREFIX)->group(function () {
            static::GET();
            static::DELETE();
            static::POST();
            static::UPDATE();
        });
    }

    public static function GET()
    {
        Route::get('/', function () {
            abort(404);
        });
    }
    public static function POST()
    {
        Route::post('/', function () {
            abort(404);
        });
    }
    public static function UPDATE()
    {
        Route::put('/', function () {
            abort(404);
        });
    }
    public static function DELETE()
    {
        Route::delete('/', function () {
            abort(404);
        });
    }
}
