<?php

namespace Lib\PHPX\PHPXUI;

use Lib\PHPX\PHPX;
use Lib\MainLayout;
use Lib\PHPX\PPIcons\{ChevronRight, Check};

class DropdownMenu extends PHPX
{
    public function __construct(array $props = [])
    {
        parent::__construct($props);

        $script = <<<HTML
        <script>
            function openDropdown(root, trigger, content) {
                content.classList.remove("hidden");
                trigger.setAttribute("aria-expanded", "true");
            }

            function closeDropdown(root, trigger, content) {
                content.classList.add("hidden");
                trigger.setAttribute("aria-expanded", "false");
            }

            function toggleDropdown(root, trigger, content) {
                const isHidden = content.classList.contains("hidden");
                if (isHidden) {
                    openDropdown(root, trigger, content);
                } else {
                    closeDropdown(root, trigger, content);
                }
            }

            document.addEventListener('PPBodyLoaded', function() {
                document.querySelectorAll('.dropdown-menu-trigger').forEach(function(trigger) {
                    trigger.addEventListener('click', function(e) {
                        const dropdownRoot = trigger.closest('.dropdown-menu-root');
                        if (!dropdownRoot) return;

                        const dropdownContent = dropdownRoot.querySelector('.dropdown-menu-content');
                        if (!dropdownContent) return;

                        toggleDropdown(dropdownRoot, trigger, dropdownContent);
                    });
                });

                document.addEventListener('click', function(event) {
                    const expandedTriggers = document.querySelectorAll(
                        '.dropdown-menu-trigger[aria-expanded="true"]'
                    );

                    expandedTriggers.forEach(function(openTrigger) {
                        const root = openTrigger.closest('.dropdown-menu-root');
                        if (!root) return;

                        const content = root.querySelector('.dropdown-menu-content');
                        if (!content) return;

                        if (!openTrigger.contains(event.target) && !content.contains(event.target)) {
                            closeDropdown(root, openTrigger, content);
                        }
                    });
                });
            });
        </script>
        HTML;

        MainLayout::addFooterScript($script);
    }

    public function render(): string
    {
        $attributes = $this->getAttributes();
        $class = $this->getMergeClasses("dropdown-menu-root relative");

        return <<<HTML
        <div class="$class" $attributes>
            {$this->children}
        </div>
        HTML;
    }
}

class DropdownMenuTrigger extends PHPX
{
    public function __construct(array $props = [])
    {
        parent::__construct($props);
    }

    public function render(): string
    {
        $attributes = $this->getAttributes([
            'type' => 'button',
            'aria-haspopup' => 'menu',
            'aria-expanded' => 'false',
        ]);
        $class = $this->getMergeClasses(
            "dropdown-menu-trigger relative inline-flex items-center rounded-md border border-input bg-transparent px-3 py-2 text-sm shadow-sm ring-offset-background placeholder:text-muted-foreground focus:outline-none focus:ring-1 focus:ring-ring disabled:cursor-not-allowed disabled:opacity-50"
        );
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
        <button class="$class" $attributes>
            {$this->children}
            <span class="inline-block ml-2">
                <ChevronRight class="h-4 w-4 opacity-50" />
            </span>
        </button>
        HTML;
    }
}


class DropdownMenuContent extends PHPX
{
    public function __construct(array $props = [])
    {
        parent::__construct($props);
    }

    public function render(): string
    {
        $attributes = $this->getAttributes([
            'role' => 'menu',
        ]);
        $class = $this->getMergeClasses(
            "dropdown-menu-content absolute right-0 top-full z-50 min-w-[8rem] mt-1 overflow-hidden rounded-md border bg-popover p-1 text-popover-foreground shadow-md hidden"
        );

        return <<<HTML
        <div class="$class" $attributes>
            {$this->children}
        </div>
        HTML;
    }
}

class DropdownMenuGroup extends PHPX
{
    public function __construct(array $props = [])
    {
        parent::__construct($props);
    }

    public function render(): string
    {
        $attributes = $this->getAttributes([
            'role' => 'group',
        ]);
        $class = $this->getMergeClasses();

        return <<<HTML
        <div class="$class" $attributes>
            {$this->children}
        </div>
        HTML;
    }
}

class DropdownMenuSubTrigger extends PHPX
{
    public function __construct(array $props = [])
    {
        parent::__construct($props);
    }

    public function render(): string
    {
        $attributes = $this->getAttributes([
            'role' => 'menuitem',
        ]);
        $class = $this->getMergeClasses(
            "flex cursor-default gap-2 select-none items-center rounded-sm px-2 py-1.5 text-sm outline-none focus:bg-accent data-[state=open]:bg-accent hover:bg-accent hover:text-accent-foreground"
        );

        return <<<HTML
        <div class="$class" $attributes>
            {$this->children}
            <span class="ml-auto">
                <ChevronRight class="h-4 w-4 opacity-70" />
            </span>
        </div>
        HTML;
    }
}

class DropdownMenuSubContent extends PHPX
{
    public function __construct(array $props = [])
    {
        parent::__construct($props);
    }

    public function render(): string
    {
        $attributes = $this->getAttributes([
            'role' => 'menu',
        ]);
        $class = $this->getMergeClasses(
            "z-50 min-w-[8rem] overflow-hidden rounded-md border bg-popover p-1 text-popover-foreground shadow-lg hidden"
        );

        return <<<HTML
        <div class="$class" $attributes>
            {$this->children}
        </div>
        HTML;
    }
}

class DropdownMenuSub extends PHPX
{
    public function __construct(array $props = [])
    {
        parent::__construct($props);
    }

    public function render(): string
    {
        $attributes = $this->getAttributes();
        $class = $this->getMergeClasses("dropdown-menu-sub");

        return <<<HTML
        <div class="$class" $attributes>
            {$this->children}
        </div>
        HTML;
    }
}

class DropdownMenuRadioGroup extends PHPX
{
    public function __construct(array $props = [])
    {
        parent::__construct($props);
    }

    public function render(): string
    {
        $attributes = $this->getAttributes([
            'role' => 'radiogroup',
        ]);
        $class = $this->getMergeClasses();

        return <<<HTML
        <div class="$class" $attributes>
            {$this->children}
        </div>
        HTML;
    }
}

class DropdownMenuItem extends PHPX
{
    public function __construct(array $props = [])
    {
        parent::__construct($props);
    }

    public function render(): string
    {
        $attributes = $this->getAttributes([
            'role' => 'menuitem',
            'tabindex' => '-1',
        ]);
        $class = $this->getMergeClasses(
            "relative flex cursor-default select-none items-center gap-2 rounded-sm px-2 py-1.5 text-sm outline-none transition-colors focus:bg-accent focus:text-accent-foreground data-[disabled]:pointer-events-none data-[disabled]:opacity-50 [&>svg]:size-4 [&>svg]:shrink-0 hover:bg-accent hover:text-accent-foreground"
        );

        return <<<HTML
        <div class="$class" $attributes>
            {$this->children}
        </div>
        HTML;
    }
}

class DropdownMenuCheckboxItem extends PHPX
{
    public function __construct(array $props = [])
    {
        parent::__construct($props);
    }

    public function render(): string
    {
        $checked = $this->props['checked'] ?? 'false';
        $attributes = $this->getAttributes([
            'role' => 'menuitemcheckbox',
            'aria-checked' => $checked === 'true' ? 'true' : 'false',
        ]);
        $class = $this->getMergeClasses(
            "relative flex cursor-default select-none items-center rounded-sm py-1.5 pl-8 pr-2 text-sm outline-none transition-colors focus:bg-accent focus:text-accent-foreground data-[disabled]:pointer-events-none data-[disabled]:opacity-50"
        );

        $circleIcon = <<<HTML
        <span class="absolute left-2 flex h-3.5 w-3.5 items-center justify-center">
            <Check class="h-4 w-4" />
        </span>
        HTML;

        $checkIcon = ($checked === 'true') ? $circleIcon : '';

        return <<<HTML
        <div class="$class" $attributes>
            {$checkIcon}
            {$this->children}
        </div>
        HTML;
    }
}

class DropdownMenuRadioItem extends PHPX
{
    public function __construct(array $props = [])
    {
        parent::__construct($props);
    }

    public function render(): string
    {
        $value = $this->props['value'] ?? '';
        $attributes = $this->getAttributes([
            'role' => 'menuitemradio',
            'aria-checked' => 'false',
            'data-value' => $value,
        ]);
        $class = $this->getMergeClasses(
            "relative flex cursor-default select-none items-center rounded-sm py-1.5 pl-8 pr-2 text-sm outline-none transition-colors focus:bg-accent focus:text-accent-foreground data-[disabled]:pointer-events-none data-[disabled]:opacity-50"
        );

        return <<<HTML
        <div class="$class" $attributes>
            <span class="absolute left-2 flex h-3.5 w-3.5 items-center justify-center">
                <svg class="h-2 w-2 fill-current" fill="currentColor" viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="4"></circle>
                </svg>
            </span>
            {$this->children}
        </div>
        HTML;
    }
}

class DropdownMenuLabel extends PHPX
{
    public function __construct(array $props = [])
    {
        parent::__construct($props);
    }

    public function render(): string
    {
        $attributes = $this->getAttributes();
        $class = $this->getMergeClasses("px-2 py-1.5 text-sm font-semibold");

        return <<<HTML
        <div class="$class" $attributes>
            {$this->children}
        </div>
        HTML;
    }
}

class DropdownMenuSeparator extends PHPX
{
    public function __construct(array $props = [])
    {
        parent::__construct($props);
    }

    public function render(): string
    {
        $attributes = $this->getAttributes();
        $class = $this->getMergeClasses("-mx-1 my-1 h-px bg-muted");

        return <<<HTML
        <div class="$class" $attributes></div>
        HTML;
    }
}

class DropdownMenuShortcut extends PHPX
{
    public function __construct(array $props = [])
    {
        parent::__construct($props);
    }

    public function render(): string
    {
        $attributes = $this->getAttributes();
        $class = $this->getMergeClasses("ml-auto text-xs tracking-widest opacity-60");

        return <<<HTML
        <span class="$class" $attributes>
            {$this->children}
        </span>
        HTML;
    }
}
