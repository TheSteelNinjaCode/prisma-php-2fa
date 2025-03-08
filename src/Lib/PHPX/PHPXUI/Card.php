<?php

namespace Lib\PHPX\PHPXUI;

use Lib\PHPX\PHPX;

class Card extends PHPX
{
    public function __construct(array $props = [])
    {
        parent::__construct($props);
    }

    public function render(): string
    {
        $attributes = $this->getAttributes();
        $class = $this->getMergeClasses('rounded-lg border bg-card text-card-foreground shadow-sm');

        return <<<HTML
        <div class="$class" $attributes>{$this->children}</div>
        HTML;
    }
}

class CardContent extends PHPX
{
    public function __construct(array $props = [])
    {
        parent::__construct($props);
    }

    public function render(): string
    {
        $attributes = $this->getAttributes();
        $class = $this->getMergeClasses('p-6 pt-0');

        return <<<HTML
        <div class="$class" $attributes>{$this->children}</div>
        HTML;
    }
}

class CardDescription extends PHPX
{
    public function __construct(array $props = [])
    {
        parent::__construct($props);
    }

    public function render(): string
    {
        $attributes = $this->getAttributes();
        $class = $this->getMergeClasses('text-sm text-muted-foreground');

        return <<<HTML
        <p class="$class" $attributes>{$this->children}</p>
        HTML;
    }
}

class CardFooter extends PHPX
{
    public function __construct(array $props = [])
    {
        parent::__construct($props);
    }

    public function render(): string
    {
        $attributes = $this->getAttributes();
        $class = $this->getMergeClasses('flex items-center p-6 pt-0');

        return <<<HTML
        <div class="$class" $attributes>{$this->children}</div>
        HTML;
    }
}

class CardHeader extends PHPX
{
    public function __construct(array $props = [])
    {
        parent::__construct($props);
    }

    public function render(): string
    {
        $attributes = $this->getAttributes();
        $class = $this->getMergeClasses('flex flex-col space-y-1.5 p-6');

        return <<<HTML
        <div class="$class" $attributes>{$this->children}</div>
        HTML;
    }
}

class CardTitle extends PHPX
{
    public function __construct(array $props = [])
    {
        parent::__construct($props);
    }

    public function render(): string
    {
        $attributes = $this->getAttributes();
        $class = $this->getMergeClasses('text-2xl font-semibold leading-none tracking-tight');

        return <<<HTML
        <h3 class="$class" $attributes>{$this->children}</h3>
        HTML;
    }
}
