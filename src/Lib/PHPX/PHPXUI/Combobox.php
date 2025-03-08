<?php

namespace Lib\PHPX\PHPXUI;

use Lib\PHPX\PHPXUI\{Button, Command, CommandEmpty, CommandGroup, CommandInput, CommandItem, CommandList, Popover, PopoverContent, PopoverTrigger};
use Lib\PHPX\PPIcons\{Check, ChevronsUpDown};

use Lib\PHPX\PHPX;

class Combobox extends PHPX
{
    public function __construct(array $props = [])
    {
        parent::__construct($props);
    }
}

