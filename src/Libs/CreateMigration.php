<?php

namespace ProBonoLabs\LaravelApiGenerator\Libs;

use Illuminate\Database\Migrations\Migration;

class CreateMigration extends CreateClass
{
    public function __construct(string $name)
    {
        parent::__construct($name);

    }

    public function build($withNamespace = true, $tableName = '')
    {
        parent::build($withNamespace);

        $this->file->addUse(config('laravel-api-generator.base.migration'));
        $this->file->addUse('Illuminate\Database\Schema\Blueprint');
        $this->file->addUse('Illuminate\Support\Facades\Schema');

        //  Extends class
        $this->class->setExtends(config('laravel-api-generator.base.migration'));


        //  Up method
        $up = $this->class->addMethod('up')
            ->addComment('Run the migrations.')
            ->addComment('')
            ->addComment('@return void')
            ->addBody('Schema::create(\'' . $tableName . '\', function (Blueprint $table) {')
            ->addBody('$table->id();')
            ->addBody('$table->timestamps();')
            ->addBody('});');

        //  Down method
        $down = $this->class->addMethod('down')
            ->addComment('Reverse the migrations.')
            ->addComment('')
            ->addComment('@return void')
            ->setBody('Schema::dropIfExists(\'' . $tableName . '\');');

        return $this;
    }
}
