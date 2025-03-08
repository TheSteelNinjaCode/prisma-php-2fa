<?php

use Lib\PHPX\PHPXUI\Button;
use Lib\Request;
use Lib\PHPX\PPIcons\{
    House,
    Swords,
    Sofa,
    Car,
    CodeXml,
    Palette,
    Users,
    Warehouse,
    Building,
    UserCog,
    Settings
};

$sidebarCollapsed = Request::$localStorage->sidebarCollapsed ?? false;
$sidebarCollapsedClass = $sidebarCollapsed ? 'w-16' : 'w-52';
$sidebarIconTextClass = $sidebarCollapsed ? 'sm:hidden' : '';
$sidebarHeaderTextContent = $sidebarCollapsed ? 'DAF' : 'Administración';
$sidebarHeaderClass = $sidebarCollapsed ? 'text-center' : '';

$menuItems = [
    ["icon" => "House", "label" => "Dashboard", "href" => "/dashboard"],
    ["icon" => "Swords", "label" => "Armas", "href" => "/dashboard/armas"],
    ["icon" => "Sofa", "label" => "Mobiliario y Equipo", "href" => "/dashboard/mobiliario-y-equipo"],
    ["icon" => "Car", "label" => "Vehículo", "href" => "/dashboard/vehiculo"],
    ["icon" => "CodeXml", "label" => "Software", "href" => "/dashboard/software"],
    ["icon" => "Palette", "label" => "Obra de arte", "href" => "/dashboard/obras-de-arte"],
    ["icon" => "Users", "label" => "Empleados", "href" => "/dashboard/empleados"],
    ["icon" => "Warehouse", "label" => "Bodega", "href" => "/dashboard/bodega"],
    ["icon" => "Building", "label" => "Oficinas", "href" => "/dashboard/oficinas"],
    ["icon" => "UserCog", "label" => "Usuarios", "href" => "/dashboard/users"],
    ["icon" => "Settings", "label" => "Configuraciones", "href" => "/dashboard/configuraciones"],
];

?>

<div class="sidebar flex flex-col h-full bg-background border-r transition-all duration-300 ease-in-out <?= $sidebarCollapsedClass ?>">
    <div class="p-3 border-b">
        <h2 class="sidebar-header text-lg font-semibold transition-all duration-300 ease-in-out <?= $sidebarHeaderClass ?>"><?= $sidebarHeaderTextContent ?></h2>
    </div>
    <nav class="flex-1 overflow-y-auto">
        <ul class="p-2 space-y-2">
            <?php foreach ($menuItems as $index => $item) : ?>
                <li>
                    <Button
                        variant="ghost"
                        asChild="true"
                        class="w-full justify-start <?= Request::$pathname === $item['href'] ? 'bg-accent text-accent-foreground' : '' ?>">
                        <a href="<?= $item['href'] ?>">
                            <<?= $item['icon'] ?> class="mr-2 h-4 w-4" />
                            <span class="sidebar-icon-text transition-opacity duration-300 ease-in-out <?= $sidebarIconTextClass ?>"><?= $item['label'] ?></span>
                        </a>
                    </Button>
                </li>
            <?php endforeach; ?>
        </ul>
    </nav>
</div>