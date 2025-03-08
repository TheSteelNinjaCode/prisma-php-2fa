<?php

namespace Lib\PHPX\PHPXUI;

use Lib\PHPX\PHPX;
use Lib\MainLayout;

class Toggle extends PHPX
{
    public function __construct(array $props = [])
    {
        parent::__construct($props);

        $script = <<<HTML
        <script>
            function toggleButton(event) {
                const button = event.currentTarget;
                const isPressed = button.getAttribute('aria-pressed') === 'true';

                // Cambia el estado de presionado
                button.setAttribute('aria-pressed', !isPressed);

                // Mantener el color de fondo cuando está activado
                if (!isPressed) {
                    button.classList.add('bg-[#27272A]');
                } else {
                    button.classList.remove('bg-[#27272A]');
                }
            }
        </script>
        HTML;

        MainLayout::addFooterScript($script);
    }

    public function render(): string
    {
        $attributes = $this->getAttributes([
            'aria-pressed' => 'false',
            'role' => 'button',
            'onclick' => 'toggleButton(event)',
            'type' => 'button',
        ]);

        // Verifica si se pasa el 'variant' outline y asigna las clases correspondientes
        $variant = $this->props['variant'] ?? null;

        if ($variant === 'outline') {
            $class = $this->getMergeClasses('
                flex items-center justify-center w-10 h-10 rounded-md 
                border border-[#27272A] 
                transition hover:bg-[#27272A] hover:text-white 
                focus:outline-none focus:ring-0
            ');
        } else {
            $class = $this->getMergeClasses('
                flex items-center justify-center w-10 h-10 rounded-md 
                transition hover:bg-[#27272A] 
                focus:outline-none focus:ring-0
            ');
        }

        return <<<HTML
        <button class="$class cursor-pointer" $attributes>
            {$this->children}
        </button>
        HTML;
    }
}
