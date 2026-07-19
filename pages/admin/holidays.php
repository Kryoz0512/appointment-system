<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
    header("Location: ../../login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Holidays - Tax Appointment System</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="../../css/index.css">
</head>
<body class="bg-zinc-900 flex h-screen overflow-hidden">

    <?php include '../../includes/admin_sidebar.php'; ?>

    <main class="flex-1 overflow-y-auto p-8 lg:p-12 bg-zinc-900">
        <div class="fade-in max-w-4xl mx-auto">
            <div class="mb-8">
                <h2 class="text-2xl font-bold text-zinc-50">Manage Holidays</h2>
                <p class="text-zinc-400 mt-1">Block out specific dates where no appointments can be booked.</p>
            </div>

            <section class="bg-zinc-800 rounded-2xl shadow-sm border border-zinc-700 p-8">
                <form id="blockDateForm" class="flex flex-col sm:flex-row space-y-4 sm:space-y-0 sm:space-x-4 mb-8 bg-zinc-900 p-6 rounded-xl border border-zinc-700">
                    <div class="flex-1">
                        <label class="block text-xs font-semibold text-zinc-400 uppercase tracking-wide mb-2">Date to Block</label>
                        <input type="date" id="blockDateInput" required class="w-full p-3 bg-zinc-800 border border-zinc-700 text-zinc-100 rounded-xl text-sm focus:ring-2 focus:ring-[#D4AF37] focus:outline-none transition-all">
                    </div>
                    <div class="flex-2">
                        <label class="block text-xs font-semibold text-zinc-400 uppercase tracking-wide mb-2">Reason (Optional)</label>
                        <input type="text" id="blockReasonInput" placeholder="e.g. National Holiday" class="w-full p-3 bg-zinc-800 border border-zinc-700 text-zinc-100 rounded-xl text-sm focus:ring-2 focus:ring-[#D4AF37] focus:outline-none transition-all">
                    </div>
                    <div class="flex items-end">
                        <button type="submit" class="w-full sm:w-auto bg-[#D4AF37] text-zinc-900 font-semibold px-8 py-3 rounded-xl hover:bg-[#C29A2B] transition-all shadow-sm hover:shadow hover:-translate-y-0.5">Block Date</button>
                    </div>
                </form>

                <h3 class="text-sm font-bold text-zinc-50 mb-4 border-b border-zinc-700 pb-3 uppercase tracking-wide">Currently Blocked Dates</h3>
                <div id="blockedDatesList" class="space-y-3 max-h-96 overflow-y-auto pr-2">
                    <p class="text-sm text-zinc-400">Loading blocked dates...</p>
                </div>
            </section>
        </div>
    </main>

    <script src="../../js/main.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', async () => {
            loadBlockedDates();

            document.getElementById('blockDateForm').addEventListener('submit', async (e) => {
                e.preventDefault();
                const date = document.getElementById('blockDateInput').value;
                const reason = document.getElementById('blockReasonInput').value;

                const res = await apiCall('settings.php?action=add_blocked', 'POST', { date, reason });
                if(res && res.success) {
                    showToast('Date blocked successfully');
                    document.getElementById('blockDateForm').reset();
                    loadBlockedDates();
                } else {
                    showToast(res ? res.error : 'Failed', 'error');
                }
            });
        });

        async function loadBlockedDates() {
            const res = await apiCall('settings.php?action=list_blocked');
            if(res && res.success) {
                const list = document.getElementById('blockedDatesList');
                list.innerHTML = '';
                if(res.data.length === 0) {
                    list.innerHTML = '<div class="p-6 text-center text-sm text-zinc-500 bg-zinc-900 rounded-xl border border-dashed border-zinc-700">No holidays or blocked dates configured.</div>';
                    return;
                }
                res.data.forEach(b => {
                    const div = document.createElement('div');
                    div.className = 'flex justify-between items-center p-4 bg-zinc-900 hover:bg-zinc-800 border border-zinc-700 hover:border-zinc-600 rounded-xl transition-all shadow-sm hover:shadow';
                    div.innerHTML = `
                        <div class="flex items-center">
                            <div class="w-10 h-10 rounded-full bg-red-500/10 flex items-center justify-center text-red-400 mr-4">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            </div>
                            <div>
                                <div class="font-bold text-zinc-100">${formatDate(b.BlockedDate)}</div>
                                <div class="text-sm text-zinc-400 mt-0.5">${b.Reason || 'No reason provided'}</div>
                            </div>
                        </div>
                        <button onclick="removeBlocked(${b.BlockedID})" class="text-zinc-500 hover:text-red-400 hover:bg-red-500/10 p-2 rounded-lg transition-colors tooltip relative group">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        </button>
                    `;
                    list.appendChild(div);
                });
            }
        }

        async function removeBlocked(id) {
            const res = await apiCall('settings.php?action=remove_blocked', 'POST', { id });
            if(res && res.success) {
                showToast('Date unblocked and is now available for booking');
                loadBlockedDates();
            }
        }
    </script>
</body>
</html>