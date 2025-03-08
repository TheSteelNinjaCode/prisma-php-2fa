<?php

namespace Lib\PHPX\PHPXUI;

use Lib\PHPX\PHPX;
use Lib\PHPX\PHPXUI\Input;

class InputOTP extends PHPX
{
    public function __construct(array $props = [])
    {
        parent::__construct($props);
    }

    public function render(): string
    {
        $class = $this->getMergeClasses("flex items-center gap-2 has-[:disabled]:opacity-50");
        $attributes = $this->getAttributes();

        return <<<HTML
        <div data-input-otp-container="true" class="$class" style="position: relative; cursor: text; user-select: none; pointer-events: none; --root-height: 0px;" $attributes>
            {$this->children}
            <div style="position:absolute;inset:0;pointer-events:none">
                <input type="text" class="opacity-0 w-full h-full" tabindex="-1" style="position:absolute;inset:0;opacity:0;pointer-events:none" />
            </div>
        </div>
        HTML;
    }
}

class InputOTPGroup extends PHPX
{
    public function __construct(array $props = [])
    {
        parent::__construct($props);
    }

    public function render(): string
    {
        $class = $this->getMergeClasses("flex items-center");
        $attributes = $this->getAttributes();

        return <<<HTML
        <div class="$class" $attributes>
            {$this->children}
        </div>
        HTML;
    }
}

class InputOTPSlot extends PHPX
{
    public function __construct(array $props = [])
    {
        parent::__construct($props);
    }

    public function render(): string
    {
        $class = $this->getMergeClasses("relative flex h-10 w-10 items-center justify-center border-y border-r border-input text-sm transition-all first:rounded-l-md first:border-l last:rounded-r-md");
        $attributes = $this->getAttributes();

        return <<<HTML
        <div class="$class" $attributes>
            {$this->children}
        </div>
        HTML;
    }
}

class InputOTPSeparator extends PHPX
{
    public function __construct(array $props = [])
    {
        parent::__construct($props);
    }

    public function render(): string
    {
        $class = $this->getMergeClasses();
        $attributes = $this->getAttributes([
            'role' => 'separator',
        ]);

        if (!$this->children) {
            $children = <<<HTML
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12.1" cy="12.1" r="1"></circle>
            </svg>
            HTML;
        }

        return <<<HTML
        <div class="$class" $attributes>
            {$children}
        </div>
        HTML;
    }
}
