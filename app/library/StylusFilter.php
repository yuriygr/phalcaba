<?php

use Phalcon\Assets\FilterInterface;

/**
 * Stylus formated
 *
 * @param string $contents
 * @return string
 */
class StylusFilter implements FilterInterface
{
    /**
     * Do the filtering
     *
     * @param string $contents
     * @return string
     */
    public function filter($contents)
    {
        $license = "/* (c) 2015 Your Name Here */";

        return $license . PHP_EOL . PHP_EOL . $contents;
    }
}