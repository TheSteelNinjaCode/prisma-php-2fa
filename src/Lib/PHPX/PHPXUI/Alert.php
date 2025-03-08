<?php

namespace Lib\PHPX\PHPXUI;

use Lib\PHPX\PHPX;

class Alert extends PHPX
{
    public function __construct(array $props = [])
    {
        parent::__construct($props);
    }

    private function getComputedClasses(): string
    {
        $baseClass = 'relative w-full rounded-lg border p-4 [&>svg~*]:pl-7 [&>svg+div]:translate-y-[-3px] [&>svg]:absolute [&>svg]:left-4 [&>svg]:top-4 [&>svg]:text-inherit';

        $variantClasses = [
            'destructive' => 'border-destructive/50 text-destructive dark:border-destructive',
            'default' => 'bg-background text-foreground',
        ];

        $variant = $this->props['variant'] ?? 'default';
        $variantClass = $variantClasses[$variant] ?? $variantClasses['default'];

        return $this->getMergeClasses($baseClass, $variantClass);
    }


    public function render(): string
    {
        $attributes = $this->getAttributes([
            'role' => 'alert',
        ]);
        $class = $this->getComputedClasses();

        return <<<HTML
        <div class="$class" $attributes>
            {$this->children}
        </div>
        HTML;
    }
}

class AlertTitle extends PHPX
{
    public function __construct(array $props = [])
    {
        parent::__construct($props);
    }

    public function render(): string
    {
        $attributes = $this->getAttributes();
        $class = $this->getMergeClasses('mb-1 font-medium leading-none tracking-tight text-inherit');

        return <<<HTML
        <h5 class="$class" $attributes>
            {$this->children}
        </h5>
        HTML;
    }
}

class AlertDescription extends PHPX
{
    public function __construct(array $props = [])
    {
        parent::__construct($props);
    }

    public function render(): string
    {
        $attributes = $this->getAttributes();
        $class = $this->getMergeClasses('text-sm [&_p]:leading-relaxed text-inherit');

        return <<<HTML
        <div class="$class" $attributes>
            {$this->children}
        </div>
        HTML;
    }
}
