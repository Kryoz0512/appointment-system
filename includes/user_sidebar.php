<?php
$current_page = basename($_SERVER['SCRIPT_NAME']);
?>
<!-- Sidebar -->
<aside class="w-64 bg-white shadow-lg flex flex-col text-slate-700 flex-shrink-0 z-20 border-r border-slate-200">
    <div class="h-20 flex items-center px-6 border-b border-slate-100">
        <div class="w-8 h-8 bg-indigo-600 rounded-lg mr-3 flex items-center justify-center">
            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
        </div>
        <h1 class="text-lg font-bold text-slate-800 tracking-wide">Tax Portal</h1>
    </div>
    <nav class="flex-1 px-4 py-8 space-y-2">
        <a href="dashboard.php" class="w-full flex items-center px-4 py-3.5 rounded-xl transition-all text-left border <?php echo $current_page == 'dashboard.php' ? 'bg-indigo-50 text-indigo-700 font-semibold border-indigo-100' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-800 font-medium border-transparent'; ?>">
            <svg class="w-5 h-5 mr-3 <?php echo $current_page == 'dashboard.php' ? 'text-indigo-500' : 'opacity-70'; ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
            Book Appointment
        </a>
        <a href="my_appointments.php" class="w-full flex items-center px-4 py-3.5 rounded-xl transition-all text-left border <?php echo $current_page == 'my_appointments.php' ? 'bg-indigo-50 text-indigo-700 font-semibold border-indigo-100' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-800 font-medium border-transparent'; ?>">
            <svg class="w-5 h-5 mr-3 <?php echo $current_page == 'my_appointments.php' ? 'text-indigo-500' : 'opacity-70'; ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path></svg>
            My Appointments
        </a>
    </nav>
    <div class="p-4 border-t border-slate-100 mb-4">
        <button id="logoutBtn" class="w-full flex items-center justify-center px-4 py-3 text-slate-500 hover:text-red-600 hover:bg-red-50 rounded-xl font-medium transition-all">
            <svg class="w-5 h-5 mr-2 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
            Sign Out
        </button>
    </div>
</aside>
