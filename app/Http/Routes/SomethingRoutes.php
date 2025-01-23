<?php

namespace App\Http\Routes;

use App\Utils\Base\BaseRoutes;
use Illuminate\Support\Facades\Route;

class SomethingRoutes extends BaseRoutes
{
    public function __construct()
    {
        parent::__construct('something');
    }

    #[\Override]
    public static function GET()
    {
        // return [
        Route::get('/', function () {
            return view('welcome');
        })->name(parent::$PREFIX . '.index');
        // Add more routes here
        Route::get('/about', function () {
            return 'About Page';
        });
        // ];
    }

    #[\Override]
    public static function POST()
    {
        // return [
        Route::get('/', function () {
            return view('welcome');
        })->name(parent::$PREFIX . '.index');
        // Add more routes here
        Route::get('/somepost', function () {
            return 'About Page';
        });
        // ];
    }
}
