<?php

namespace Lib\PHPX\PHPXUI;

use Lib\PHPX\PHPX;
use Lib\PHPX\PHPXUI\Button;
use Lib\MainLayout;
use Lib\PHPX\PPIcons\{ChevronLeft, ChevronRight};

class Calendar extends PHPX
{
    public function __construct(array $props = [])
    {
        parent::__construct($props);

        $script = <<<'HTML'
        <script>
            document.addEventListener('PPBodyLoaded', function() {
                const calendarContainers = document.querySelectorAll('[phpxui-calendar]');
                
                calendarContainers.forEach(calendarContainer => {
                    const previousMonthButton = calendarContainer.querySelector("[name='previous-month']");
                    const nextMonthButton = calendarContainer.querySelector("[name='next-month']");
                    const monthLabel = calendarContainer.querySelector('#react-day-picker-2');
                    const daysContainer = calendarContainer.querySelector("#calendar-days-D461A");
                    const showOutsideDays = calendarContainer.getAttribute("showOutsideDays") === 'true';
                    const onselect = calendarContainer.getAttribute("onselect-date");
                    const onselectReturn = calendarContainer.getAttribute("onselect-return") || 'false';
                    const afterRequest = calendarContainer.getAttribute("after-request") || "@close";
                    let currentDate = new Date();
                    let currentMonth = currentDate.getMonth();
                    let currentYear = currentDate.getFullYear();

                    function getDaysInMonth(month, year) {
                        return new Date(year, month + 1, 0).getDate();
                    }

                    function generateCalendarDays(month, year) {
                        const displayMonth = month + 1;
                        const firstDayOfMonth = new Date(year, month, 1).getDay();
                        const daysInMonth = getDaysInMonth(month, year);
                        const prevMonthDays = getDaysInMonth(month - 1 < 0 ? 11 : month - 1, month - 1 < 0 ? year - 1 : year);
                        const totalCells = 7 * 5;

                        daysContainer.innerHTML = '';

                        let rowHTML = `<tr class="flex w-full mt-2 gap-1">`;
                        let dayCounter = 0;

                        if (showOutsideDays) {
                            for (let i = firstDayOfMonth - 1; i >= 0; i--) {
                                const day = prevMonthDays - i;
                                const outsideDate = new Date(month - 1 < 0 ? year - 1 : year, month - 1 < 0 ? 11 : month - 1, day);
                                const isToday = outsideDate.toDateString() === currentDate.toDateString();

                                const dayClass = isToday
                                    ? 'bg-primary text-primary-foreground hover:bg-primary hover:text-primary-foreground'
                                    : 'text-muted-foreground opacity-50 hover:bg-accent hover:text-accent-foreground';

                                rowHTML += `<td class="size-9 p-0 relative">
                                    <button class="cursor-pointer inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 size-9 p-0 font-normal aria-selected:opacity-100 ${dayClass}" role="gridcell" tabindex="0" type="button" name="day">${day}</button>
                                </td>`;
                                dayCounter++;
                            }
                        } else {
                            for (let i = 0; i < firstDayOfMonth; i++) {
                                rowHTML += `<td class="size-9 p-0 text-muted-foreground opacity-50"></td>`;
                                dayCounter++;
                            }
                        }

                        for (let day = 1; day <= daysInMonth; day++) {
                            const outsideDate = new Date(year, month, day);
                            const isToday = outsideDate.toDateString() === currentDate.toDateString();
                            const onSelectDate = onselect ? `onclick="${onselect}({'day': ${day}, 'month': ${displayMonth}, 'year': ${year}, 'onselectReturn': '${onselectReturn}'})"` : '';
                            const ariaSelected = isToday ? 'true' : 'false';
                            const dayClass = isToday
                                ? `size-9 p-0 font-normal aria-selected:opacity-100 bg-primary text-primary-foreground hover:bg-primary hover:text-primary-foreground focus:bg-primary focus:text-primary-foreground bg-accent text-accent-foreground`
                                : 'hover:bg-accent hover:text-accent-foreground';

                            rowHTML += `<td class="size-9 p-0 relative">
                                <button class="cursor-pointer inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 size-9 p-0 font-normal aria-selected:opacity-100 ${dayClass}" role="gridcell" tabindex="0" type="button" name="day" aria-selected="${ariaSelected}" ${onSelectDate} pp-after-request="${afterRequest}">${day}</button>
                            </td>`;
                            dayCounter++;

                            if (dayCounter % 7 === 0) {
                                rowHTML += `</tr>`;
                                daysContainer.innerHTML += rowHTML;
                                rowHTML = `<tr class="flex w-full mt-2 gap-1">`;
                            }
                        }

                        const remainingCells = totalCells - dayCounter;
                        if (remainingCells > 0) {
                            if (showOutsideDays) {
                                for (let day = 1; day <= remainingCells; day++) {
                                    const outsideDate = new Date(year, month + 1, day);
                                    const isToday = outsideDate.toDateString() === currentDate.toDateString();

                                    const dayClass = isToday
                                        ? 'bg-primary text-primary-foreground hover:bg-primary hover:text-primary-foreground'
                                        : 'text-muted-foreground opacity-50 hover:bg-accent hover:text-accent-foreground';

                                    rowHTML += `<td class="size-9 p-0 relative">
                                        <button class="cursor-pointer inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 size-9 p-0 font-normal aria-selected:opacity-100 ${dayClass}" role="gridcell" tabindex="0" type="button" name="day">${day}</button>
                                    </td>`;
                                }
                            } else {
                                for (let i = 0; i < remainingCells; i++) {
                                    rowHTML += `<td class="size-9 p-0 text-muted-foreground opacity-50"></td>`;
                                }
                            }
                            rowHTML += `</tr>`;
                            daysContainer.innerHTML += rowHTML;
                        }

                        const dayButtons = daysContainer.querySelectorAll('button');
                        dayButtons.forEach(button => {
                            button.addEventListener('click', function() {
                                const currentlySelected = daysContainer.querySelector('[aria-selected="true"]');
                                if (currentlySelected) {
                                    currentlySelected.setAttribute('aria-selected', 'false');
                                    currentlySelected.classList.remove('bg-primary', 'text-primary-foreground', 'hover:bg-accent', 'hover:text-accent-foreground');
                                }
                                this.setAttribute('aria-selected', 'true');
                                this.classList.add('bg-primary', 'text-primary-foreground');
                                this.classList.remove('hover:bg-accent', 'hover:text-accent-foreground');
                            });
                        });
                    }

                    function updateCalendar(month, year) {
                        monthLabel.innerText = new Date(year, month).toLocaleString('default', { month: 'long', year: 'numeric' });
                        generateCalendarDays(month, year);
                    }

                    previousMonthButton.addEventListener('click', function() {
                        currentMonth -= 1;
                        if (currentMonth < 0) {
                            currentMonth = 11;
                            currentYear -= 1;
                        }
                        updateCalendar(currentMonth, currentYear);
                    });

                    nextMonthButton.addEventListener('click', function() {
                        currentMonth += 1;
                        if (currentMonth > 11) {
                            currentMonth = 0;
                            currentYear += 1;
                        }
                        updateCalendar(currentMonth, currentYear);
                    });

                    updateCalendar(currentMonth, currentYear);
                });
            });
        </script>
        HTML;

        MainLayout::addFooterScript($script);
    }

    private function getClassNames()
    {
        return array_merge([
            'months' => 'flex flex-col sm:flex-row space-y-4 sm:space-x-4 sm:space-y-0',
            'month' => 'space-y-4',
            'caption' => 'flex justify-center pt-1 relative items-center text-center',
            'caption_label' => 'text-sm font-medium',
            'nav' => 'space-x-1 flex items-center',
            'nav_button' => 'h-7 w-7 p-0 opacity-50 hover:opacity-100',
            'nav_button_previous' => 'absolute left-1',
            'nav_button_next' => 'absolute right-1',
            'table' => 'w-full border-collapse space-y-1 text-center',
            'head_row' => 'flex gap-1',
            'head_cell' => 'text-muted-foreground rounded-md w-9 font-normal text-[0.8rem]',
            'row' => 'flex w-full mt-2 gap-1',
            'cell' => 'size-9 p-0 relative',
        ]);
    }

    public function render(): string
    {
        $showOutsideDays = $this->props['showOutsideDays'] ?? 'true';
        $attributes = $this->getAttributes([
            'phpxui-calendar' => true,
            'showOutsideDays' => $showOutsideDays,
        ]);
        $class = $this->getMergeClasses('p-3 rounded-md border');
        $classNames = $this->getClassNames();

        $previousButton = <<<HTML
        <Button 
            class="{$classNames['nav_button']} {$classNames['nav_button_previous']}" 
            name="previous-month" 
            aria-label="Go to previous month" 
            variant="outline" 
            size="icon">
            <ChevronLeft class="size-4" />
        </Button>
        HTML;

        $nextButton = <<<HTML
        <Button 
            class="{$classNames['nav_button']} {$classNames['nav_button_next']}" 
            name="next-month" 
            aria-label="Go to next month" 
            variant="outline" 
            size="icon">
            <ChevronRight class="size-4" />
        </Button>
        HTML;

        return <<<HTML
        <div class="$class" $attributes>
            <div class="{$classNames['months']}">
                <div class="{$classNames['month']}">
                    <div class="{$classNames['caption']}">
                        $previousButton
                        <div class="{$classNames['caption_label']}" id="react-day-picker-2" role="presentation">October 2024</div>
                        $nextButton
                    </div>
                    <table class="{$classNames['table']}" role="grid" aria-labelledby="react-day-picker-2">
                        <thead>
                            <tr class="{$classNames['head_row']}">
                                <th class="{$classNames['head_cell']}" aria-label="Sunday">Su</th>
                                <th class="{$classNames['head_cell']}" aria-label="Monday">Mo</th>
                                <th class="{$classNames['head_cell']}" aria-label="Tuesday">Tu</th>
                                <th class="{$classNames['head_cell']}" aria-label="Wednesday">We</th>
                                <th class="{$classNames['head_cell']}" aria-label="Thursday">Th</th>
                                <th class="{$classNames['head_cell']}" aria-label="Friday">Fr</th>
                                <th class="{$classNames['head_cell']}" aria-label="Saturday">Sa</th>
                            </tr>
                        </thead>
                        <tbody id="calendar-days-D461A"></tbody>
                    </table>
                </div>
            </div>
        </div>
        HTML;
    }
}
