<?php

namespace Gytree\PHPReact;

class Component
{
    protected $name;
    protected $props;

    public function __construct(string $name, array $props = [])
    {
        $this->name = $name;
        $this->props = $props;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function getProps(): array
    {
        return $this->props;
    }
}
