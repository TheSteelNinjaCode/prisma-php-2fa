<?php

namespace Lib\PHPX\PHPXUI;

use Lib\PHPX\PHPX;

class Textarea extends PHPX
{
    public function __construct(array $props = [])
    {
        parent::__construct($props);
    }

    public function render(): string
    {
        $attributes = $this->getAttributes();
        $class = $this->getMergeClasses('flex min-h-[60px] w-full rounded-md border border-input bg-transparent px-3 py-2 text-base shadow-sm placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:cursor-not-allowed disabled:opacity-50 md:text-sm');

        return <<<HTML
        <textarea class="$class" $attributes>
            {$this->children}
        </textarea>
        HTML;
    }
}
