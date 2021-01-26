<?php

namespace ProBonoLabs\LaravelApiGenerator\Libs;

class CreateModel extends CreateClass
{

    /**
     * CreateModel constructor.
     * @param string $name
     * @param string $appNamespace
     * @param string $localNamespace
     */
    public function __construct(string $name, string $appNamespace, string $localNamespace)
    {
        parent::__construct($name, $appNamespace, $localNamespace);
    }

    /**
     * @param bool $withNamespace
     * @return $this
     */
    public function build($withNamespace = true)
    {
        parent::build($withNamespace);

        //  Add use to namespace
        $this->namespace->addUse('Illuminate\Database\Eloquent\Factories\HasFactory');
        $this->namespace->addUse(config('laravel-api-generator.base.model'));

        //  Extends FormRequest::class
        $this->class->setExtends(config('laravel-api-generator.base.model'));

        $this->class->addTrait('Illuminate\Database\Eloquent\Factories\HasFactory');

        return $this;
    }
}
