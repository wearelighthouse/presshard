<?php

namespace PressHard\Core;

class Singleton
{
    /**
     * @return $this
     */
    public static function instance()
    {
        static $instance = null;

        if (null === $instance) {
            $instance = new static();
        }

        return $instance;
    }

    /**
     * @return void
     */
    protected function __construct()
    {
        $this->initialise();
    }

    /**
     * Hook into constructor
     *
     * @return void
     */
    protected function initialise()
    {
    }

    /**
     * @return void
     */
    private function __clone()
    {
    }

    /**
     * @return void
     */
    private function __wakeup()
    {
    }
}
