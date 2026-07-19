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
    <title>Daily Quotas - Tax Appointment System</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="../../css/index.css">
</head>
<body class="bg-slate-50 flex h-screen overflow-hidden">
    
    <?php include '../../includes/admin_sidebar.php'; ?>

    <main class="flex-1 overflow-y-auto p-8 lg:p-12">
        <div class="fade-in max-w-4xl mx-auto">
            <div class="mb-8">
                <h2 class="text-2xl font-bold text-slate-800">Daily Quotas</h2>
                <p class="text-slate-500 mt-1">Set the maximum number of appointments allowed per transaction type per day.</p>
            </div>
            
            <section class="bg-white rounded-2xl shadow-sm border border-slate-200 p-8">
                <div id="quotasList" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <p class="text-sm text-slate-500 col-span-full">Loading quotas...</p>
                </div>
            </section>
        </div>
    </main>

    <script src="../../js/main.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', async () => {
            loadQuotas();
        });

        async function loadQuotas() {
            const res = await apiCall('transactions.php?action=list');
            if(res && res.success) {
                const list = document.getElementById('quotasList');
                list.innerHTML = '';
                res.data.forEach(t => {
                    const div = document.createElement('div');
                    div.className = 'flex flex-col sm:flex-row sm:items-center justify-between bg-slate-50 p-4 rounded-xl border border-slate-100 hover:border-indigo-100 transition-colors';
                    div.innerHTML = `
                        <div class="font-medium text-slate-800 mb-3 sm:mb-0">${t.TransactionName}</div>
                        <div class="flex items-center space-x-3">
                            <span class="text-xs font-semibold text-slate-400 uppercase tracking-wide">Daily Limit:</span>
                            <input type="number" id="quota_${t.TransactionID}" value="${t.DailyQuota}" class="w-20 p-2 text-center border border-slate-200 rounded-lg text-sm font-semibold text-indigo-700 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none transition-all">
                            <button onclick="updateQuota(${t.TransactionID})" class="text-white bg-slate-800 hover:bg-indigo-600 text-xs font-semibold px-3 py-2 rounded-lg transition-colors shadow-sm">Save</button>
                        </div>
                    `;
                    list.appendChild(div);
                });
            }
        }

        async function updateQuota(id) {
            const quota = document.getElementById(`quota_${id}`).value;
            const res = await apiCall('transactions.php?action=update_quota', 'POST', { id, quota });
            if(res && res.success) {
                showToast('Quota successfully updated');
            } else {
                showToast('Failed to update', 'error');
            }
        }
    </script>
</body>
</html>
