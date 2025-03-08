<?php

namespace Lib\PHPX\PHPXUI;

use Lib\PHPX\PHPX;

class Avatar extends PHPX
{
    public function __construct(array $props = [])
    {
        parent::__construct($props);
    }

    public function render(): string
    {
        // Se agregan clases responsivas para ajustar el tamaño del avatar según el breakpoint
        $class = $this->getMergeClasses('relative flex size-8 shrink-0 overflow-hidden rounded-full');
        $attributes = $this->getAttributes();

        return <<<HTML
        <span class="$class" $attributes>
            {$this->children}
        </span>
        HTML;
    }
}

class AvatarImage extends PHPX
{
    public function __construct(array $props = [])
    {
        parent::__construct($props);
    }

    public function render(): string
    {
        // La imagen ocupa todo el contenedor y se incluyen atributos para optimizar la carga
        $class = $this->getMergeClasses('aspect-square h-full w-full object-cover');
        $attributes = $this->getAttributes();

        // Se pueden pasar atributos "srcset" y "sizes" desde $props para que el navegador elija la imagen adecuada
        return <<<HTML
        <img class="$class" loading="lazy" decoding="async" $attributes />
        HTML;
    }
}

class AvatarFallback extends PHPX
{
    public function __construct(array $props = [])
    {
        parent::__construct($props);
    }

    public function render(): string
    {
        // Se agregan clases responsivas para que el texto o icono de fallback se ajuste en tamaño
        $class = $this->getMergeClasses('flex items-center justify-center rounded-full bg-muted h-full w-full text-xs sm:text-sm md:text-base');
        $attributes = $this->getAttributes();

        return <<<HTML
        <div class="$class" $attributes>
            {$this->children}
        </div>
        HTML;
    }
}
