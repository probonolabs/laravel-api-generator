<?php

namespace ProBonoLabs\LaravelApiGenerator\Libs;

class CreateRequest extends CreateClass
{
    public function __construct(string $name, string $appNamespace, string $localNamespace, string $suffix = '')
    {
        parent::__construct($name, $appNamespace, $localNamespace);

        $this->setSuffix('Request');
    }

    /**
     * @param bool $withNamespace
     * @return $this
     */
    public function build($withNamespace = true)
    {
        parent::build($withNamespace);

        //  Add use to namespace
        $this->namespace->addUse(config('laravel-api-generator.base.request'));

        //  Extends FormRequest::class
        $this->class->setExtends(config('laravel-api-generator.base.request'));

        //  Authorize method
        $this->class->addMethod('authorize')
            ->addComment('Determine if the user is authorized to make this request.')
            ->addComment('')
            ->addComment('@return bool')
            ->setReturnType('bool')
            ->setBody('return false;');

        //  Rules method
        $this->class->addMethod('rules')
            ->addComment('Get the validation rules that apply to the request.')
            ->addComment('')
            ->addComment('@return mixed[]')
            ->setReturnType('array')
            ->setBody('return [];');

        return $this;
    }
}
