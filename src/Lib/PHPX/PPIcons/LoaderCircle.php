<?php

namespace Lib\PHPX\PPIcons;

use Lib\PHPX\PHPX;

class LoaderCircle extends PHPX
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
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="{$class}" $attributes><path d="M21 12a9 9 0 1 1-6.219-8.56"></path></svg>
        HTML;
    }
}
