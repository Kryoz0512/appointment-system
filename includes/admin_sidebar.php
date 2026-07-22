<?php
$current_page = basename($_SERVER['SCRIPT_NAME']);

$nav_items = [
    [
        'href' => 'dashboard.php',
        'label' => 'Appointments',
        'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>',
    ],
    [
        'href' => 'transaction_types.php',
        'label' => 'Transaction Types',
        'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>',
    ],
    [
        'href' => 'holidays.php',
        'label' => 'Holidays',
        'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>',
    ],
];
?>
<!-- Sidebar -->
<aside id="adminSidebar" class="w-64 h-screen bg-[#0d1117] flex flex-col text-zinc-300 flex-shrink-0 overflow-hidden transition-all duration-300">
    <div id="sidebarHeader" class="h-20 flex items-center justify-between px-5 shrink-0 border-b border-white transition-all duration-300">
        <div id="sidebarLogo" class="flex items-center overflow-hidden">
            <div class="w-8 h-8 bg-zinc-800 rounded-lg mr-3 flex items-center justify-center shrink-0">
                <svg class="w-4 h-4 text-[#D4AF37]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
            </div>
            <h1 class="text-lg font-bold text-white tracking-wide whitespace-nowrap sidebar-label">Tax Admin</h1>
        </div>
        <button id="collapseToggle" class="flex items-center justify-center text-zinc-500 hover:text-zinc-100 hover:bg-white/10 rounded-lg p-1.5 transition-all">
            <svg id="collapseIcon" class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                <rect width="18" height="18" x="3" y="3" rx="2" ry="2"/>
                <path d="M9 3v18"/>
                <path d="m16 15-3-3 3-3"/>
            </svg>
        </button>
    </div>

    <nav class="flex-1 px-3 py-4 space-y-1.5 overflow-y-auto overflow-x-hidden">
        <?php foreach ($nav_items as $item):
            $is_active = $current_page === $item['href'];
        ?>
        <a href="<?php echo $item['href']; ?>"
   class="group relative flex items-center gap-3 px-3 py-3 rounded-xl overflow-hidden transition-all duration-300 <?php echo $is_active
        ? 'bg-gradient-to-r from-[#2563eb] to-[#3b82f6] text-white font-semibold shadow-lg shadow-blue-500/25'
        : 'text-zinc-400 hover:bg-white/5 hover:text-zinc-100 font-medium'; ?>">

    <!-- Hover shimmer -->
    <div class="absolute inset-0 flex justify-center overflow-hidden pointer-events-none">
        <div class="absolute h-full w-10 bg-white/30 -skew-x-12 -translate-x-40 group-hover:translate-x-80 transition-transform duration-1000 ease-in-out">
        </div>
    </div>

    <svg class="w-5 h-5 shrink-0 relative z-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <?php echo $item['icon']; ?>
    </svg>
    <span class="relative z-10 whitespace-nowrap sidebar-label"><?php echo $item['label']; ?></span>
</a>
        <?php endforeach; ?>
    </nav>

    <div class="px-3 pb-3 pt-2 space-y-1.5 border-t border-white shrink-0">
        <button id="logoutBtn" class="w-full flex items-center gap-3 px-3 py-3 text-zinc-400 hover:text-zinc-100 hover:bg-white/5 rounded-xl font-medium transition-all">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
            <span class="text-sm whitespace-nowrap sidebar-label">Sign Out</span>
        </button>
    </div>
</aside>

<style>
    #adminSidebar.collapsed { width: 4.75rem; }
    #adminSidebar.collapsed .sidebar-label { display: none; }
    #adminSidebar.collapsed #sidebarLogo { display: none; }
    #adminSidebar.collapsed #sidebarHeader { justify-content: center; padding-left: 0; padding-right: 0; }
    #adminSidebar.collapsed nav a,
    #adminSidebar.collapsed #logoutBtn { justify-content: center; }
</style>

<script>
    (function () {
        const sidebar = document.getElementById('adminSidebar');
        const toggle = document.getElementById('collapseToggle');
        const icon = document.getElementById('collapseIcon');

        function applyState(collapsed) {
            sidebar.classList.toggle('collapsed', collapsed);
            icon.innerHTML = collapsed
                ? '<rect width="18" height="18" x="3" y="3" rx="2" ry="2"/><path d="M9 3v18"/><path d="m14 9 3 3-3 3"/>'
                : '<rect width="18" height="18" x="3" y="3" rx="2" ry="2"/><path d="M9 3v18"/><path d="m16 15-3-3 3-3"/>';
        }

        const saved = localStorage.getItem('adminSidebarCollapsed') === '1';
        applyState(saved);

        toggle.addEventListener('click', () => {
            const collapsed = !sidebar.classList.contains('collapsed');
            applyState(collapsed);
            localStorage.setItem('adminSidebarCollapsed', collapsed ? '1' : '0');
        });
    })();
</script>