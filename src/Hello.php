<?php

namespace sureshalpha\alpha_variable;

class Hello
{
    private $here;

    public function __construct()
    {
        $this->here = 'sureshalpha\alpha_variable';
    }

    public function say()
    {
        return 'Hello, world! from '.$this->here;
    }
}
