function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    const main = document.getElementById('mainContent');
    
    sidebar.classList.toggle('hidden');
    main.classList.toggle('expanded');
}