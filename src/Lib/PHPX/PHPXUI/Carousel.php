<?php

namespace Lib\PHPX\PHPXUI;

use Lib\PHPX\PHPX;
use Lib\PHPX\PPIcons\{ArrowLeft, ArrowRight};
use Lib\PHPX\PHPXUI\Button;

/**
 * Carousel
 * =========
 * Este archivo define las siguientes clases:
 *  - Carousel
 *  - CarouselContent
 *  - CarouselItem
 *  - CarouselPrevious
 *  - CarouselNext
 *
 * Funcionalidad:
 *  - Usa scroll-snap para alinear slides sin ver trozos del siguiente/previo.
 *  - Desplaza un slide completo al hacer clic en los botones.
 *  - Los botones se desactivan (disabled) y quedan opacos si no hay contenido en esa dirección.
 *  - El script se inyecta dentro de un CDATA para evitar errores de XML.
 */

/**
 * Componente principal <Carousel>
 */
class Carousel extends PHPX
{
    protected $orientation;

    public function __construct(array $props = [])
    {
        parent::__construct($props);
        // Valor por defecto: "horizontal". También puede ser "vertical".
        $this->orientation = $props['orientation'] ?? 'horizontal';
    }

    public function render(): string
    {
        $class = $this->getMergeClasses('relative ' . ($this->props['class'] ?? ''));
        $attributes = $this->getAttributes();
        $dataAttributes = 'data-carousel="true" data-orientation="' . $this->orientation . '"';

        $html = <<<HTML
<div class="$class" $attributes $dataAttributes role="region" aria-roledescription="carousel">
    {$this->children}
</div>
HTML;

        // Se inyecta el script dentro de un CDATA para evitar que el parser XML se confunda.
        // Se comprueba document.readyState para inicializar inmediatamente si es posible.
        $html .= <<<HTML
<script><![CDATA[
function initCarousel() {
  function updateButtons(container, prevBtn, nextBtn, orientation) {
    if (!container || !prevBtn || !nextBtn) return;
    if (orientation === "horizontal") {
      prevBtn.disabled = container.scrollLeft <= 0;
      nextBtn.disabled = (container.scrollLeft + container.clientWidth) >= (container.scrollWidth - 1);
    } else {
      prevBtn.disabled = container.scrollTop <= 0;
      nextBtn.disabled = (container.scrollTop + container.clientHeight) >= (container.scrollHeight - 1);
    }
  }
  
  var carousels = document.querySelectorAll('[data-carousel="true"]');
  carousels.forEach(function(carouselEl) {
    var orientation = carouselEl.getAttribute("data-orientation") || "horizontal";
    var scrollContainer = carouselEl.querySelector('[data-carousel-track="true"]');
    if (!scrollContainer) return;
    var nextBtn = carouselEl.querySelector('[data-carousel-next]');
    var prevBtn = carouselEl.querySelector('[data-carousel-prev]');
    
    scrollContainer.addEventListener("scroll", function() {
      updateButtons(scrollContainer, prevBtn, nextBtn, orientation);
    });
    
    updateButtons(scrollContainer, prevBtn, nextBtn, orientation);
    
    if (orientation === "horizontal") {
      if (nextBtn) {
        nextBtn.addEventListener("click", function() {
          scrollContainer.scrollBy({ left: scrollContainer.clientWidth, behavior: "smooth" });
        });
      }
      if (prevBtn) {
        prevBtn.addEventListener("click", function() {
          scrollContainer.scrollBy({ left: -scrollContainer.clientWidth, behavior: "smooth" });
        });
      }
    } else {
      if (nextBtn) {
        nextBtn.addEventListener("click", function() {
          scrollContainer.scrollBy({ top: scrollContainer.clientHeight, behavior: "smooth" });
        });
      }
      if (prevBtn) {
        prevBtn.addEventListener("click", function() {
          scrollContainer.scrollBy({ top: -scrollContainer.clientHeight, behavior: "smooth" });
        });
      }
    }
  });
}
if(document.readyState === "complete" || document.readyState === "interactive"){
  initCarousel();
} else {
  document.addEventListener("DOMContentLoaded", initCarousel);
}
]]></script>
HTML;

        return $html;
    }
}

/**
 * <CarouselContent>
 * Contenedor con overflow-hidden y scroll-snap.
 */
class CarouselContent extends PHPX
{
    public function __construct(array $props = [])
    {
        parent::__construct($props);
    }

    public function render(): string
    {
        $orientation = $this->props['orientation'] ?? 'horizontal';
        $additionalClasses = $this->props['class'] ?? '';
        $flexDirection = ($orientation === 'horizontal') ? 'flex-row' : 'flex-col';
        $snapAxis = ($orientation === 'horizontal') ? 'snap-x' : 'snap-y';

        // Se evita el uso de gap o márgenes negativos para no ver trozos de slides.
        $class = $this->getMergeClasses("flex $flexDirection $snapAxis snap-mandatory $additionalClasses");
        $attributes = $this->getAttributes();

        // data-carousel-track="true" para que el script lo encuentre.
        return <<<HTML
<div class="overflow-hidden w-full h-full" data-carousel-track="true">
    <div class="$class" $attributes>
        {$this->children}
    </div>
</div>
HTML;
    }
}

/**
 * <CarouselItem>
 * Cada slide ocupa el 100% del contenedor y se alinea con snap-start.
 */
class CarouselItem extends PHPX
{
    public function __construct(array $props = [])
    {
        parent::__construct($props);
    }

    public function render(): string
    {
        $additionalClasses = $this->props['class'] ?? '';
        $class = $this->getMergeClasses("w-full shrink-0 snap-start $additionalClasses");
        $attributes = $this->getAttributes([
            'role' => 'group',
            'aria-roledescription' => 'slide'
        ]);

        return <<<HTML
<div class="$class" $attributes>
    {$this->children}
</div>
HTML;
    }
}

/**
 * <CarouselPrevious>
 * Botón para ir al slide anterior, posicionado fuera del contenedor a la izquierda.
 * Ahora usa "-left-4" en móviles y "sm:-left-12" en pantallas medianas o mayores.
 */
class CarouselPrevious extends PHPX
{
    public function __construct(array $props = [])
    {
        parent::__construct($props);
    }

    public function render(): string
    {
        $orientation = $this->props['orientation'] ?? 'horizontal';
        $additionalClasses = $this->props['class'] ?? '';
        $defaultClasses = $orientation === 'horizontal'
            ? '-left-4 sm:-left-12 top-1/2 -translate-y-1/2'
            : '-top-12 left-1/2 -translate-x-1/2 rotate-90';

        $class = $this->getMergeClasses("absolute h-8 w-8 rounded-full $defaultClasses $additionalClasses");

        $button = new Button([
            'variant' => 'outline',
            'size'    => 'icon',
            'class'   => $class,
        ]);
        $button->props['data-carousel-prev'] = 'true';
        if (!empty($this->props['disabled'])) {
            $button->props['disabled'] = 'disabled';
        }
        $arrowIcon = (new ArrowLeft(['class' => 'h-4 w-4']))->render();
        $button->children = $arrowIcon . '<span class="sr-only">Previous slide</span>';

        return $button->render();
    }
}

/**
 * <CarouselNext>
 * Botón para ir al siguiente slide, posicionado fuera del contenedor a la derecha.
 * Ahora usa "-right-4" en móviles y "sm:-right-12" en pantallas medianas o mayores.
 */
class CarouselNext extends PHPX
{
    public function __construct(array $props = [])
    {
        parent::__construct($props);
    }

    public function render(): string
    {
        $orientation = $this->props['orientation'] ?? 'horizontal';
        $additionalClasses = $this->props['class'] ?? '';
        $defaultClasses = $orientation === 'horizontal'
            ? '-right-4 sm:-right-12 top-1/2 -translate-y-1/2'
            : '-bottom-12 left-1/2 -translate-x-1/2 rotate-90';

        $class = $this->getMergeClasses("absolute h-8 w-8 rounded-full $defaultClasses $additionalClasses");

        $button = new Button([
            'variant' => 'outline',
            'size'    => 'icon',
            'class'   => $class,
        ]);
        $button->props['data-carousel-next'] = 'true';
        if (!empty($this->props['disabled'])) {
            $button->props['disabled'] = 'disabled';
        }
        $arrowIcon = (new ArrowRight(['class' => 'h-4 w-4']))->render();
        $button->children = $arrowIcon . '<span class="sr-only">Next slide</span>';

        return $button->render();
    }
}
