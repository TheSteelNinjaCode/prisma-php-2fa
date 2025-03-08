<?php

namespace Lib\PHPX\PHPXUI;

use Lib\PHPX\PHPX;
use Lib\PHPX\PHPXUI\Slot;

class Button extends PHPX
{
    public function __construct(array $props = [])
    {
        parent::__construct($props);
    }

    private function getComputedClasses(): string
    {
        $defaultClasses =
            "inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium transition-[color,box-shadow] disabled:pointer-events-none disabled:opacity-50 [&_svg]:pointer-events-none [&_svg:not([class*='size-'])]:size-4 [&_svg]:shrink-0 outline-none focus-visible:border-ring focus-visible:ring-ring/50 focus-visible:ring-[3px] aria-invalid:ring-destructive/20 dark:aria-invalid:ring-destructive/40 aria-invalid:border-destructive";

        $variantClasses = [
            'default' => 'bg-primary text-primary-foreground shadow-xs hover:bg-primary/90',
            'destructive' => 'bg-destructive text-white shadow-xs hover:bg-destructive/90 focus-visible:ring-destructive/20 dark:focus-visible:ring-destructive/40',
            'outline' => 'border border-input bg-background shadow-xs hover:bg-accent hover:text-accent-foreground',
            'secondary' => 'bg-secondary text-secondary-foreground shadow-xs hover:bg-secondary/80',
            'ghost' => 'hover:bg-accent hover:text-accent-foreground',
            'link' => 'text-primary underline-offset-4 hover:underline',
        ];

        $sizeClasses = [
            'default' => 'h-9 px-4 py-2 has-[>svg]:px-3',
            'sm' => 'h-8 rounded-md gap-1.5 px-3 has-[>svg]:px-2.5',
            'lg' => 'h-10 rounded-md px-6 has-[>svg]:px-4',
            'icon' => 'size-9',
        ];

        $variant = $this->props['variant'] ?? 'default';
        $size = $this->props['size'] ?? 'default';

        $variantClass = $variantClasses[$variant] ?? $variantClasses['default'];
        $sizeClass = $sizeClasses[$size] ?? $sizeClasses['default'];

        return $this->getMergeClasses($defaultClasses, $variantClass, $sizeClass);
    }

    public function render(): string
    {
        $attributes = $this->getAttributes([
            'type' => 'button',
        ]);
        $class = $this->getComputedClasses();
        $asChild = $this->props['asChild'] ?? false;

        if ($asChild) {
            $slot = new Slot([
                'class' => $class,
                'asChild' => true,
                ...$this->attributesArray,
            ]);
            $slot->children = $this->children;
            return $slot->render();
        }

        return <<<HTML
        <button class="$class" $attributes>{$this->children}</button>
        HTML;
    }
}
