<?php

namespace ProBonoLabs\LaravelApiGenerator\Libs;

class CreateResource extends CreateClass
{
    public function __construct(string $name, string $appNamespace, string $localNamespace)
    {
        parent::__construct($name, $appNamespace, $localNamespace);

        $this->setSuffix('Resource');
    }

    /**
     * @param bool $withNamespace
     * @return $this
     */
    public function build($withNamespace = true)
    {
        parent::build($withNamespace);

        //  Add use to namespace
        $this->namespace->addUse(config('laravel-api-generator.base.resource'));

        //  Extends JsonResource::class
        $this->class->setExtends(config('laravel-api-generator.base.resource'));

        //  toArray method
        $method = $this->class->addMethod('toArray')
            ->addComment('Transform the resource into an array.')
            ->addComment('')
            ->addComment('@param $request')
            ->addComment('@return array')
            ->setBody('return [];');
        $method->addParameter('request');
        $method->setReturnType('array');

        return $this;
    }
}
