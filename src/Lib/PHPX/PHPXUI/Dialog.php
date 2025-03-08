<?php

namespace Lib\PHPX\PHPXUI;

use Lib\PHPX\PHPX;
use Lib\StateManager;
use Lib\MainLayout;

class Dialog extends PHPX
{
    public function __construct(array $props = [])
    {
        parent::__construct($props);

        $dialogId = StateManager::getState('dialogId') ?? '';
        $callback = $props['callback'] ?? null;

        $script = <<<HTML
        <script>
            document.addEventListener('PPBodyLoaded', () => {
                const dialog = document.getElementById('$dialogId');
                if (dialog) {
                    const dialogWrapper = dialog.querySelector('.wrapper');
                    const body = document.body;

                    // Intercept showModal to disable scrolling
                    const originalShowModal = dialog.showModal.bind(dialog);
                    dialog.showModal = () => {
                        originalShowModal();
                        body.classList.add('overflow-hidden');
                    };

                    // Add close event listener to re-enable scrolling
                    dialog.addEventListener('close', () => {
                        body.classList.remove('overflow-hidden');
                    });
                    
                    dialog.addEventListener('click', (event) => {
                        if (!dialogWrapper.contains(event.target)) {
                            dialog.close();
                        }
                    });
                }
            });
        </script>
        HTML;

        if ($callback) {
            $callbackScript = <<<HTML
            <script>
                document.addEventListener('PPBodyLoaded', () => {
                    const dialog = document.getElementById('$dialogId');
                    if (dialog) {
                        // Intercept showModal to trigger callbacks
                        const originalShowModal = dialog.showModal.bind(dialog);
                        dialog.showModal = () => {
                            originalShowModal();
                            if (typeof window['$callback'] === 'function') {
                                window['$callback']({'state': 'open', 'id': '$dialogId'});
                            }
                        };

                        // Add close event listener for callback
                        dialog.addEventListener('close', () => {
                            if (typeof window['$callback'] === 'function') {
                                window['$callback']({'state': 'close', 'id': '$dialogId'});
                            }
                        });
                    }
                });
            </script>
            HTML;

            MainLayout::addFooterScript($script, $callbackScript);
        }
    }

    public function render(): string
    {
        $attributes = $this->getAttributes();
        $class = $this->getMergeClasses('flex flex-col gap-4');

        return <<<HTML
        <div class="{$class}" $attributes>
            {$this->children}
        </div>
        HTML;
    }
}

class DialogTrigger extends PHPX
{
    public function __construct(array $props = [])
    {
        parent::__construct($props);

        $dialogId = $props['id'] ?? uniqid('phpxuiDialog');
        StateManager::setState('dialogId', $dialogId);

        $script = <<<HTML
        <script>
            document.addEventListener('PPBodyLoaded', () => {
                const dialog = document.getElementById('$dialogId');
                if (dialog) {
                    const dialogWrapper = dialog.querySelector('.wrapper');
                    dialog.addEventListener('click', (event) => {
                        if (!dialogWrapper.contains(event.target)) {
                            dialog.close();
                        }
                    });
                }
            });
        </script>
        HTML;

        MainLayout::addFooterScript($script);
    }

    public function render(): string
    {
        // Default keys always present in props
        $defaultKeys = ['class', 'children'];

        // Check if 'id' is the only non-default key in props
        $nonDefaultKeys = array_diff(array_keys($this->props), $defaultKeys);
        if ($nonDefaultKeys === ['id']) {
            return '';
        }

        $dialogId = StateManager::getState('dialogId') ?? '';
        $attributes = $this->getAttributes([
            'type' => 'button',
            'aria-haspopup' => 'dialog',
            'aria-expanded' => 'false',
            'aria-controls' => $dialogId,
            'onclick' => "$dialogId.showModal()",
        ]);
        $class = $this->getMergeClasses();
        $asChild = $this->props['asChild'] ?? false;

        // Handle the 'asChild' case
        if ($asChild) {
            // Remove 'id' to prevent duplicates
            $attributesArray = array_filter($this->attributesArray, fn($key) => $key !== 'id', ARRAY_FILTER_USE_KEY);

            $slot = new Slot([
                'class' => $class,
                'asChild' => true,
                ...$attributesArray, // Ensure no 'id' here
            ]);
            $slot->children = $this->children;
            return $slot->render();
        }

        // Default button rendering
        return <<<HTML
        <button class="{$class}" $attributes>
            {$this->children}
        </button>
        HTML;
    }
}

class DialogContent extends PHPX
{
    public function __construct(array $props = [])
    {
        parent::__construct($props);
    }

    public function render(): string
    {
        $dialogId = StateManager::getState("dialogId") ?? '';
        $attributes = $this->getAttributes([
            'id' => $dialogId,
        ]);
        $class = $this->getMergeClasses('fixed top-[50%] left-[50%] z-50 w-full max-w-[calc(100%-2rem)] translate-x-[-50%] translate-y-[-50%] gap-4 rounded-lg border p-6 shadow-lg duration-200 sm:max-w-lg bg-background text-foreground backdrop:bg-foreground backdrop:opacity-10 backdrop:backdrop-blur-sm dialog:backdrop-visible');

        return <<<HTML
        <dialog class="{$class}" $attributes>
            <div class="wrapper p-6">
                {$this->children}
                <form method="dialog" class="modal-backdrop">
                    <button type="submit" class="absolute right-4 top-4 rounded-sm opacity-70 ring-offset-background transition-opacity hover:opacity-100 focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 disabled:pointer-events-none">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4">
                            <path d="M18 6 6 18"></path>
                            <path d="m6 6 12 12"></path>
                        </svg>
                        <span class="sr-only">Close</span>
                    </button>
                </form>
            </div>
        </dialog>
        HTML;
    }
}

class DialogHeader extends PHPX
{
    public function __construct(array $props = [])
    {
        parent::__construct($props);
    }

    public function render(): string
    {
        $attributes = $this->getAttributes();
        $class = $this->getMergeClasses('flex flex-col space-y-1.5 text-center sm:text-left');

        return <<<HTML
        <div class="{$class}" $attributes>
            {$this->children}
        </div>
        HTML;
    }
}

class DialogTitle extends PHPX
{
    public function __construct(array $props = [])
    {
        parent::__construct($props);
    }

    public function render(): string
    {
        $attributes = $this->getAttributes();
        $class = $this->getMergeClasses('text-lg font-semibold leading-none tracking-tight');

        return <<<HTML
        <h2 class="{$class}" $attributes>
            {$this->children}
        </h2>
        HTML;
    }
}

class DialogDescription extends PHPX
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
        <p class="{$class}" $attributes>
            {$this->children}
        </p>
        HTML;
    }
}

class DialogFooter extends PHPX
{
    public function __construct(array $props = [])
    {
        parent::__construct($props);
    }

    public function render(): string
    {
        $attributes = $this->getAttributes();
        $class = $this->getMergeClasses('flex flex-col-reverse sm:flex-row sm:justify-end sm:space-x-2');

        return <<<HTML
        <div class="{$class}" $attributes>
            {$this->children}
        </div>
        HTML;
    }
}
