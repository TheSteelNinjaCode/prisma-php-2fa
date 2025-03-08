<?php

namespace Lib\PHPX\PHPXUI;

use Lib\PHPX\PHPX;

class AspectRatio extends PHPX
{
    public function __construct(array $props = [])
    {
        parent::__construct($props);
    }

    public function render(): string
    {
        $ratio = $this->props['ratio'] ?? (16 / 9); // Valor por defecto
        $class = $this->getMergeClasses('relative overflow-hidden', $this->props['class'] ?? '');

        return <<<HTML
        <div class="$class" style="aspect-ratio: $ratio;">
            {$this->children}
        </div>
        HTML;
    }
}
