<?php

namespace Lib\PHPX\PHPXUI;

use Lib\PHPX\PHPX;

class Label extends PHPX
{
    public function __construct(array $props = [])
    {
        parent::__construct($props);
    }

    public function render(): string
    {
        $attributes = $this->getAttributes();
        $class = $this->getMergeClasses(
            "text-xs sm:text-sm md:text-base font-medium leading-tight md:leading-normal 
            group-disabled:cursor-not-allowed group-disabled:opacity-70 
            transition-all duration-300 ease-in-out"
        );

        return <<<HTML
        <label class="$class" $attributes>{$this->children}</label>
        HTML;
    }
}
