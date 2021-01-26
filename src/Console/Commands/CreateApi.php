<?php

namespace ProBonoLabs\LaravelApiGenerator\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use ProBonoLabs\LaravelApiGenerator\Libs\CreateController;
use ProBonoLabs\LaravelApiGenerator\Libs\CreateMigration;
use ProBonoLabs\LaravelApiGenerator\Libs\CreatePolicy;
use ProBonoLabs\LaravelApiGenerator\Libs\CreateRequest;
use ProBonoLabs\LaravelApiGenerator\Libs\CreateModel;
use ProBonoLabs\LaravelApiGenerator\Libs\CreateResource;

/**
 * Class CreateApi
 * @package ProBonoLabs\LaravelApiGenerator\Console\Commands
 */
class CreateApi extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:api {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new API crud';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Api Resource';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        //  Resource name
        $name = Str::ucfirst(Str::camel(collect(explode('/', $this->argument('name')))->last()));

        //  Local namespace
        $localNamespace = collect(explode('/', $this->argument('name')))->slice(0, -1);

        //  Inject previous models
        $injectModels = $this->getModels($localNamespace);

        //  Create a model
        $model = (new CreateModel($name, config('laravel-api-generator.namespaces.model'), $localNamespace->join('/')))
            ->build()->saveFile();

        //  Create a migration
        $migrationName = 'Create' . Str::plural(Str::ucfirst(Str::camel($name))) . 'Table';
        $migration = (new CreateMigration($migrationName))->build(false, Str::plural(Str::lower($name)))->saveFile(
            config('laravel-api-generator.migrations'),
            date('Y_m_d_His', time()) . '_create_' . Str::plural(Str::snake($name)) . '_table'
        );

        //  Create a resource
        $resource = (new CreateResource($name, config('laravel-api-generator.namespaces.resource'), $localNamespace->join('/')))
            ->build()->saveFile();

        $this->addRoute('');
        $this->addRoute('// Routes generated with probonolabs/laravel-api-generator');

        $routeMiddleware = config('laravel-api-generator.middleware');
        if (count($routeMiddleware) > 0) {
            $this->addRoute('Route::middleware(['.collect($routeMiddleware)->map(function($item) { return "'".$item."'";})->join(', ').'])->group(function () {');
        }
        collect([
            ['name' => 'Index', 'method' => 'Get', 'return' => 'collection', 'parameter' => false],
            ['name' => 'Get', 'method' => 'Get', 'return' => 'make', 'parameter' => true],
            ['name' => 'Create', 'method' => 'Post', 'return' => 'make', 'parameter' => true],
            ['name' => 'Update', 'method' => 'Put', 'return' => 'make', 'parameter' => true],
            ['name' => 'Delete', 'method' => 'Delete', 'return' => null, 'parameter' => true],
        ])->each(function ($crud) use ($name, $model, $resource, $localNamespace, $injectModels, $routeMiddleware) {

            //  Create requests
            $request = (new CreateRequest($name, config('laravel-api-generator.namespaces.request'), $localNamespace->join('/')))
                ->setPrefix($crud['name'])
                ->build()->saveFile();

            //  Create a controller
            $controller = (new CreateController($name, config('laravel-api-generator.namespaces.controller'), $localNamespace->join('/')))
                ->setModel($model)
                ->setResource($resource, $crud['return']);

            //  Inject previous models
            $injectModels->each(function ($model) use ($controller) {
                $controller->addInvokeParameter($model['namespace'], Str::camel($model['name']));
            });

            //  Inject model and custom request
            $controller->addInvokeParameter($model, 'model')
                ->addInvokeParameter($request, 'request')
                ->setPrefix($crud['name'])
                ->build();

            //  Get controllers namespace
            $controllerNamespace = $controller->saveFile();

            //  Build API route path
            $apiRoutePath = $injectModels->map(function ($model) {
                return '/' . Str::kebab($model['name']) . '/{' . Str::camel($model['name']) . '}';
            })->join('');

            //  Current route
            $apiRoutePath .= '/' . Str::kebab($name);

            //  Add current model to route
            if ($crud['parameter']) {
                $apiRoutePath .= '/{' . Str::camel($name) . '}';
            }

            $this->addRoute((count($routeMiddleware) > 0 ? "\t" : '') . 'Route::' . Str::lower($crud['method']) . '(\'' . $apiRoutePath . '\', [\\' . $controllerNamespace . '::class, \'__invoke\']);');
        });

        //  Close middleware group
        if (count($routeMiddleware) > 0) {
            $this->addRoute('});');
        }
        $this->line('CRUD resource created, routes added in ' . config('laravel-api-generator.routes'));

    }

    /**
     * @param string $route
     */
    protected function addRoute(string $route)
    {
        File::append(base_path() . '/' . config('laravel-api-generator.routes'), $route . "\n");
    }

    /**
     * @param Collection $localNamespace
     * @return Collection
     */
    protected function getModels(Collection $localNamespace): Collection
    {
        //  Inject previous models
        $injectModels = collect();

        //  Check if local namespace contains existing models
        $localNamespace->each(function ($name, $index) use ($localNamespace, $injectModels) {

            //  Get the model namespace
            $namespace = $this->createPath(
                config('laravel-api-generator.namespaces.model'),
                collect(array_slice($localNamespace->toArray(), 0, $index + 1))->join('/')
            );

            //  Check if model exists
            $path = $this->createPath(app_path(), $namespace);
            if (File::exists($path)) {
                $namespace = str_replace('/', '\\', $namespace);
                $injectModels->add(compact('name', 'namespace', 'path'));
            }
        });

        return $injectModels;
    }

    /**
     * @param mixed ...$path
     * @return string
     */
    protected function createPath(...$path): string
    {
        return collect($path)->join('/');
    }
}
