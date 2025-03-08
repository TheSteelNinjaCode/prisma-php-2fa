<?php

use Lib\PHPX\PHPXUI\Button;
use Lib\PHPX\LucideIcons\PanelLeft;

?>

<Button onclick="resizeSidebar" size="icon" variant="ghost" class="hover:bg-none">
    <PanelLeft />
</Button>

<script>
    document.addEventListener('PPBodyLoaded', () => {
        const sidebar = document.querySelector('.sidebar');
        const dashboardContent = document.querySelector('.dashboard-content');
        const sidebarHeader = document.querySelector('.sidebar-header');
        const asideSidebar = document.querySelector('.aside-sidebar');
        const sidebarIconText = document.querySelectorAll('.sidebar-icon-text');

        // Retrieve initial state from the store or determine it from the DOM
        const isCollapsed = store.state.sidebarCollapsed ?? sidebar.classList.contains('w-16');

        // Ensure the DOM matches the initial state
        toggleSidebarState(isCollapsed, {
            sidebar,
            dashboardContent,
            sidebarHeader,
            asideSidebar,
            sidebarIconText,
        });

        // Update the store with the corrected state
        store.setState({
            sidebarCollapsed: isCollapsed
        });
    });

    function resizeSidebar() {
        const sidebar = document.querySelector('.sidebar');
        const dashboardContent = document.querySelector('.dashboard-content');
        const sidebarHeader = document.querySelector('.sidebar-header');
        const asideSidebar = document.querySelector('.aside-sidebar');
        const sidebarIconText = document.querySelectorAll('.sidebar-icon-text');

        // Toggle the collapsed state
        const isCollapsed = !store.state.sidebarCollapsed;

        // Update global state
        store.setState({
            sidebarCollapsed: isCollapsed
        }, true);

        // Apply sidebar state
        toggleSidebarState(isCollapsed, {
            sidebar,
            dashboardContent,
            sidebarHeader,
            asideSidebar,
            sidebarIconText,
        });
    }

    function toggleSidebarState(isCollapsed, elements) {
        const {
            sidebar,
            dashboardContent,
            sidebarHeader,
            asideSidebar,
            sidebarIconText
        } = elements;

        // Update sidebar width
        sidebar.classList.toggle('w-16', isCollapsed);
        sidebar.classList.toggle('w-52', !isCollapsed);

        // Adjust dashboard content margin
        dashboardContent.classList.toggle('md:ml-16', isCollapsed);
        dashboardContent.classList.toggle('md:ml-52', !isCollapsed);

        // Update sidebar header
        sidebarHeader.textContent = isCollapsed ? 'DAF' : 'Administración';
        sidebarHeader.classList.toggle('text-center', isCollapsed);

        // Adjust aside sidebar width
        asideSidebar.classList.toggle('w-16', isCollapsed);
        asideSidebar.classList.toggle('w-52', !isCollapsed);

        // Show or hide sidebar icon text with fade effect
        sidebarIconText.forEach((iconText) => {
            iconText.classList.toggle('sm:hidden', isCollapsed);
        });
    }
</script>