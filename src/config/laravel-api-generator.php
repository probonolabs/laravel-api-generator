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

        'policy' => 'Policies'

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
    'middleware' => []

];
