<?php

namespace Foundation;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @method static run(InputInterface $input = null, OutputInterface $output = null)
 * @method static add(Command $command)
 * Class Disposer
  */
class Disposer
{

    /**
     * @var Console\Application
     */
    private static $disposer;

    /**
     * @return Console\Application
     */
    protected static function getDisposer()
    {
        if ( ! is_null(self::$disposer)) return self::$disposer;

        self::$disposer = new Console\Application();

        return self::$disposer;
    }

    /**
     * Dynamically pass all missing methods to console Artisan.
     *
     * @param  string  $method
     * @param  array   $parameters
     * @return mixed
     */
    public static function __callStatic($method, $parameters)
    {
        return call_user_func_array(array(self::getDisposer(), $method), $parameters);
    }
}