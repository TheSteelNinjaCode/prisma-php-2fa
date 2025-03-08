<?php

namespace Lib\PHPX\PHPXUI;

use Lib\PHPX\PHPX;
use Lib\PHPX\PPIcons\ChevronRight;

class Breadcrumb extends PHPX
{
    public function __construct(array $props = [])
    {
        parent::__construct($props);
    }

    public function render(): string
    {
        $attributes = $this->getAttributes([
            'aria-label' => 'breadcrumb',
        ]);
        $class = $this->getMergeClasses();

        return <<<HTML
        <nav class="$class" $attributes>{$this->children}</nav>
        HTML;
    }
}

class BreadcrumbList extends PHPX
{
    public function __construct(array $props = [])
    {
        parent::__construct($props);
    }

    public function render(): string
    {
        $attributes = $this->getAttributes();
        $class = $this->getMergeClasses(
            "flex flex-wrap items-center gap-1.5 break-words text-sm sm:gap-2.5"
        );

        return <<<HTML
        <ol class="$class" $attributes>{$this->children}</ol>
        HTML;
    }
}

class BreadcrumbItem extends PHPX
{
    public function __construct(array $props = [])
    {
        parent::__construct($props);
    }

    public function render(): string
    {
        $attributes = $this->getAttributes();
        $class = $this->getMergeClasses('inline-flex items-center gap-1.5');

        return <<<HTML
        <li class="$class" $attributes>{$this->children}</li>
        HTML;
    }
}

class BreadcrumbLink extends PHPX
{
    public function __construct(array $props = [])
    {
        parent::__construct($props);
    }

    public function render(): string
    {
        $attributes = $this->getAttributes();
        $class = $this->getMergeClasses('transition-colors text-muted-foreground hover:text-foreground');

        return <<<HTML
        <a class="$class" $attributes>{$this->children}</a>
        HTML;
    }
}

class BreadcrumbPage extends PHPX
{
    public function __construct(array $props = [])
    {
        parent::__construct($props);
    }

    public function render(): string
    {
        $attributes = $this->getAttributes([
            'aria-current' => 'page',
            'aria-disabled' => 'true',
            'role' => 'link',
        ]);
        $class = $this->getMergeClasses('font-normal text-foreground');

        return <<<HTML
        <span class="$class" $attributes>
            {$this->children}
        </span>
        HTML;
    }
}

class BreadcrumbSeparator extends PHPX
{
    public function __construct(array $props = [])
    {
        parent::__construct($props);
    }

    public function render(): string
    {
        $separator = $this->props['separator'] ?? (new ChevronRight())->render();
        $attributes = $this->getAttributes();
        $class = $this->getMergeClasses('[&>svg]:w-3.5 [&>svg]:h-3.5');

        return <<<HTML
        <li role="presentation" aria-hidden="true" class="$class" $attributes>
            {$separator}
        </li>
        HTML;
    }
}

class BreadcrumbEllipsis extends PHPX
{
    public function __construct(array $props = [])
    {
        parent::__construct($props);
    }

    public function render(): string
    {
        $attributes = $this->getAttributes([
            'aria-hidden' => 'true',
            'role' => 'presentation',
        ]);
        $class = $this->getMergeClasses('flex h-9 w-9 items-center justify-center');

        return <<<HTML
        <span class="$class" $attributes>
            <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg">
                <path d="..." />
            </svg>
            <span class="sr-only">More</span>
        </span>
        HTML;
    }
}
