<?php

namespace Lib\PHPX\PHPXUI;

use Lib\PHPX\PHPX;
use Lib\MainLayout;

class ToggleSwitch extends PHPX
{
    public function __construct(array $props = [])
    {
        parent::__construct($props);

        // Add the toggle script to handle click events
        $script = <<<HTML
        <script>
            document.addEventListener('click', (event) => {
                const toggleSwitch = event.target.closest('[role="switch"]');
                if (toggleSwitch && !toggleSwitch.disabled) {
                    const isChecked = toggleSwitch.getAttribute('aria-checked') === 'true';
                    toggleSwitch.setAttribute('aria-checked', String(!isChecked));
                    toggleSwitch.setAttribute('data-state', isChecked ? 'unchecked' : 'checked');
                    toggleSwitch.value = isChecked ? 'off' : 'on';
                    toggleSwitch.dispatchEvent(new Event('change'));
                    toggleSwitch.setAttribute('checked', !isChecked);

                    const thumb = toggleSwitch.querySelector('span[data-state]');
                    if (thumb) {
                        thumb.setAttribute('data-state', isChecked ? 'unchecked' : 'checked');
                    }
                }
            });
        </script>
        HTML;

        MainLayout::addFooterScript($script);
    }

    public function render(): string
    {
        $checked = $this->props['checked'] ?? false;
        $dataState = $checked ? 'checked' : 'unchecked';

        $attributes = $this->getAttributes([
            'role' => 'switch',
            'aria-checked' => $checked ? 'true' : 'false',
            'data-state' => $dataState,
            'value' => 'on',
            'type' => 'button',
        ]);

        $class = $this->getMergeClasses(
            'peer inline-flex h-6 w-11 shrink-0 cursor-pointer items-center rounded-full border-2 border-transparent transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 focus-visible:ring-offset-background disabled:cursor-not-allowed disabled:opacity-50 data-[state=checked]:bg-primary data-[state=unchecked]:bg-gray-300'
        );

        return <<<HTML
        <button class="$class" $attributes>
            <span
                data-state="$dataState"
                class="pointer-events-none block h-5 w-5 rounded-full bg-primary-foreground shadow-lg ring-0 transition-transform duration-300 ease-in-out
                data-[state=checked]:translate-x-5 data-[state=unchecked]:translate-x-0"
            ></span>
        </button>
        HTML;
    }
}
