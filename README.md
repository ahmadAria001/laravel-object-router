# Laravel Object Routing

## Normal Laravel Routing

In Laravel, routes are defined in the `routes/web.php` file for web routes and `routes/api.php` for API routes. Routes are used to map URLs to specific controllers or actions. Here is an example of a basic route definition:

```php
use Illuminate\Support\Facades\Route;

Route::get('/example', function () {
    return 'Hello, World!';
});

Route::post('/example-post', function () {
    return 'Hello, World!';
});
```

This route listens for GET requests to the `/example` URL and returns a simple text response. Routes can also be defined to use controllers:

```php
use App\Http\Controllers\ExampleController;

Route::get('/example', [ExampleController::class, 'show']);
```

In this example, the route maps the `/example` URL to the `show` method of the `ExampleController` class.

## Alternative with Object Routing

What if we use Polymorphism in routing?

### Polymorphic Routes

Polymorphic routes allow you to define routes that can handle multiple types of requests or actions. This can be useful for creating more dynamic and flexible routing structures.

### Defining Polymorphic Routes

To define polymorphic routes, you can create a base route class and extend it for specific route types. Here is an example:

```php
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
```

In this example, `BaseRoutes` is an abstract class that defines the basic structure for a route. It includes methods for defining GET, POST, UPDATE, and DELETE routes, which can be overridden by subclasses to provide specific route definitions.

### Using Polymorphic Routes

Here is how you can use the polymorphic routes in your application:

```php
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
```

In this example, `PostRoutes` extends `BaseRoutes` and overrides the `GET` method to define specific GET routes. The routes are then registered with the router using the `define` method.


## Registering All Routes

Since I am too lazy to put all the routes in the `routes/web.php`, we can just register the routes with services which will be started and loaded on startup.

```php
namespace App\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\File;
use ReflectionClass;

class RouteServiceProvider extends ServiceProvider
{
    public function boot()
    {
        parent::boot();
    }

    /**
     * Define the routes for the application.
     *
     * @return void
     */
    public function map()
    {
        $this->mapClassBasedRoutes();
    }

    /**
     * Automatically find and register all classes extending BaseRoutes.
     *
     * @return void
     */
    protected function mapClassBasedRoutes()
    {
        try {
            // Define the namespace and directory of the route classes
            $namespace_path = config('app.namespace_routes_path');
            $namespace = "App\\Http\\$namespace_path";

            if (PHP_OS == 'WINNT')
                $parsed_namespace_path = str_replace('/', '\\', $namespace_path);
            else
                $parsed_namespace_path = str_replace('\\', '/', $namespace_path);

            $directory = base_path('app' . DIRECTORY_SEPARATOR . 'Http' . DIRECTORY_SEPARATOR . $parsed_namespace_path);

            // Scan for all PHP files in the directory
            $routeFiles = File::allFiles($directory);

            foreach ($routeFiles as $file) {
                $className = $namespace . '\\' . str_replace(['/', '.php'], ['\\', ''], $file->getRelativePathname());

                // Check if the class exists and extends BaseRoutes
                if (class_exists($className)) {
                    $reflection = new ReflectionClass($className);

                    if ($reflection->isSubclassOf(\App\Utils\Base\BaseRoutes::class) && !$reflection->isAbstract()) {
                        $this->registerRouteClass($reflection->getName());
                    }
                }
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    /**
     * Register a route class by calling its `define` method.
     *
     * @param string $routeClass
     * @return void
     */
    protected function registerRouteClass(string $routeClass)
    {
        $routeInstance = new $routeClass();
        if (method_exists($routeInstance, 'define')) {
            $routeInstance->define();
        }
    }
}
```

### Explanation

The `RouteServiceProvider` is responsible for defining and registering routes in our Laravel application and this is the detailed explanation of the code:

1. **boot Method**:
   - The `boot` method is called after all other service providers have been registered. It calls the parent `boot` method.

1. **map Method**:
   - The `map` method is responsible for defining the routes for the application. It calls the `mapClassBasedRoutes` method to register class-based routes.

1. **mapClassBasedRoutes Method**:
   - This method automatically finds and registers all classes extending `BaseRoutes`.
   - It defines the namespace and directory of the route classes based on the configuration.
   - It scans the specified directory for all PHP files.
   - For each file, it constructs the class name and checks if the class exists and extends `BaseRoutes`.
   - If the class is valid, it registers the route class by calling the `registerRouteClass` method.

1. **registerRouteClass Method**:
   - This method creates an instance of the route class and calls its `define` method to register the routes.

This approach allows you to organize your routes into separate classes and automatically register them on application startup, making it easier to manage and maintain your routes.

Finally, add the service provider to the `bootstrap/providers.php` file under the `providers` array.

```php
return [
    // ...existing code...
    App\Providers\RouteServiceProvider::class,
];
```

## Summary: Object Routing in Laravel - Pros, Cons, and Optimization

This "Object Routing" approach offers a structured alternative to traditional Laravel route files by organizing routes into classes for improved modularity and maintainability, especially in larger applications.

**Benefits include:** better route organization, potential code reuse, polymorphism for HTTP verb handling, and automatic route registration, leading to cleaner route definition files and easier refactoring.

**However, consider the trade-offs:** increased complexity, a steeper learning curve, and potential performance overhead if not optimized.  Without optimization, the dynamic route registration can be slower than standard Laravel routing.

**To ensure performance in production, it is crucial to optimize your application by running these commands during deployment:**

```bash
php artisan optimize
php artisan route:cache
composer dump-autoload --optimize
```

**`php artisan route:cache` is the key optimization, as it eliminates the runtime overhead of dynamic route registration, making this approach performant for production environments.**  By leveraging these optimizations, you can effectively use Object Routing to enhance the organization of your Laravel routes without sacrificing performance.