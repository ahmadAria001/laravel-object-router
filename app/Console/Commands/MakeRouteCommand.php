<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

class MakeRouteCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:route {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new route class';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $routeName = $this->argument('name'); // Get the 'name' argument
        $routeClassName = Str::studly($routeName) . 'Route'; // Convert to StudlyCase and add "Route" suffix
        $routeClassFilename = $routeClassName . '.php';
        $routesDirectory = app_path('Http/Routes'); // Path to your Routes directory
        $routeClassPath = $routesDirectory . DIRECTORY_SEPARATOR . $routeClassFilename;
        $namespace = 'App\\Http\\Routes'; // Namespace for your route classes

        // Ensure the Routes directory exists
        if (!File::isDirectory($routesDirectory)) {
            File::makeDirectory($routesDirectory, 0755, true); // Create directory if not exists
        }

        // Check if the route class already exists
        if (File::exists($routeClassPath)) {
            $this->error("Route class '{$routeClassName}' already exists!");
            return;
        }

        // Generate the route class content (you can customize the template)
        $routeClassContent = $this->generateRouteClassContent($namespace, $routeClassName);

        // Create the route class file
        File::put($routeClassPath, $routeClassContent);

        $this->info("Route class '{$routeClassName}' created successfully in {$routeClassPath}");
    }

    protected function generateRouteClassContent(string $namespace, string $className): string
    {
        return <<<EOT
<?php

namespace {$namespace};

use App\Utils\Base\BaseRoutes;
use Illuminate\Support\Facades\Route;

class {$className} extends BaseRoutes
{
    public function __construct()
    {
        parent::__construct(strtolower('{$className}')); // Example prefix based on class name
    }

    #[\Override]
    public static function GET()
    {
        return [
            Route::get('/', function () {
                return '{$className} Index Route';
            })->name(parent::\$PREFIX . '.index'),
            // Define more GET routes here
        ];
    }
}
EOT;
    }
}
