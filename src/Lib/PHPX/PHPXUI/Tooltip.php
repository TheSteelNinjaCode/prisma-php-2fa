<?php

namespace Lib\PHPX\PHPXUI;

use Lib\PHPX\PHPX;
use Lib\PHPX\PHPXUI\Slot;
use Lib\MainLayout;
use Lib\StateManager;

class Tooltip extends PHPX
{
    public function __construct(array $props = [])
    {

        parent::__construct($props);

        $script = <<<'HTML'
        <script>
            document.addEventListener("DOMContentLoaded", () => {
                const tooltipTriggers = document.querySelectorAll("[aria-describedby]");

                tooltipTriggers.forEach((trigger) => {
                    const tooltipId = trigger.getAttribute("aria-describedby");
                    const tooltipElement = document.getElementById(tooltipId);

                    if (tooltipElement) {
                        trigger.addEventListener("mouseenter", () => {
                            // Get dimensions and positions
                            const triggerRect = trigger.getBoundingClientRect();
                            const tooltipRect = tooltipElement.getBoundingClientRect();
                            const side = tooltipElement.getAttribute("data-side") || "bottom";

                            let top = 0;
                            let left = 0;

                            // Calculate position based on side
                            switch (side) {
                                case "top":
                                    top = triggerRect.top - tooltipRect.height - 35;
                                    left = triggerRect.left + (triggerRect.width - tooltipRect.width - 94) / 2;
                                    break;
                                case "bottom":
                                    top = triggerRect.bottom + 8;
                                    left = triggerRect.left + (triggerRect.width - tooltipRect.width - 94) / 2;
                                    break;
                                case "left":
                                    top = triggerRect.top + (triggerRect.height - tooltipRect.height - 30) / 2;
                                    left = triggerRect.left - tooltipRect.width - 100;
                                    break;
                                case "right":
                                    top = triggerRect.top + (triggerRect.height - tooltipRect.height - 30) / 2;
                                    left = triggerRect.right + 6;
                                    break;
                            }

                            // Apply calculated position
                            tooltipElement.style.position = "absolute";
                            tooltipElement.style.top = `${top + window.scrollY}px`;
                            tooltipElement.style.left = `${left + window.scrollX}px`;
                            tooltipElement.style.visibility = "visible";
                            tooltipElement.setAttribute("data-state", "delayed-open");
                        });

                        trigger.addEventListener("mouseleave", () => {
                            tooltipElement.style.visibility = "hidden";
                            tooltipElement.setAttribute("data-state", "closed");
                        });
                    }
                });
            });
        </script>
        HTML;

        MainLayout::addFooterScript($script);
    }

    public function render(): string
    {
        return <<<HTML
        <div class="tooltip">
            {$this->children}
        </div>
        HTML;
    }
}

class TooltipProvider extends PHPX
{
    public function __construct(array $props = [])
    {
        parent::__construct($props);
    }

    public function render(): string
    {
        return <<<HTML
        <div class="tooltip-provider">
            {$this->children}
        </div>
        HTML;
    }
}

class TooltipTrigger extends PHPX
{
    public function __construct(array $props = [])
    {
        parent::__construct($props);

        StateManager::setState('tooltipId', uniqid('tooltip-'));
    }

    public function render(): string
    {
        $tooltipId = StateManager::getState('tooltipId');
        $asChild = $this->props['asChild'] ?? false;
        $attributes = $this->getAttributes([
            'data-state' => 'instant-open',
            'aria-describedby' => $tooltipId
        ]);
        $class = $this->getMergeClasses('cursor-pointer');

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
        <span class="$class" $attributes>
            {$this->children}
        </span>
        HTML;
    }
}

class TooltipContent extends PHPX
{
    public function __construct(array $props = [])
    {
        parent::__construct($props);
    }

    public function render(): string
    {
        $tooltipId = StateManager::getState('tooltipId');
        $attributes = $this->getAttributes([
            'data-state' => 'closed',
            'data-side' => $this->props['side'] ?? 'bottom',
            'data-align' => $this->props['align'] ?? 'center',
            'id' => $tooltipId,
        ]);
        $class = $this->getMergeClasses(
            'z-50 absolute hidden bg-primary px-3 py-1.5 text-xs text-primary-foreground
            rounded-md shadow-md data-[state=delayed-open]:block'
        );

        return <<<HTML
        <div class="$class" $attributes>
            {$this->children}
        </div>
        HTML;
    }
}
