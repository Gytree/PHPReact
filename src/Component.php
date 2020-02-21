<?php

namespace Gytree\PHPReact;

class Component
{
    protected $component_name;

    public function __construct(string $component_name)
    {
        $this->component_name = $component_name;
    }

    public function name(): string
    {
        return $this->component_name;
    }
}
