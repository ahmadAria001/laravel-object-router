<?php

namespace App\Providers;

use App\Utils\Enums\TermOutType;
use App\Utils\TerminalOut;
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
            // dd($namespace_path);
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
            $msg = $th->getMessage();

            new TerminalOut($msg, TermOutType::ERR, 'https://github.com/ahmadAria001/rsud-prambanan/blob/main/app/Enum/File/FileTypeEnum.php');
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
