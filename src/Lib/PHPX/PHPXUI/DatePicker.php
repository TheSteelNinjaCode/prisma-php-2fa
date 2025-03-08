<?php

namespace Lib\PHPX\PHPXUI;

use Lib\MainLayout;
use Lib\PHPX\PHPX;
use Lib\PHPX\PHPXUI\{Button, Calendar, Popover, PopoverContent, PopoverTrigger};
use Lib\PHPX\PPIcons\{Calendar as CalendarIcon};

class DatePicker extends PHPX
{
    private string $datePickerId;

    public function __construct(array $props = [])
    {

        parent::__construct($props);

        $this->datePickerId = $this->props['id'] ?? uniqid('phpxui-date-picker-');

        $script = <<<'HTML'
        <script>
            function selectedDatePicker(data) {
                const date = new Date(data.year, data.month - 1, data.day);
                const formattedDate = new Intl.DateTimeFormat('en-US', {
                    day: 'numeric',
                    month: 'long',
                    year: 'numeric',
                }).format(date);
                const onSelectReturn = data.onselectReturn;
                const datePickerContainer = document.getElementById(onSelectReturn);
                const datePickerTrigger = datePickerContainer.querySelector(`[phpxui-date-picker="${onSelectReturn}-trigger"]`);
                datePickerTrigger.classList.remove('text-muted-foreground');
                const dateContent = datePickerTrigger.querySelector('span');
                dateContent.innerText = formattedDate;
                const hiddenInput = datePickerContainer.querySelector(`[phpxui-date-picker="${onSelectReturn}-input"]`);
                hiddenInput.value = date.toISOString();
            }

            function setSelectedDatePicker(selectedDatePickerId, value = null) {
                const datePickerTrigger = document.getElementById(selectedDatePickerId);
                const dateContent = datePickerTrigger.querySelector('span');
                const date = value ? new Date(value) : null;

                if (date) {
                    const formattedDate = new Intl.DateTimeFormat('en-US', {
                        day: 'numeric',
                        month: 'long',
                        year: 'numeric',
                    }).format(date);
                    dateContent.innerText = formattedDate;
                    const hiddenInput = document.querySelector('input[name="datePicker"]');
                    hiddenInput.value = date.toISOString();
                } else {
                    dateContent.innerText = 'Pick a date';
                }
            }
        </script>
        HTML;

        MainLayout::addFooterScript($script);
    }

    public function render(): string
    {
        $attributes = $this->getAttributes([
            'id' => $this->datePickerId,
        ]);
        $class = $this->getMergeClasses();
        $hiddenInputName = $this->props['name'] ?? 'datePicker';

        return <<<HTML
        <Popover class="$class" $attributes>
            <PopoverTrigger asChild="true">
                <Button
                phpxui-date-picker="$this->datePickerId-trigger"
                class="w-full justify-start text-left font-normal text-muted-foreground"
                variant="outline">
                    <CalendarIcon />
                    <span>Pick a date</span>
                </Button>
            </PopoverTrigger>
            <PopoverContent class="w-auto p-0" align="start">
                <input phpxui-date-picker="$this->datePickerId-input" type="hidden" name="$hiddenInputName" />
                <Calendar onselect-return="$this->datePickerId" onselect-date="selectedDatePicker" mode="single"/>
            </PopoverContent>
        </Popover>
        HTML;
    }
}
