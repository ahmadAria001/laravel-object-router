<?php

namespace App\Http\Routes;

use App\Utils\Base\BaseRoutes;
use Illuminate\Support\Facades\Route;

class PostRoutes extends BaseRoutes
{
    public function __construct()
    {
        parent::__construct('posts');
    }

    #[\Override]
    public static function GET()
    {
        return [
            Route::get('/', function () {
                return view('welcome');
            })->name(parent::$PREFIX . '.index'),
            // Add more routes here
            Route::get('/about', function () {
                return 'About Page';
            })
        ];
    }
}
