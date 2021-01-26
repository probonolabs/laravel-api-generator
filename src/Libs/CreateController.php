<?php

namespace ProBonoLabs\LaravelApiGenerator\Libs;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;


class CreateController extends CreateClass
{

    protected $invokeParameters;
    protected $resource;
    protected $return;
    protected $model;

    public function __construct(string $name, string $appNamespace, string $localNamespace)
    {

        parent::__construct($name, $appNamespace, $localNamespace);

        $this->setSuffix('Controller');

        $this->invokeParameters = [];

    }

    public function addInvokeParameter($namespace, $name)
    {
        $this->invokeParameters[] = compact('namespace', 'name');
        return $this;
    }

    public function setResource(string $resource, ?string $return)
    {
        $this->resource = $resource;
        $this->return = $return;
        return $this;
    }

    public function setModel(string $model)
    {
        $this->model = $model;
        return $this;
    }

    public function build($withNamespace = true)
    {
        parent::build($withNamespace);

        $this->namespace->addUse(config('laravel-api-generator.base.controller'));
        $this->namespace->addUse('Illuminate\Http\JsonResponse');
        $this->namespace->addUse($this->resource);
        foreach ($this->invokeParameters as $parameter) {
            $this->namespace->addUse($parameter['namespace']);
        }

        //  Extends class from Laravel controller
        $this->class->setExtends(config('laravel-api-generator.base.controller'));

        //  Add invoke method
        $method = $this->class->addMethod('__invoke');

        //  Add method parameters
        foreach ($this->invokeParameters as $parameter) {
            $name = collect(explode('\\', $parameter['namespace']))->last();
            $method->addComment('@param ' . $name . ' $' . Str::camel($name));
            $method->addParameter(Str::camel($name))->setType($parameter['namespace']);
        }

        //  Add JSON return type
        $method->addComment('@return JsonResponse');
        $method->setReturnType(JsonResponse::class);

        //  Add default return
        $output = '$' . Str::camel($this->name);
        if ($this->return == 'collection') {
            $output = '$' . Str::plural(Str::camel($this->name));
            $method->addBody($output . ' = ' . $this->getName($this->model) . '::all();');
        }
        $method->addBody('return response()->json(' . ($this->return ? $this->getName($this->resource) . '::' . $this->return . '(' . $output . ')' : '') . ');');

        return $this;
    }

    private function getName(string $resource): string
    {
        return collect(explode('\\', $resource))->last();
    }
}
