<?php

namespace PressHard\Route;

use PressHard\Utility\Singleton;

use Exception;

abstract class AbstractRoute extends Singleton
{
    /**
     * @return string|array
     */
    abstract public function getRedirect();

    /**
     * @return string|array
     */
    abstract public function getRegex();

    /**
     * @return void
     */
    public function register()
    {
        $regexArray = is_array($this->getRegex())
            ? $this->getRegex()
            : [$this->getRegex()];

        $redirectArray = is_array($this->getRedirect())
            ? $this->getRedirect()
            : [$this->getRedirect()];

        $regexCount = count($regexArray);
        $redirectCount = count($redirectArray);

        if ($regexCount !== $redirectCount) {
            throw new Exception('You must have matching redirects for all regex');
        }

        for ($i = 0; $i < $redirectCount; $i++) {
            add_rewrite_rule(
                $regexArray[$i],
                $redirectArray[$i],
                'top'
            );
        }
    }
}
