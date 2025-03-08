<?php

namespace Lib\PHPX\PHPXUI;

use Lib\PHPX\PHPX;
use Lib\PHPX\PPIcons\Check;
use Lib\PHPX\PHPXUI\Label;

class Checkbox extends PHPX
{
    public function __construct(array $props = [])
    {
        parent::__construct($props);
    }

    public function render(): string
    {
        // Genera un ID único si el usuario no lo proporciona
        $checkboxId = $this->props['id'] ?? uniqid('checkbox_');

        // Verificar si el checkbox está deshabilitado
        $isDisabled = isset($this->props['disabled']) && $this->props['disabled'] === "true";

        // Clases dinámicas para el checkbox
        $checkboxClasses = 'peer hidden ' . ($isDisabled ? 'cursor-not-allowed' : 'cursor-pointer');

        // Clases dinámicas para el label responsivo
        $label = new Label([
            'for' => $checkboxId,
            'class' => "text-xs sm:text-sm font-medium leading-none"
        ]);

        // Obtener atributos dinámicos
        $attributes = [
            'type' => 'checkbox',
            'class' => $checkboxClasses,
            'id' => $checkboxId,
            'style' => $isDisabled ? 'cursor: not-allowed;' : 'cursor: pointer;'
        ];

        // Si está deshabilitado, agregar el atributo `disabled`
        if ($isDisabled) {
            $attributes['disabled'] = 'disabled';
        }

        // Convertir atributos a cadena
        $attributesString = $this->formatAttributes($attributes);

        // Capturar el texto dentro del Checkbox si está definido
        $text = $this->props['label'] ?? '';
        $description = $this->props['description'] ?? '';

        // Renderizar el componente con el `Check` funcionando dinámicamente según el fondo
        return <<<HTML
        <div class="flex flex-col">
            <!-- Checkbox y Label alineados -->
            <div class="flex items-center space-x-[5px]">
                <!-- Input checkbox real -->
                <input {$attributesString} />

                <!-- Custom checkbox visual con icono dentro -->
                <label for="{$checkboxId}" class="w-4 h-4 sm:w-5 sm:h-5 flex items-center justify-center border border-gray-500 rounded-md bg-inherit peer-checked:bg-primary peer-checked:border-primary relative transition-all duration-300 ease-in-out" style="{$attributes['style']}">
                    <div class="absolute inset-0 flex items-center justify-center">
                        <Check class="w-3 h-3 sm:w-4 sm:h-4 text-background peer-checked:text-black dark:peer-checked:text-white" />
                    </div>
                </label>

                <!-- Label con el texto -->
                {$label->render()} {$text}
            </div>

            <!-- Mostrar descripción solo si el usuario la proporcionó -->
            {$this->renderDescription($description)}
        </div>
        HTML;
    }

    // Método para renderizar la Descripción solo si se proporciona
    private function renderDescription($description)
    {
        if (empty($description)) {
            return '';
        }
        return <<<HTML
        <p class="text-xs sm:text-sm text-muted-foreground ml-[25px]">{$description}</p>
        HTML;
    }

    // Método para convertir un array de atributos en una cadena
    private function formatAttributes(array $attributes): string
    {
        $string = '';
        foreach ($attributes as $key => $value) {
            $string .= $key . '="' . htmlspecialchars($value, ENT_QUOTES, 'UTF-8') . '" ';
        }
        return trim($string);
    }
}
