<?php
$current_page = basename($_SERVER['SCRIPT_NAME']);
?>
<!-- Sidebar -->
<aside class="w-64 bg-indigo-900 shadow-xl flex flex-col text-indigo-100 flex-shrink-0 z-20 border-r border-indigo-800/50">
    <div class="h-20 flex items-center px-6 border-b border-indigo-800/50">
        <div class="w-8 h-8 bg-indigo-500 rounded-lg mr-3 flex items-center justify-center">
            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
        </div>
        <h1 class="text-xl font-bold text-white tracking-wide">Tax Admin</h1>
    </div>
    <nav class="flex-1 px-4 py-8 space-y-2">
        <a href="dashboard.php" class="w-full flex items-center px-4 py-3.5 rounded-xl transition-all text-left border <?php echo $current_page == 'dashboard.php' ? 'bg-indigo-800/80 text-white font-semibold shadow-inner border-indigo-700/50' : 'text-indigo-200 hover:bg-indigo-800/50 hover:text-white font-medium border-transparent'; ?>">
            <svg class="w-5 h-5 mr-3 <?php echo $current_page == 'dashboard.php' ? 'text-indigo-300' : 'opacity-70'; ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
            Appointments
        </a>
        <a href="quotas.php" class="w-full flex items-center px-4 py-3.5 rounded-xl transition-all text-left border <?php echo $current_page == 'quotas.php' ? 'bg-indigo-800/80 text-white font-semibold shadow-inner border-indigo-700/50' : 'text-indigo-200 hover:bg-indigo-800/50 hover:text-white font-medium border-transparent'; ?>">
            <svg class="w-5 h-5 mr-3 <?php echo $current_page == 'quotas.php' ? 'text-indigo-300' : 'opacity-70'; ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
            Daily Quotas
        </a>
        <a href="holidays.php" class="w-full flex items-center px-4 py-3.5 rounded-xl transition-all text-left border <?php echo $current_page == 'holidays.php' ? 'bg-indigo-800/80 text-white font-semibold shadow-inner border-indigo-700/50' : 'text-indigo-200 hover:bg-indigo-800/50 hover:text-white font-medium border-transparent'; ?>">
            <svg class="w-5 h-5 mr-3 <?php echo $current_page == 'holidays.php' ? 'text-indigo-300' : 'opacity-70'; ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
            Holidays
        </a>
    </nav>
    <div class="p-4 border-t border-indigo-800/50 mb-4">
        <button id="logoutBtn" class="w-full flex items-center justify-center px-4 py-3 text-indigo-200 hover:text-white hover:bg-indigo-800/50 rounded-xl font-medium transition-all">
            <svg class="w-5 h-5 mr-2 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
            Sign Out
        </button>
    </div>
</aside>
