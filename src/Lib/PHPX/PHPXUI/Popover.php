<?php

namespace Lib\PHPX\PHPXUI;

use Lib\MainLayout;
use Lib\PHPX\PHPX;
use Lib\PHPX\PHPXUI\Slot;
use Lib\StateManager;

class Popover extends PHPX
{
    public function __construct(array $props = [])
    {
        parent::__construct($props);

        $script = <<<'HTML'
        <script>
            // Wait for DOMContentLoaded so elements exist
            document.addEventListener('PPBodyLoaded', function () {
                const triggers = document.querySelectorAll('[data-popover-trigger]');
                const contents = document.querySelectorAll('[data-popover-content]');

                // When a trigger is clicked, toggle the matching popover
                triggers.forEach((trigger) => {
                    // When a trigger is clicked, toggle the matching popover
                    trigger.addEventListener('click', function (event) {
                        event.preventDefault();
                        event.stopPropagation();

                        const contentId = trigger.dataset.popoverTrigger;
                        const popoverContent = document.querySelector(`[data-popover-content="${contentId}"]`);
                        if (!popoverContent) return;

                        // 1) Temporarily show the popover so we can measure its size
                        popoverContent.style.display = 'block';
                        const popoverWidth = popoverContent.offsetWidth;
                        const popoverHeight = popoverContent.offsetHeight;
                        // Hide it again if necessary
                        popoverContent.style.display = '';

                        // 2) Get the trigger’s size and position
                        const triggerRect = trigger.getBoundingClientRect();

                        // 4) Decide vertical position (top/bottom) based on available space
                        const spaceBelow = window.innerHeight - triggerRect.bottom;
                        const spaceAbove = triggerRect.top;
                        const sideOffset = 4; // or parseInt(popoverContent.dataset.sideOffset) || 4
                        let popoverTop;

                        if (spaceBelow >= popoverHeight + sideOffset) {
                            // Enough space below
                            popoverTop = triggerRect.bottom + sideOffset;
                        } else if (spaceAbove >= popoverHeight + sideOffset) {
                            // Enough space above
                            popoverTop = triggerRect.top - popoverHeight - sideOffset;
                        } else {
                            // Fallback: choose whichever side has more space
                            if (spaceBelow > spaceAbove) {
                                popoverTop = triggerRect.bottom + sideOffset;
                            } else {
                                popoverTop = triggerRect.top - popoverHeight - sideOffset;
                            }
                        }

                        // 5) Clamp so it doesn’t go off-screen
                        popoverTop = Math.max(0, popoverTop); // do not allow popover to go above the top of the viewport
                        popoverTop = Math.min(popoverTop, window.innerHeight - popoverHeight); // do not allow popover to go beyond bottom

                        // 6) Apply final position
                        popoverContent.style.position = 'fixed';
                        popoverContent.style.top  = popoverTop + 'px';

                        // Toggle popover open/closed
                        popoverContent.dataset.state = (popoverContent.dataset.state === 'open') ? 'closed' : 'open';
                    });
                });
                
                // Clicking inside the popover shouldn't close it
                contents.forEach((content) => {
                    content.addEventListener('click', function (event) {
                        event.stopPropagation();
                    });
                });

                // Clicking anywhere else closes all popovers
                document.addEventListener('click', function (event) {
                    contents.forEach((content) => {
                        content.dataset.state = 'closed';
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
        $class = $this->getMergeClasses('relative inline-block');

        return <<<HTML
        <div class="$class" $attributes>
            {$this->children}
        </div>
        HTML;
    }
}

class PopoverTrigger extends PHPX
{
    public function __construct(array $props = [])
    {
        parent::__construct($props);

        $popoverId = $this->props['id'] ?? uniqid('popover-');
        StateManager::setState('popoverId', $popoverId);
    }

    public function render(): string
    {
        $popoverId = StateManager::getState('popoverId');

        // Merge the default attributes with data-popover-trigger
        $attributes = $this->getAttributes([
            'data-popover-trigger' => $popoverId,
        ]);
        $class = $this->getMergeClasses();
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

class PopoverAnchor extends PHPX
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
        <div class="$class" $attributes>
            {$this->children}
        </div>
        HTML;
    }
}

class PopoverContent extends PHPX
{
    public function render(): string
    {
        $popoverId = StateManager::getState('popoverId');
        $attributes = $this->getAttributes([
            'data-popover-content' => $popoverId,
            'data-state'           => 'closed',
            'data-side'            => $this->props['side'] ?? 'bottom',
            'data-align'           => $this->props['align'] ?? 'center',
            'data-side-offset'     => $this->props['sideOffset'] ?? 4,
        ]);

        $class = $this->getMergeClasses('
            hidden
            z-50 w-72 rounded-md border bg-popover p-4 text-popover-foreground shadow-md outline-none
            data-[state=open]:animate-in data-[state=closed]:animate-out
            data-[state=closed]:fade-out-0 data-[state=open]:fade-in-0
            data-[state=closed]:zoom-out-95 data-[state=open]:zoom-in-95
            data-[side=bottom]:slide-in-from-top-2 data-[side=left]:slide-in-from-right-2
            data-[side=right]:slide-in-from-left-2 data-[side=top]:slide-in-from-bottom-2
            data-[state=open]:block
            data-[state=closed]:hidden
        ');

        return <<<HTML
        <div class="$class" $attributes>
            {$this->children}
        </div>
        HTML;
    }
}
