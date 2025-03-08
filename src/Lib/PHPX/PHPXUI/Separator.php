<?php

namespace Lib\PHPX\PHPXUI;

use Lib\PHPX\PHPX;

class Separator extends PHPX
{
    public function __construct(array $props = [])
    {
        parent::__construct($props);
    }

    public function render(): string
    {
        // Verifica si la orientación es vertical u horizontal
        $orientation = $this->props['orientation'] ?? 'horizontal';

        // Permite agregar clases personalizadas al separator
        $additionalClasses = $this->props['class'] ?? '';

        // Define las clases según la orientación
        $class = $this->getMergeClasses(
            $orientation === 'vertical'
                ? 'w-px h-5 bg-gray-500 flex-shrink-0 ' . $additionalClasses
                : 'w-full border-t border-gray-500 my-2 ' . $additionalClasses
        );

        return <<<HTML
        <div class="$class"></div>
        HTML;
    }
}
