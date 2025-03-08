<?php

namespace Lib\PHPX\PHPXUI;

use Lib\PHPX\PHPX;
use Lib\StateManager;

class Form extends PHPX
{
    public function __construct(array $props = [])
    {
        parent::__construct($props);
        StateManager::setState('formErrors', []);

        // Validación automática si el formulario fue enviado
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $this->handleValidation($_POST);
        }
    }

    private function getValidationRules(): array
    {
        return [
            'username' => function ($value) {
                return strlen($value) >= 2 ? true : "El nombre de usuario debe tener al menos 2 caracteres.";
            }
        ];
    }

    private function handleValidation($data)
    {
        $rules = $this->getValidationRules();
        $errors = [];

        foreach ($rules as $field => $rule) {
            if (!isset($data[$field]) || $rule($data[$field]) !== true) {
                $errors[$field] = $rule($data[$field]);
            }
        }

        StateManager::setState('formErrors', $errors);
    }

    public function render(): string
    {
        $attributes = $this->getAttributes(['class' => 'w-2/3 space-y-6']);

        return <<<HTML
        <form {$attributes} method="POST">
            {$this->children}
        </form>
        HTML;
    }
}

class FormField extends PHPX
{
    public function __construct(array $props = [])
    {
        parent::__construct($props);
    }

    public function render(): string
    {
        return <<<HTML
        <div class="space-y-2">
            {$this->children}
        </div>
        HTML;
    }
}

class FormItem extends PHPX
{
    public function __construct(array $props = [])
    {
        parent::__construct($props);
    }

    public function render(): string
    {
        return <<<HTML
        <div class="space-y-1">
            {$this->children}
        </div>
        HTML;
    }
}

class FormLabel extends PHPX
{
    public function __construct(array $props = [])
    {
        parent::__construct($props);
    }

    public function render(): string
    {
        return <<<HTML
        <label class="block font-medium text-white">
            {$this->children}
        </label>
        HTML;
    }
}

class FormControl extends PHPX
{
    public function __construct(array $props = [])
    {
        parent::__construct($props);
    }

    public function render(): string
    {
        return <<<HTML
        <div>
            {$this->children}
        </div>
        HTML;
    }
}

class FormDescription extends PHPX
{
    public function __construct(array $props = [])
    {
        parent::__construct($props);
    }

    public function render(): string
    {
        return <<<HTML
        <p class="text-sm text-gray-400">
            {$this->children}
        </p>
        HTML;
    }
}

class FormMessage extends PHPX
{
    public function __construct(array $props = [])
    {
        parent::__construct($props);
    }

    public function render(): string
    {
        $fieldName = $this->props['field'] ?? '';
        $errors = StateManager::getState('formErrors');

        if (!empty($fieldName) && isset($errors[$fieldName])) {
            return <<<HTML
            <p class="text-sm text-red-500">{$errors[$fieldName]}</p>
            HTML;
        }

        return '';
    }
}
