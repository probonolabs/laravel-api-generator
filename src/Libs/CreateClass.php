<?php

namespace ProBonoLabs\LaravelApiGenerator\Libs;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Nette\PhpGenerator\PhpFile;
use Nette\PhpGenerator\PsrPrinter;

/**
 * Class CreateClass
 * @package Luters\RestApi\Libs
 */
class CreateClass
{

    /**
     * @var string
     */
    protected $name;
    /**
     * @var string
     */
    protected $appNamespace;
    /**
     * @var string
     */
    protected $localNamespace;
    /**
     * @var
     */
    protected $prefix;
    /**
     * @var
     */
    protected $suffix;
    /**
     * @var
     */
    protected $file;
    /**
     * @var
     */
    protected $namespace;
    /**
     * @var
     */
    protected $namespaceName;
    /**
     * @var
     */
    protected $class;

    protected $appPrefix;

    /**
     * CreateClass constructor.
     * @param string $name
     * @param string $appNamespace
     * @param string $localNamespace
     * @param bool $appPrefix
     */
    public function __construct(string $name, string $appNamespace = '', string $localNamespace = '', bool $appPrefix = true)
    {
        //  Classname
        $this->name = $name;

        //  App namespace
        $this->appNamespace = $appNamespace;

        //  Local namespace
        $this->localNamespace = $localNamespace;

        //  Add App/ prefix in namespace
        $this->appPrefix = $appPrefix;
    }

    /**
     * @param string $prefix
     * @return $this
     */
    public function setPrefix(string $prefix): self
    {
        $this->prefix = $prefix;
        return $this;
    }

    /**
     * @param string $suffix
     * @return $this
     */
    public function setSuffix(string $suffix): self
    {
        $this->suffix = $suffix;
        return $this;
    }

    /**
     * @param string $appNamespace
     * @param string $localNamespace
     * @param bool $appPrefix
     * @return string
     */
    public function mergeNamespace(string $appNamespace, string $localNamespace): string
    {
        if($this->appPrefix && !Str::startsWith('app', [Str::lower($appNamespace)])) {
            $appNamespace = 'App\\' . $appNamespace;
        }
        $this->namespaceName = collect(explode('\\', $appNamespace))
            ->merge(collect(explode('/', $localNamespace)))
            ->add($this->prefix ? $this->name : '')
            ->reject(function ($item) {
                return $item == '';
            })
            ->join('\\');
        return $this->namespaceName;
    }

    /**
     * @param bool $withNamespace
     * @return $this
     */
    public function build($withNamespace = true)
    {
        //  Merge app and local namespace
        $this->mergeNamespace($this->appNamespace, $this->localNamespace);

        //  Create an empty file
        $this->file = new PhpFile();

        //  Check if class should contain a names
        if ($withNamespace) {

            //  Create namespace
            $this->namespace = $this->file->addNamespace($this->namespaceName);

            //  Add policy class
            $this->class = $this->namespace->addClass($this->prefix . $this->name . $this->suffix);

        }

        //  Build a class without a namespace
        if (!$withNamespace) {
            $this->class = $this->file->addClass($this->prefix . $this->name . $this->suffix);
        }

        return $this;
    }

    /**
     * @param string $path
     * @return string|string[]
     */
    public function saveFile($path = '', $filename = '')
    {
        //  Create PHP file
        $output = (new PsrPrinter())->printFile($this->file);

        //  File location
        $directory = str_replace('\\', '/', $this->namespaceName . '/');


        //  Write file
        if ($path && $filename) {
            //  Create directories
            @File::makeDirectory($path, 0777, true);

            //  Save file
            File::put($path . '/' . $filename . '.php', $output);
        } else {
            //  Create directories
            @File::makeDirectory($directory, 0777, true);

            //  Save file
            File::put( $directory . $this->prefix . $this->name . $this->suffix . '.php', $output);
        }

        return str_replace('/', '\\', $directory . $this->prefix . $this->name . $this->suffix);
    }

    protected function getName(string $resource): string
    {
        return collect(explode('\\', $resource))->last();
    }
}
