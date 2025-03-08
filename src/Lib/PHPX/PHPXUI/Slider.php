<?php

namespace Lib\PHPX\PHPXUI;

use Lib\PHPX\PHPX;
use Lib\MainLayout;

class Slider extends PHPX
{
    public function __construct(array $props = [])
    {
        parent::__construct($props);
    }

    public function render(): string
    {
        $min = 0;
        $max = $this->props['max'] ?? 100;
        $step = $this->props['step'] ?? 1;
        $defaultValue = $this->props['defaultValue'] ?? ($max / 2);
        $class = $this->props['class'] ?? 'w-[60%]';

        return <<<HTML
        <div class="$class flex items-center">
            <input 
                type="range" 
                min="$min" 
                max="$max" 
                step="$step" 
                value="{$defaultValue}" 
                class="peer w-full cursor-pointer appearance-none bg-neutral-800 rounded-lg h-1 outline-none transition-all duration-200 
                [&::-webkit-slider-thumb]:appearance-none 
                [&::-webkit-slider-thumb]:w-4 
                [&::-webkit-slider-thumb]:h-4 
                [&::-webkit-slider-thumb]:bg-white 
                [&::-webkit-slider-thumb]:rounded-full 
                [&::-webkit-slider-thumb]:transition-transform 
                peer-active:[&::-webkit-slider-thumb]:scale-125" 
            />
        </div>
        HTML;
    }
}
