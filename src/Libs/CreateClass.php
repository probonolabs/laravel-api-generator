<?php

namespace ProBonoLabs\LaravelApiGenerator\Libs;

use Illuminate\Support\Facades\File;
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

    /**
     * CreateClass constructor.
     * @param string $name
     * @param string $appNamespace
     * @param string $localNamespace
     */
    public function __construct(string $name, string $appNamespace = '', string $localNamespace = '')
    {
        //  Classname
        $this->name = $name;

        //  App namespace
        $this->appNamespace = $appNamespace;

        //  Local namespace
        $this->localNamespace = $localNamespace;
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
     * @return string
     */
    public function mergeNamespace(string $appNamespace, string $localNamespace): string
    {
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

        //  Create directories
        @File::makeDirectory(app_path() . '/' . $directory, 0777, true);

        //  Write file
        if ($path && $filename) {
            File::put($path . '/' . $filename . '.php', $output);
        } else {
            File::put(app_path() . '/' . $directory . $this->prefix . $this->name . $this->suffix . '.php', $output);
        }

        return str_replace('/', '\\', $directory . $this->prefix . $this->name . $this->suffix);
    }
}
