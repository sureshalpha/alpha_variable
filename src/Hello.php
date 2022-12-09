<?php

namespace Kitamula\Kitchen;

class Hello
{
    private $here;

    public function __construct()
    {
        $this->here = 'kitamula\kitchen';
    }

    public function say()
    {
        return 'Hello, world! from '.$this->here;
    }
}
