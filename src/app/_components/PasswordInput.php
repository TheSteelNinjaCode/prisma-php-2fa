<?php

namespace app\_components;

use Lib\PHPX\PHPX;
use Lib\MainLayout;
use Lib\PHPX\PHPXUI\{Button, Input, Label};
use Lib\PHPX\PPIcons\{Eye, EyeOff};

class PasswordInput extends PHPX
{
    private string $passwordId;

    public function __construct(array $props = [])
    {
        parent::__construct($props);

        $this->passwordId = $props['id'] ?? uniqid('password-');

        $script = <<<HTML
        <script>
            function togglePasswordVisibility(element) {
                const passwordInput = document.getElementById('$this->passwordId');
                const visibilityIcon = document.getElementById('$this->passwordId-visibility');
                const visibilityOffIcon = document.getElementById('$this->passwordId-visibility-off');

                element.ariaLabel = passwordInput.type === 'password' ? 'Hide password' : 'Show password';

                if (passwordInput.type === 'password') {
                    passwordInput.type = 'text';

                    visibilityIcon.classList.add('hidden');
                    visibilityOffIcon.classList.remove('hidden');
                } else {
                    passwordInput.type = 'password';

                    visibilityIcon.classList.remove('hidden');
                    visibilityOffIcon.classList.add('hidden');
                }
            }
        </script>
        HTML;

        MainLayout::addFooterScript($script);
    }

    public function render(): string
    {
        $class = $this->getMergeClasses('relative');
        $attributes = $this->getAttributes([
            'id' => $this->passwordId,
            'type' => $this->props['type'] ?? 'password',
            'placeholder' => $this->props['placeholder'] ?? 'Ingresa tu contraseña',
        ]);

        return <<<HTML
        <div class="$class">
            <Input class="text-sm sm:text-base pr-8 appearance-none" $attributes />
            <Button
                type="button"
                variant="ghost"
                size="icon"
                class="absolute right-0 top-0 h-full px-3 py-2 hover:bg-transparent"
                onclick="togglePasswordVisibility(this)"
                aria-label="Ocultar contraseña">
                <Eye id="$this->passwordId-visibility" class="w-4 h-4 sm:w-5 sm:h-5" />
                <EyeOff id="$this->passwordId-visibility-off" class="w-4 h-4 sm:w-5 sm:h-5 hidden" />
            </Button>
        </div>
        HTML;
    }
}
