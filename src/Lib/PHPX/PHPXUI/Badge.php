<?php

namespace Lib\PHPX\PHPXUI;

use Lib\PHPX\PHPX;

class Badge extends PHPX
{
    public function __construct(array $props = [])
    {
        parent::__construct($props);
    }

    private function getComputedClasses(): string
    {
        $defaultClasses = "inline-flex items-center rounded-md border px-2.5 py-0.5 text-xs font-semibold transition-colors focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2";

        $variantClasses = [
            'default' => "border-transparent bg-primary text-primary-foreground shadow hover:bg-primary/80",
            'secondary' => "border-transparent bg-secondary text-secondary-foreground hover:bg-secondary/80",
            'destructive' => "border-transparent bg-destructive text-destructive-foreground shadow hover:bg-destructive/80",
            'outline' => "text-foreground",
        ];

        $variant = $this->props['variant'] ?? 'default';

        $variantClass = $variantClasses[$variant] ?? $variantClasses['default'];

        return $this->getMergeClasses($defaultClasses, $variantClass);
    }

    public function render(): string
    {
        $attributes = $this->getAttributes();
        $class = $this->getComputedClasses();

        return <<<HTML
        <div class="$class" $attributes>{$this->children}</div>
        HTML;
    }
}
