<?php
namespace Tengu;

class Container
{
    /**
     * Object hash map
     *
     * @var array
     */
    private $container = array();

    /**
     * Static instance object
     *
     * @var object
     */
    public static $instance;

    /**
     * Create a new Container instance.
     *
     * @return void
     */
    public function __construct()
    {
        self::$instance = $this;
    }

    /**
     * Get the current instance of the Container.
     *
     * @return object
     */
    public function getInstance()
    {
        return self::$instance;
    }

    /**
     * Magic set method.
     *
     * @param string $key
     * @param mixed  $value
     *
     * @return void
     */
    public function __set($key, $value)
    {
        $this->container[$key] = $value;
    }

    /**
     * Magic get method.
     *
     * @param string $key
     *
     * @return mixed
     */
    public function __get($key)
    {
        return $this->container[$key];
    }
}
