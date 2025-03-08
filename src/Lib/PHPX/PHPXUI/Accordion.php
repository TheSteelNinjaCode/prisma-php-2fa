<?php

namespace Lib\PHPX\PHPXUI;

use Lib\MainLayout;
use Lib\PHPX\PHPX;
use Lib\StateManager;
use Lib\PHPX\PPIcons\ChevronDown;

class Accordion extends PHPX
{
    public function __construct(array $props = [])
    {
        parent::__construct($props);

        $script = <<<HTML
        <script>
            function toggleAccordion(id) {
                const allContents = document.querySelectorAll('[id^="content-"]');
                const allIcons = document.querySelectorAll('[id^="icon-"]');
                const allButtons = document.querySelectorAll('button[aria-expanded]');

                // Close all other accordion items
                allContents.forEach((content) => {
                    if (content.id !== 'content-' + id) {
                        content.classList.add('hidden');
                    }
                });

                allButtons.forEach((button) => {
                    if (button.getAttribute('aria-controls') !== 'content-' + id) {
                        button.setAttribute('aria-expanded', 'false');
                        button.setAttribute('data-state', 'closed');
                    }
                });

                // Toggle the selected accordion item
                const content = document.getElementById('content-' + id);
                const isExpanded = content.classList.contains('hidden');

                content.classList.toggle('hidden', !isExpanded);
                content.setAttribute('data-state', isExpanded ? 'open' : 'closed');

                // Update aria-expanded for the clicked button
                const button = document.querySelector('[aria-controls="content-' + id + '"]');
                button.setAttribute('aria-expanded', isExpanded.toString());
                button.setAttribute('data-state', isExpanded ? 'open' : 'closed');
            }
        </script>
        HTML;

        MainLayout::addFooterScript($script);
    }

    public function render(): string
    {
        $attributes = $this->getAttributes();
        $class = $this->getMergeClasses('w-full max-w-md mx-auto');

        return <<<HTML
        <div class="$class" $attributes>
            {$this->children}
        </div>
        HTML;
    }
}

class AccordionContent extends PHPX
{
    public function __construct(array $props = [])
    {
        parent::__construct($props);
    }

    public function render(): string
    {
        $accordionItemId = StateManager::getState('accordionItemId');
        $attributes = $this->getAttributes([
            'aria-labelledby' => $accordionItemId,
            'id' => "content-$accordionItemId",
            'role' => 'region',
            'data-state' => 'closed',
        ]);
        $class = $this->getMergeClasses('hidden px-4 py-2 text-gray-700 data-[state=closed]:animate-accordion-up data-[state=open]:animate-accordion-down');

        return <<<HTML
        <div class="$class" $attributes>   
            {$this->children}
        </div>
        HTML;
    }
}

class AccordionItem extends PHPX
{
    public function __construct(array $props = [])
    {
        parent::__construct($props);
    }

    public function render(): string
    {
        $attributes = $this->getAttributes();
        $class = $this->getMergeClasses('border-b');

        return <<<HTML
        <div class="$class" $attributes>
            {$this->children}
        </div>
        HTML;
    }
}

class AccordionTrigger extends PHPX
{
    public function __construct(array $props = [])
    {
        parent::__construct($props);

        $accordionItemId = $props['id'] ?? uniqid('accordion-item-');
        StateManager::setState('accordionItemId', $accordionItemId);
    }

    public function render(): string
    {
        $accordionItemId = StateManager::getState('accordionItemId');

        $attributes = $this->getAttributes([
            'aria-expanded' => 'false',
            'aria-controls' => "content-$accordionItemId",
            'data-state' => 'closed',
            'id' => $accordionItemId,
            'type' => 'button',
            'onclick' => "toggleAccordion('$accordionItemId')",
        ]);
        $class = $this->getMergeClasses('flex justify-between items-center font-medium w-full py-3 px-4 text-left text-gray-800 transition-all focus:outline-hidden hover:underline [&[data-state=open]>svg]:rotate-180');
        return <<<HTML
        <button class="$class" $attributes>
            {$this->children}
            <ChevronDown class="h-4 w-4 shrink-0 text-muted-foreground transition-transform duration-200" />
        </button>
        HTML;
    }
}
