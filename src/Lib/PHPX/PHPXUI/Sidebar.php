<?php


namespace Lib\PHPX\PHPXUI;

use Lib\PHPX\PHPX;
use Lib\PHPX\PPIcons\{PanelLeft, Check};
use Lib\PHPX\PHPXUI\{Button, Input, Separator, Sheet};
use Lib\PHPX\PHPXUI\{SheetContent, Skeleton};
use Lib\PHPX\PHPXUI\{Tooltip, TooltipContent, TooltipProvider, TooltipTrigger};

class NavigationMenu extends PHPX
{
    public function __construct(array $props = [])
    {
        parent::__construct($props);
    }
}