<?php

namespace Lib\PHPX\PPIcons;

use Lib\PHPX\PHPX;

class Check extends PHPX
{
    public function __construct(array $props = [])
    {
        parent::__construct($props);
    }

    public function render(): string
    {
        $attributes = $this->getAttributes();
        $class = $this->getMergeClasses();

        return <<<HTML
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="{$class}" $attributes><path d="M20 6 9 17l-5-5"></path></svg>
        HTML;
    }
}
