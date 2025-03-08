<?php

namespace Lib\PHPX\PHPXUI;

use Lib\PHPX\PHPX;
use Lib\MainLayout;
use Lib\PHPX\PHPXUI\Button;
use Lib\PHPX\PHPXUI\Slot;

class AlertDialog extends PHPX
{
    public function __construct(array $props = [])
    {
        parent::__construct($props);

        $script = <<<'HTML'
        <script>
            document.addEventListener("PPBodyLoaded", function () {
                document.querySelectorAll('[data-alert-dialog="container"]').forEach(function (container) {
                    const content = container.querySelector('[data-alert-dialog-content]');
                    console.log("🚀 ~ content:", content)
                    const overlay = container.querySelector('[data-alert-dialog-overlay]');
                    console.log("🚀 ~ overlay:", overlay)
                    if (!content) return;

                    function openDialog() {
                        content.classList.remove('hidden', 'opacity-0', 'scale-95');
                        content.classList.add('opacity-100', 'scale-100');
                        overlay?.classList.remove('hidden', 'opacity-0');
                        overlay?.classList.add('opacity-100');
                    }

                    function closeDialog() {
                        content.classList.remove('opacity-100', 'scale-100');
                        content.classList.add('opacity-0', 'scale-95');
                        overlay?.classList.remove('opacity-100');
                        overlay?.classList.add('opacity-0');
                        setTimeout(() => {
                            content.classList.add('hidden');
                            overlay?.classList.add('hidden');
                        }, 200);
                    }

                    container.addEventListener("click", function (event) {
                        if (event.target.closest('[data-alert-dialog-trigger]')) {
                            openDialog();
                        }
                        if (event.target.closest('[data-alert-dialog-cancel]') || event.target.closest('[data-alert-dialog-action]')) {
                            closeDialog();
                        }
                    });

                    overlay?.addEventListener("click", closeDialog);
                });
            });
        </script>
        HTML;

        MainLayout::addFooterScript($script);
    }

    public function render(): string
    {
        $attributes = $this->getAttributes();
        $class = $this->getMergeClasses("relative");

        return <<<HTML
        <div class="{$class}" data-alert-dialog="container" {$attributes}>
            {$this->children}
        </div>
        HTML;
    }
}

class AlertDialogOverlay extends PHPX
{
    public function __construct(array $props = [])
    {
        parent::__construct($props);
    }

    public function render(): string
    {
        $attributes = $this->getAttributes([
            'data-alert-dialog-overlay' => '',
        ]);
        $class = $this->getMergeClasses("fixed inset-0 z-50 bg-black/80 data-[state=open]:animate-in data-[state=closed]:animate-out data-[state=closed]:fade-out-0 data-[state=open]:fade-in-0");

        return <<<HTML
        <div class="{$class}" {$attributes}></div>
        HTML;
    }
}

class AlertDialogTrigger extends PHPX
{
    public function __construct(array $props = [])
    {
        parent::__construct($props);
    }

    public function render(): string
    {
        $attributes = $this->getAttributes([
            'data-alert-dialog-trigger' => '',
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
        <Button class="{$class}" {$attributes}>
            {$this->children}
        </Button>
        HTML;
    }
}

class AlertDialogContent extends PHPX
{
    public function __construct(array $props = [])
    {
        parent::__construct($props);
    }

    public function render(): string
    {
        $attributes = $this->getAttributes([
            'data-alert-dialog-content' => '',
        ]);
        $class = $this->getMergeClasses("fixed hidden left-[50%] top-[50%] z-50 grid w-full max-w-lg translate-x-[-50%] translate-y-[-50%] gap-4 border bg-background p-6 shadow-lg duration-200 data-[state=open]:animate-in data-[state=closed]:animate-out data-[state=closed]:fade-out-0 data-[state=open]:fade-in-0 data-[state=closed]:zoom-out-95 data-[state=open]:zoom-in-95 data-[state=closed]:slide-out-to-left-1/2 data-[state=closed]:slide-out-to-top-[48%] data-[state=open]:slide-in-from-left-1/2 data-[state=open]:slide-in-from-top-[48%] sm:rounded-lg");

        return <<<HTML
        <div class="{$class}" {$attributes}>
            <div class="modal-animate">
                {$this->children}
            </div>
        </div>
        HTML;
    }
}

class AlertDialogHeader extends PHPX
{
    public function __construct(array $props = [])
    {
        parent::__construct($props);
    }

    public function render(): string
    {
        $attributes = $this->getAttributes();
        $class = "flex flex-col space-y-2 text-center sm:text-left";

        return <<<HTML
        <div class="{$class}" {$attributes}>
            {$this->children}
        </div>
        HTML;
    }
}

class AlertDialogFooter extends PHPX
{
    public function __construct(array $props = [])
    {
        parent::__construct($props);
    }

    public function render(): string
    {
        $attributes = $this->getAttributes();
        $class = $this->getMergeClasses("flex flex-col-reverse sm:flex-row sm:justify-end sm:space-x-2");

        return <<<HTML
        <div class="{$class}" {$attributes}>
            {$this->children}
        </div>
        HTML;
    }
}

class AlertDialogTitle extends PHPX
{
    public function __construct(array $props = [])
    {
        parent::__construct($props);
    }

    public function render(): string
    {
        $attributes = $this->getAttributes();
        $class = $this->getMergeClasses("text-lg font-semibold");

        return <<<HTML
        <h2 class="{$class}" {$attributes}>
            {$this->children}
        </h2>
        HTML;
    }
}

class AlertDialogDescription extends PHPX
{
    public function __construct(array $props = [])
    {
        parent::__construct($props);
    }

    public function render(): string
    {
        $attributes = $this->getAttributes();
        $class = $this->getMergeClasses("text-sm text-muted-foreground");

        return <<<HTML
        <p class="{$class}" data-alert-dialog-description="" {$attributes}>
            {$this->children}
        </p>
        HTML;
    }
}

class AlertDialogAction extends PHPX
{
    public function __construct(array $props = [])
    {
        parent::__construct($props);
    }

    public function render(): string
    {
        $attributes = $this->getAttributes([
            'data-alert-dialog-action' => '',
        ]);
        $class = $this->getMergeClasses();

        return <<<HTML
        <Button class="{$class}" {$attributes}>
            {$this->children}
        </Button>
        HTML;
    }
}

class AlertDialogCancel extends PHPX
{
    public function __construct(array $props = [])
    {
        parent::__construct($props);
    }

    public function render(): string
    {
        $attributes = $this->getAttributes([
            'variant' => 'outline',
            'data-alert-dialog-cancel' => '',
        ]);
        $class = $this->getMergeClasses("mt-2 sm:mt-0");

        return <<<HTML
        <Button class="{$class}" {$attributes}>
            {$this->children}
        </Button>
        HTML;
    }
}
