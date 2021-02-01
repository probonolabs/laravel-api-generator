<?php

return [

    /**
     * Default namespaces
     */
    'namespaces' => [

        'controller' => 'Http\Controllers\Api',

        'request' => 'Http\Requests\Api',

        'resource' => 'Http\Resources\Api',

        'model' => 'Models',

        'policy' => 'Policies',

        'factory' => 'Database\Factories',

    ],

    /**
     *  Default base classes
     */
    'base' => [

        'controller' => \App\Http\Controllers\Controller::class,

        'request' => \Illuminate\Foundation\Http\FormRequest::class,

        'resource' => \Illuminate\Http\Resources\Json\JsonResource::class,

        'model' => \Illuminate\Database\Eloquent\Model::class,

        'migration' => \Illuminate\Database\Migrations\Migration::class,

        'factory' => \Illuminate\Database\Eloquent\Factories\Factory::class,

    ],

    /**
     * Path to migrations
     */
    'migrations' => 'database/migrations',

    /**
     * Append generated routes to this file
     */
    'routes' => 'routes/api.php',

    /**
     *  Middleware, applied on all created routes
     */
    'middleware' => [],

    /**
     *  Migration table columns
     */
    'columns' => [
        'string' => ['faker' => '$this->faker->sentence', 'default' => null],
        'text' => ['faker' => '$this->faker->sentences(10, true)', 'default' => null],
        'date' => ['faker' => 'Carbon::now()', 'default' => null],
        'json' => ['faker' => null, 'default' => '[]'],
        'integer' => ['faker' => '$this->faker->randomNumber()', 'default' => null],
        'double' => ['faker' => '$this->faker->randomNumber(2)', 'default' => null],
        'enum' => ['faker' => '$this->faker->randomElement({{list}})', 'default' => null],
    ]

];
