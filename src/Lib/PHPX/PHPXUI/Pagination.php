<?php

namespace Lib\PHPX\PHPXUI;

use Lib\PHPX\PHPX;

class Pagination extends PHPX
{
    public function __construct(array $props = [])
    {
        parent::__construct($props);
    }

    public function render(): string
    {
        $attributes = $this->getAttributes();
        $class = $this->getMergeClasses('flex items-center justify-center space-x-0.5 sm:space-x-1');

        return <<<HTML
        <nav class="$class" $attributes>
            {$this->children}
        </nav>
        HTML;
    }
}

class PaginationContent extends PHPX
{
    public function __construct(array $props = [])
    {
        parent::__construct($props);
    }

    public function render(): string
    {
        $attributes = $this->getAttributes();
        $class = $this->getMergeClasses('flex items-center space-x-0.5 sm:space-x-1');

        return <<<HTML
        <ul class="$class" $attributes>
            {$this->children}
        </ul>
        HTML;
    }
}

class PaginationItem extends PHPX
{
    public function __construct(array $props = [])
    {
        parent::__construct($props);
    }

    public function render(): string
    {
        $attributes = $this->getAttributes();
        $class = $this->getMergeClasses('inline-block px-1 sm:px-1.5');

        return <<<HTML
        <li class="$class" $attributes>
            {$this->children}
        </li>
        HTML;
    }
}

class PaginationLink extends PHPX
{
    public function __construct(array $props = [])
    {
        parent::__construct($props);
    }

    public function render(): string
    {
        $href = $this->props['href'] ?? '#';
        $isActive = isset($this->props['isActive']) && $this->props['isActive'];
        $attributes = $this->getAttributes(['href' => $href]);

        $class = $this->getMergeClasses(
            'px-3 py-2 rounded-lg text-sm transition duration-200 text-black dark:text-white',
            'hover:bg-[#27272A] hover:text-white dark:hover:bg-gray-600',
            $isActive ? 'border border-black dark:border-white text-black dark:text-white font-bold shadow-md scale-105' : ''
        );

        return <<<HTML
        <a class="$class" $attributes>
            {$this->children}
        </a>
        HTML;
    }
}

class PaginationEllipsis extends PHPX
{
    public function __construct(array $props = [])
    {
        parent::__construct($props);
    }

    public function render(): string
    {
        return <<<HTML
        <span class="px-2 py-1 text-gray-500 dark:text-gray-400 text-lg">...</span>
        HTML;
    }
}

class PaginationPrevious extends PHPX
{
    public function __construct(array $props = [])
    {
        parent::__construct($props);
    }

    public function render(): string
    {
        $href = $this->props['href'] ?? '#';
        $attributes = $this->getAttributes(['href' => $href]);

        $class = $this->getMergeClasses(
            'px-3 py-2 rounded-lg text-sm transition duration-200 text-black dark:text-white',
            'hover:bg-[#27272A] hover:text-white dark:hover:bg-gray-600'
        );

        return <<<HTML
        <a class="$class" $attributes>
            &laquo; Anterior
        </a>
        HTML;
    }
}

class PaginationNext extends PHPX
{
    public function __construct(array $props = [])
    {
        parent::__construct($props);
    }

    public function render(): string
    {
        $href = $this->props['href'] ?? '#';
        $attributes = $this->getAttributes(['href' => $href]);

        $class = $this->getMergeClasses(
            'px-3 py-2 rounded-lg text-sm transition duration-200 text-black dark:text-white',
            'hover:bg-[#27272A] hover:text-white dark:hover:bg-gray-600'
        );

        return <<<HTML
        <a class="$class" $attributes>
            Siguiente &raquo;
        </a>
        HTML;
    }
}
