<?php

namespace Lib\PHPX\PHPXUI;

use Lib\PHPX\PHPX;
use Lib\PHPX\PPIcons\X; // Importamos el icono X

class Toast extends PHPX
{
  public function __construct(array $props = [])
  {
    parent::__construct($props);
  }

  public function render(): string
  {
    // Usamos `text-current` para que la X herede el color correcto
    $closeButtonIcon = (new X(['class' => 'w-4 h-4 text-current']))->render();

    return <<<HTML
        <!-- Contenedor del Toast -->
        <div id="toast-container" class="fixed bottom-5 right-7 z-50 flex flex-col items-end w-auto space-y-2"></div>

        <script>
          (function() {
            function initToast() {
              window.toast = function(options) {
                options = options || {};
                var description = (typeof options.description !== 'undefined') ? options.description : "This is a toast message.";
                var type = (typeof options.type !== 'undefined') ? options.type : "default";
                var autoClose = false; // Por defecto, los Toasts normales no se cierran solos

                const toastContainer = document.getElementById('toast-container');
                if (!toastContainer) {
                  console.error("Toast container not found!");
                  return;
                }

                let existingToast = document.getElementById('active-toast');
                if (existingToast) {
                  existingToast.remove();
                }

                // Crear el Toast
                const toastElement = document.createElement('div');
                toastElement.id = 'active-toast';
                toastElement.classList.add(
                  "relative", "flex", "items-center", 
                  "p-4", "rounded-lg", "shadow-lg", "min-w-[380px]", "max-w-[460px]", "min-h-[65px]",
                  "text-sm", "transition-all", "duration-[250ms]", "ease-out",
                  "opacity-0", "translate-y-5",
                  "group"
                );

                // Detectar si está en modo oscuro o claro y aplicar clases dinámicas
                const isDarkMode = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
                if (isDarkMode) {
                  toastElement.style.backgroundColor = "#0D0D0D";  // Fondo oscuro
                  toastElement.style.color = "#FFFFFF";  // Texto en blanco
                  toastElement.style.border = "1px solid #FFFFFF"; // Borde blanco fino
                } else {
                  toastElement.style.backgroundColor = "#F9F9F9";  // Fondo claro
                  toastElement.style.color = "#000000";  // Texto en negro
                  toastElement.style.border = "1px solid #000000"; // Borde negro fino
                }

                // ✅ Nueva Funcionalidad para `type: "input"`
                if (type === "input") {
                  autoClose = true; // Se cierra solo en 3 segundos

                  const formData = {};
                  document.querySelectorAll("form input, form textarea").forEach(input => {
                    if (input.name) formData[input.name] = input.value;
                  });

                  // Validación: Si no hay valores en los inputs, no mostramos el Toast
                  if (Object.keys(formData).length === 0) {
                    console.warn("No form data to display in Toast.");
                    return;
                  }

                  // Título del Toast
                  const titleElement = document.createElement("p");
                  titleElement.textContent = "You submitted the following values:";
                  titleElement.classList.add("font-bold", "mb-2");

                  // Contenedor JSON con fondo azul oscuro
                  const jsonContainer = document.createElement("pre");
                  jsonContainer.classList.add(
                    "mt-2", "w-full", "rounded-md", "bg-slate-950", "p-4", "text-white", 
                    "overflow-x-auto", "text-sm"
                  );
                  jsonContainer.textContent = JSON.stringify(formData, null, 2);

                  // Ajustar altura según la cantidad de datos
                  let toastHeight = 80 + Object.keys(formData).length * 20;
                  toastElement.style.minHeight = toastHeight + "px";

                  // Agregar al Toast
                  toastElement.appendChild(titleElement);
                  toastElement.appendChild(jsonContainer);
                } else {
                  // Mensaje normal dentro del Toast
                  const message = document.createElement("span");
                  message.textContent = description;
                  message.classList.add("flex-grow", "overflow-hidden", "text-ellipsis", "whitespace-nowrap");
                  toastElement.appendChild(message);
                }

                // Botón de cierre (X)
                const closeButton = document.createElement("button");
                closeButton.classList.add(
                  "absolute", "top-2", "right-2",
                  "text-lg", "transition-opacity", "duration-300",
                  "hidden", "group-hover:flex", "cursor-pointer", "text-current"
                );
                closeButton.innerHTML = `$closeButtonIcon`;

                closeButton.onclick = function () {
                  toastElement.classList.remove("opacity-100", "translate-y-0");
                  toastElement.classList.add("opacity-0", "translate-x-5", "duration-[300ms]");
                  setTimeout(() => toastElement.remove(), 300);
                };

                // Mostrar la X al pasar el cursor
                toastElement.addEventListener("mouseenter", () => {
                  closeButton.classList.remove("hidden");
                  closeButton.classList.add("flex");
                });

                // Ocultar la X al quitar el cursor
                toastElement.addEventListener("mouseleave", () => {
                  closeButton.classList.remove("flex");
                  closeButton.classList.add("hidden");
                });

                toastElement.appendChild(closeButton);
                toastContainer.appendChild(toastElement);

                // Animación para mostrar el Toast
                setTimeout(() => {
                  toastElement.classList.remove("opacity-0", "translate-y-5");
                  toastElement.classList.add("opacity-100", "translate-y-0");
                }, 10);

                // ✅ Si es "input", se cierra solo en 3 segundos
                if (autoClose) {
                  setTimeout(() => {
                    toastElement.classList.add("opacity-0", "translate-y-5");
                    setTimeout(() => toastElement.remove(), 300);
                  }, 3000);
                }
              };

              console.log("Toast function loaded successfully:", typeof window.toast === "function");
            }

            // ✅ Ahora la última llave está correctamente cerrada y no genera errores
            if (document.readyState !== "loading") {
              initToast();
            } else {
              document.addEventListener("DOMContentLoaded", initToast);
            }
          })();
        </script>
HTML;
  }
}
