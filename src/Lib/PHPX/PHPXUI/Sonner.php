<?php


namespace Lib\PHPX\PHPXUI;

use Lib\PHPX\PHPX;
use Lib\PHPX\PHPXUI\{Toast, Button};
use Lib\PHPX\PPIcons\{Toaster as Sonner};

class Toaster extends PHPX
{
    public function __construct(array $props = [])
    {
        parent::__construct($props);
    }
}