<?php

namespace ProBonoLabs\LaravelApiGenerator\Libs;

use Illuminate\Support\Collection;
use Nette\PhpGenerator\Literal;

/**
 * Class CreateFactory
 * @package ProBonoLabs\LaravelApiGenerator\Libs
 */
class CreateFactory extends CreateClass
{

    protected $model;
    protected $columns;

    /**
     * CreateFactory constructor.
     * @param string $name
     * @param string $appNamespace
     * @param string $localNamespace
     */
    public function __construct(string $name, string $appNamespace, string $localNamespace)
    {
        parent::__construct($name, $appNamespace, $localNamespace, false);
    }

    public function setModel(string $model)
    {
        $this->model = $model;
        return $this;
    }

    public function setColumns(Collection $columns)
    {
        $this->columns = $columns;
        return $this;
    }

    /**
     * @param bool $withNamespace
     * @return $this
     */
    public function build($withNamespace = true)
    {
        parent::build($withNamespace);

        //  Add use to namespace
        $this->namespace->addUse(config('laravel-api-generator.base.factory'));
        $this->namespace->addUse($this->model);
        $this->namespace->addUse('Carbon\Carbon');

        //  Extends FormRequest::class
        $this->class->setExtends(config('laravel-api-generator.base.factory'));

        //  Set corresponding model
        $this->class->addProperty('model', new Literal($this->getName($this->model).'::class'))
            ->setProtected()
            ->addComment('The name of the factory\'s corresponding model.')
            ->addComment('')
            ->addComment('@return string');

        //  Rules method
        $definition = $this->class->addMethod('definition')
            ->addComment('Define the model\'s default state.')
            ->addComment('')
            ->addComment('@return array')
            ->setReturnType('array');
        $definition->addBody('return [');
        $this->columns->each(function($column) use ($definition) {
            $faker = 'null';
            $columnProperties = config('laravel-api-generator.columns.'.$column['type']);
            if($columnProperties) {
                $faker = str_replace('{{list}}', $column['enumValues'], ($columnProperties['faker'] ?? $columnProperties['default'] ?? 'null'));
            }
            $definition->addBody("\t'".$column['name']."' => ".$faker.",");
        });
        $definition->addBody('];');

        return $this;
    }
}
