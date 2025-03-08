<?php

namespace Lib\PHPX\PHPXUI;

use Lib\PHPX\PHPX;
use Lib\PHPX\PPIcons\ChevronDown;

class Select extends PHPX
{
    public function __construct(array $props = [])
    {
        parent::__construct($props);
    }

    public function render(): string
    {
        $attributes = $this->getAttributes([
            'title' => $this->props['title'] ?? 'Select',
        ]);
        $class = $this->getMergeClasses('block w-full rounded-md border border-gray-300 bg-white px-3 py-2 pr-10 text-sm shadow-sm ring-offset-background focus:outline-none focus:ring-1 focus:ring-ring bg-background');

        return <<<HTML
        <select class="$class" $attributes>
            {$this->children}
        </select>
        HTML;
    }
}

class SelectGroup extends PHPX
{
    public function __construct(array $props = [])
    {
        parent::__construct($props);
    }

    public function render(): string
    {
        $attributes = $this->getAttributes();
        $class = $this->getMergeClasses();

        return <<<HTML
        <optgroup class="$class" $attributes>
            {$this->children}
        </optgroup>
        HTML;
    }
}

class SelectItem extends PHPX
{
    public function __construct(array $props = [])
    {
        parent::__construct($props);
    }

    public function render(): string
    {
        $attributes = $this->getAttributes();
        $class = $this->getMergeClasses();

        return <<<HTML
        <option class="$class" $attributes>
            {$this->children}
        </option>
        HTML;
    }
}
