<?php

namespace Lib\PHPX\PHPXUI;

use Lib\PHPX\PHPX; // Se asume que esta clase ya está definida en otro lugar

/**
 * Componente ResizablePanelGroup:
 * Contenedor que distribuye los paneles en horizontal o vertical.
 */
class ResizablePanelGroup extends PHPX
{
    public function render(): string
    {
        $direction = $this->props['direction'] ?? 'horizontal';
        // Agrega el atributo data-panel-group-direction para que JS y Tailwind actúen según la dirección.
        $attributes = $this->getAttributes([
            'data-panel-group-direction' => $direction,
        ]);
        // Clases base: se usa flex, y cuando es vertical se aplica flex-col vía Tailwind.
        $defaultClasses = "flex h-full w-full data-[panel-group-direction=vertical]:flex-col";
        $class = $this->getMergeClasses($defaultClasses);

        return <<<HTML
<div class="$class" $attributes>
    {$this->children}
</div>
HTML;
    }
}

/**
 * Componente ResizablePanel:
 * Panel individual que utiliza la propiedad defaultSize para definir el tamaño inicial.
 */
class ResizablePanel extends PHPX
{
    public function render(): string
    {
        $defaultSize = $this->props['defaultSize'] ?? null;
        $minSize     = $this->props['minSize'] ?? null;
        $maxSize     = $this->props['maxSize'] ?? null;

        $extraDataAttrs = [];
        if ($defaultSize !== null) {
            $extraDataAttrs['data-default-size'] = $defaultSize;
            // Se define el flex-basis inicial en porcentaje
            $extraDataAttrs['style'] = "flex: 0 0 {$defaultSize}%;";
        }
        if ($minSize !== null) {
            $extraDataAttrs['data-min-size'] = $minSize;
        }
        if ($maxSize !== null) {
            $extraDataAttrs['data-max-size'] = $maxSize;
        }

        $attributes = $this->getAttributes($extraDataAttrs);
        // Se usa flex-auto para que el panel se ajuste en el contenedor
        $class = $this->getMergeClasses("flex-auto");

        return <<<HTML
<div class="$class" $attributes>
    {$this->children}
</div>
HTML;
    }
}

/**
 * Componente ResizableHandle:
 * Barra separadora que permite el redimensionado mediante drag.
 */
class ResizableHandle extends PHPX
{
    public function render(): string
    {
        $defaultClasses = "
            resizable-handle
            relative flex w-px items-center justify-center bg-border
            after:absolute after:inset-y-0 after:left-1/2 after:w-1 after:-translate-x-1/2
            focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring focus-visible:ring-offset-1
            data-[panel-group-direction=vertical]:h-px
            data-[panel-group-direction=vertical]:w-full
            data-[panel-group-direction=vertical]:after:left-0
            data-[panel-group-direction=vertical]:after:h-1
            data-[panel-group-direction=vertical]:after:w-full
            data-[panel-group-direction=vertical]:after:-translate-y-1/2
            data-[panel-group-direction=vertical]:after:translate-x-0
            [&[data-panel-group-direction=vertical]>div]:rotate-90
        ";
        $class = $this->getMergeClasses($defaultClasses);
        $attributes = $this->getAttributes();

        $withHandle = isset($this->props['withHandle']) && $this->props['withHandle'];
        $handleHtml = "";
        if ($withHandle) {
            $handleClasses = "z-10 flex h-4 w-3 items-center justify-center rounded-sm border bg-border";
            $svgIcon = <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" class="h-2.5 w-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
    <circle cx="12" cy="5" r="1"/>
    <circle cx="12" cy="12" r="1"/>
    <circle cx="12" cy="19" r="1"/>
</svg>
SVG;
            $handleHtml = <<<HTML
<div class="$handleClasses">
    $svgIcon
</div>
HTML;
        }

        return <<<HTML
<div class="$class" $attributes>
    $handleHtml
</div>
HTML;
    }
}

/**
 * SCRIPT DE REDIMENSIONADO (JS)
 * Este script activa la funcionalidad de drag-resize en los componentes.
 */
echo <<<'SCRIPT'
<script>
document.addEventListener('DOMContentLoaded', function() {
  initResizablePanels();
});

function initResizablePanels() {
  const panelGroups = document.querySelectorAll('[data-panel-group-direction]');
  
  panelGroups.forEach(group => {
    const direction = group.getAttribute('data-panel-group-direction') || 'horizontal';
    const isHorizontal = (direction === 'horizontal');

    const children = Array.from(group.children).filter(child => {
      return child.classList.contains('resizable-handle') || child.hasAttribute('data-default-size');
    });

    for (let i = 0; i < children.length; i++) {
      const child = children[i];
      if (child.classList.contains('resizable-handle')) {
        const prevPanel = children[i - 1];
        const nextPanel = children[i + 1];
        if (!prevPanel || !nextPanel) continue;
        
        child.addEventListener('mousedown', e => {
          e.preventDefault();
          const startPos = isHorizontal ? e.clientX : e.clientY;
          const prevRect = prevPanel.getBoundingClientRect();
          const nextRect = nextPanel.getBoundingClientRect();
          const parentRect = group.getBoundingClientRect();

          const totalSize = isHorizontal ? parentRect.width : parentRect.height;
          const prevSize = isHorizontal ? prevRect.width : prevRect.height;
          const nextSize = isHorizontal ? nextRect.width : nextRect.height;

          function onMouseMove(e2) {
            const currentPos = isHorizontal ? e2.clientX : e2.clientY;
            const delta = currentPos - startPos;

            const newPrevSize = prevSize + delta;
            const newNextSize = nextSize - delta;

            const newPrevPerc = (newPrevSize / totalSize) * 100;
            const newNextPerc = (newNextSize / totalSize) * 100;

            // Importante: usar plantillas de JS (backticks) o escapar el $
            prevPanel.style.flex = `0 0 ${newPrevPerc}%`;
            nextPanel.style.flex = `0 0 ${newNextPerc}%`;
          }

          function onMouseUp() {
            document.removeEventListener('mousemove', onMouseMove);
            document.removeEventListener('mouseup', onMouseUp);
          }

          document.addEventListener('mousemove', onMouseMove);
          document.addEventListener('mouseup', onMouseUp);
        });
      }
    }
  });
}
</script>
SCRIPT;
