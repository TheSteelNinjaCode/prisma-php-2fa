<?php

namespace Lib\PHPX\PHPXUI;

use Lib\PHPX\PHPX;

class Table extends PHPX
{
    public function __construct(array $props = [])
    {
        parent::__construct($props);
    }

    public function render(): string
    {
        $attributes = $this->getAttributes();
        $class = $this->getMergeClasses('w-full caption-bottom text-sm');

        return <<<HTML
        <table class="$class" $attributes>
            {$this->children}
        </table>
        HTML;
    }
}

class TableHeader extends PHPX
{
    public function __construct(array $props = [])
    {
        parent::__construct($props);
    }

    public function render(): string
    {
        $attributes = $this->getAttributes();
        $class = $this->getMergeClasses('[&_tr]:border-b');

        return <<<HTML
        <thead class="$class" $attributes>
            {$this->children}
        </thead>
        HTML;
    }
}

class TableBody extends PHPX
{
    public function __construct(array $props = [])
    {
        parent::__construct($props);
    }

    public function render(): string
    {
        $attributes = $this->getAttributes();
        $class = $this->getMergeClasses('[&_tr:last-child]:border-0');

        return <<<HTML
        <tbody class="$class" $attributes>
            {$this->children}
        </tbody>
        HTML;
    }
}

class TableFooter extends PHPX
{
    public function __construct(array $props = [])
    {
        parent::__construct($props);
    }

    public function render(): string
    {
        $attributes = $this->getAttributes();
        $class = $this->getMergeClasses('border-t bg-muted/50 font-medium [&>tr]:last:border-b-0');

        return <<<HTML
        <tfoot class="$class" $attributes>
            {$this->children}
        </tfoot>
        HTML;
    }
}

class TableRow extends PHPX
{
    public function __construct(array $props = [])
    {
        parent::__construct($props);
    }

    public function render(): string
    {
        $attributes = $this->getAttributes();
        $class = $this->getMergeClasses('border-b transition-colors hover:bg-muted/50 data-[state=selected]:bg-muted');

        return <<<HTML
        <tr class="$class" $attributes>
            {$this->children}
        </tr>
        HTML;
    }
}

class TableHead extends PHPX
{
    public function __construct(array $props = [])
    {
        parent::__construct($props);
    }

    public function render(): string
    {
        $attributes = $this->getAttributes();
        $class = $this->getMergeClasses('h-10 px-2 text-left align-middle font-medium bg-background text-muted-foreground [&:has([role=checkbox])]:pr-0 [&>[role=checkbox]]:translate-y-[2px]');

        return <<<HTML
        <th class="$class" $attributes>
            {$this->children}
        </th>
        HTML;
    }
}

class TableCell extends PHPX
{
    public function __construct(array $props = [])
    {
        parent::__construct($props);
    }

    public function render(): string
    {
        $attributes = $this->getAttributes();
        $class = $this->getMergeClasses('p-2 align-middle [&:has([role=checkbox])]:pr-0 [&>[role=checkbox]]:translate-y-[2px]');

        return <<<HTML
        <td class="$class" $attributes>
            {$this->children}
        </td>
        HTML;
    }
}

class TableCaption extends PHPX
{
    public function __construct(array $props = [])
    {
        parent::__construct($props);
    }

    public function render(): string
    {
        $attributes = $this->getAttributes();
        $class = $this->getMergeClasses('mt-4 text-sm text-muted-foreground');

        return <<<HTML
        <caption class="$class" $attributes>
            {$this->children}
        </caption>
        HTML;
    }
}
