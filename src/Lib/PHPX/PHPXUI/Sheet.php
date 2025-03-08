<?php

namespace Lib\PHPX\PHPXUI;

use Lib\PHPX\PHPX;
use Lib\MainLayout;
use Lib\StateManager;
use Lib\PHPX\PHPXUI\Slot;

class Sheet extends PHPX
{
    public function __construct(array $props = [])
    {
        parent::__construct($props);

        $script = <<<'HTML'
        <script>
            function toggleSheet(id, open = true) {
                const sheet = document.getElementById(id);
                const background = document.getElementById(`${id}-background`);
                const sheetMain = document.getElementById(`${id}-main`);
                if (sheet) {
                    sheet.setAttribute('data-state', open ? 'open' : 'closed');
                    background.setAttribute('data-state', open ? 'open' : 'closed');
                    sheetMain.setAttribute('class', open ? 'fixed inset-0 z-50' : '');
                }

                background.addEventListener('click', () => {
                    toggleSheet(id, false);
                });
            }
        </script>
        HTML;

        MainLayout::addFooterScript($script);
    }

    public static function init(array $props = []): void
    {
        StateManager::setState('sheetId', uniqid('sheet-'));
    }

    public function render(): string
    {
        $attributes = $this->getAttributes();
        $class = $this->getMergeClasses();
        $sheetMainId = StateManager::getState('sheetId') . '-main';
        $sheetOverlay = new SheetOverlay();

        return <<<HTML
        <div class="$class" $attributes id="$sheetMainId">
            {$sheetOverlay}
            {$this->children}
        </div>
        HTML;
    }
}

class SheetOverlay extends PHPX
{
    public function __construct(array $props = [])
    {
        parent::__construct($props);
    }

    public function render(): string
    {
        $sheetIdBackground = StateManager::getState('sheetId') . '-background';
        $attributes = $this->getAttributes([
            'id' => $sheetIdBackground,
            'aria-hidden' => 'true',
            'style' => 'pointer-events: auto;',
            'data-state' => 'closed',
            'data-aria-hidden' => 'true',
            'aria-hidden' => 'true',
        ]);
        $class = $this->getMergeClasses('fixed inset-0 z-50 bg-black/80 data-[state=open]:animate-in data-[state=closed]:animate-out data-[state=closed]:fade-out-0 data-[state=open]:fade-in-0 data-[state=closed]:hidden');

        return <<<HTML
        <div class="$class" $attributes></div>
        HTML;
    }
}

class SheetContent extends PHPX
{
    public function __construct(array $props = [])
    {
        parent::__construct($props);
    }

    public function render(): string
    {
        $sheetId = StateManager::getState('sheetId') ?? '';
        $attributes = $this->getAttributes([
            'id' => $sheetId,
            'data-state' => 'closed',
            'role' => 'dialog',
        ]);
        $side = $this->props['side'] ?? 'right';

        $baseClass = 'fixed z-50 gap-4 bg-white p-6 shadow-lg transition ease-in-out data-[state=open]:animate-in data-[state=closed]:animate-out data-[state=closed]:duration-300 data-[state=open]:duration-500 data-[state=closed]:hidden';
        $right = 'inset-y-0 right-0 h-full w-3/4 border-l data-[state=closed]:slide-out-to-right data-[state=open]:slide-in-from-right sm:max-w-sm';
        $left = 'inset-y-0 left-0 h-full w-3/4 border-r data-[state=closed]:slide-out-to-left data-[state=open]:slide-in-from-left sm:max-w-sm';
        $top = 'inset-x-0 top-0 border-b data-[state=closed]:slide-out-to-top data-[state=open]:slide-in-from-top';
        $bottom = 'inset-x-0 bottom-0 border-t data-[state=closed]:slide-out-to-bottom data-[state=open]:slide-in-from-bottom';

        $class = $this->getMergeClasses(
            $baseClass,
            $side === 'right' ? $right : '',
            $side === 'left' ? $left : '',
            $side === 'top' ? $top : '',
            $side === 'bottom' ? $bottom : ''
        );

        return <<<HTML
        <div class="$class" $attributes>
            {$this->children}
        </div>
        HTML;
    }
}

class SheetTrigger extends PHPX
{
    public function __construct(array $props = [])
    {
        parent::__construct($props);
    }

    public function render(): string
    {
        $sheetId = StateManager::getState('sheetId') ?? '';
        $attributes = $this->getAttributes([
            'type' => 'button',
            'onclick' => "toggleSheet('$sheetId', true)",
        ]);
        $class = $this->getMergeClasses('text-sm font-semibold text-gray-700 cursor-pointer');
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
        </button>
        HTML;
    }
}

class SheetClose extends PHPX
{
    public function __construct(array $props = [])
    {
        parent::__construct($props);
    }

    public function render(): string
    {
        $sheetId = StateManager::getState('sheetId') ?? '';
        $attributes = $this->getAttributes([
            'type' => 'button',
            'onclick' => "toggleSheet('$sheetId', false)",
        ]);
        $class = $this->getMergeClasses('absolute right-4 top-4 rounded-sm opacity-70');

        return <<<HTML
        {$this->children}
        <button class="$class" $attributes>
            <svg
                xmlns="http://www.w3.org/2000/svg"
                width="24"
                height="24"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
                stroke-linecap="round"
                stroke-linejoin="round"
                class="h-4 w-4"
            >
                <path d="M18 6 6 18"></path>
                <path d="m6 6 12 12"></path>
            </svg>
            <span class="sr-only">Close</span>
        </button>
        HTML;
    }
}

class SheetHeader extends PHPX
{
    public function render(): string
    {
        $attributes = $this->getAttributes();
        $class = $this->getMergeClasses('flex flex-col space-y-2 text-center sm:text-left');

        return <<<HTML
        <div class="$class" $attributes>
            {$this->children}
        </div>
        HTML;
    }
}

class SheetFooter extends PHPX
{
    public function render(): string
    {
        $attributes = $this->getAttributes();
        $class = $this->getMergeClasses('flex flex-col-reverse sm:flex-row sm:justify-end sm:space-x-2');

        return <<<HTML
        <div class="$class" $attributes>
            {$this->children}
        </div>
        HTML;
    }
}

class SheetTitle extends PHPX
{
    public function render(): string
    {
        $attributes = $this->getAttributes();
        $class = $this->getMergeClasses('text-lg font-semibold text-gray-900');

        return <<<HTML
        <h2 class="$class" $attributes>
            {$this->children}
        </h2>
        HTML;
    }
}

class SheetDescription extends PHPX
{
    public function render(): string
    {
        $attributes = $this->getAttributes();
        $class = $this->getMergeClasses('text-sm text-gray-600');

        return <<<HTML
        <p class="$class" $attributes>
            {$this->children}
        </p>
        HTML;
    }
}
