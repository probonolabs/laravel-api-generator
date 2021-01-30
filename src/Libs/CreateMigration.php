<?php

namespace ProBonoLabs\LaravelApiGenerator\Libs;

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Collection;

class CreateMigration extends CreateClass
{
    protected $columns;

    public function __construct(string $name)
    {
        parent::__construct($name);
    }

    public function setColumns(Collection $columns)
    {
        $this->columns = $columns;
        return $this;
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
            ->addBody("\t".'$table->id();');

        //  Add columns
        $this->columns->each(function($column) use ($up) {
            $up->addBody("\t".'$table->'.$column['type'].'(\''.$column['name'].'\''.($column['type'] == 'enum' ? ', ['.collect($column['enumValues'])->map(function($value) { return '\''.$value.'\'';})->join(', ').']' : '').')'.($column['nullable'] ? '->nullable()' : '').';');
        });

        $up->addBody("\t".'$table->timestamps();')
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
