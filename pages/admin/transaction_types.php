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
    <title>Transaction Types - Tax Appointment System</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="../../css/index.css">
</head>
<body class="bg-slate-50 flex h-screen overflow-hidden">
    
    <?php include '../../includes/admin_sidebar.php'; ?>

    <main class="flex-1 overflow-y-auto p-8 lg:p-12">
        <div class="fade-in max-w-5xl mx-auto">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 gap-4">
                <div>
                    <h2 class="text-2xl font-bold text-slate-800">Transaction Types</h2>
                    <p class="text-slate-500 mt-1">Manage transaction types, their requirements, and daily quotas.</p>
                </div>
                <button onclick="openModal()" class="bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2.5 px-5 rounded-xl transition-all shadow-sm flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Create New
                </button>
            </div>
            
            <section class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-200 text-sm font-semibold text-slate-500 uppercase tracking-wider">
                                <th class="p-4">Transaction Name</th>
                                <th class="p-4">Requirements</th>
                                <th class="p-4 text-center">Daily Quota</th>
                                <th class="p-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="transactionList" class="divide-y divide-slate-200">
                            <tr>
                                <td colspan="4" class="p-4 text-sm text-slate-500 text-center">Loading transactions...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    </main>

    <!-- Modal for Create/Edit -->
    <div id="transactionModal" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-50 hidden flex items-center justify-center opacity-0 transition-opacity duration-300">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg mx-4 transform scale-95 transition-transform duration-300" id="modalContent">
            <div class="p-6 border-b border-slate-100 flex justify-between items-center">
                <h3 class="text-xl font-bold text-slate-800" id="modalTitle">Create Transaction Type</h3>
                <button onclick="closeModal()" class="text-slate-400 hover:text-slate-600 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            <form id="transactionForm" onsubmit="handleFormSubmit(event)" class="p-6 space-y-4">
                <input type="hidden" id="transactionId">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Transaction Name</label>
                    <input type="text" id="transactionName" required class="w-full p-2.5 border border-slate-200 rounded-xl text-slate-800 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none transition-all">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Requirements (Description)</label>
                    <textarea id="transactionRequirements" rows="4" class="w-full p-2.5 border border-slate-200 rounded-xl text-slate-800 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none transition-all"></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Daily Quota</label>
                    <input type="number" id="transactionQuota" required min="0" class="w-full p-2.5 border border-slate-200 rounded-xl text-slate-800 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none transition-all">
                </div>
                <div class="pt-4 flex justify-end space-x-3">
                    <button type="button" onclick="closeModal()" class="px-5 py-2.5 text-sm font-medium text-slate-600 hover:bg-slate-50 rounded-xl transition-colors">Cancel</button>
                    <button type="submit" class="px-5 py-2.5 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl shadow-sm transition-colors">Save Transaction</button>
                </div>
            </form>
        </div>
    </div>

    <script src="../../js/main.js"></script>
    <script>
        let transactions = [];

        document.addEventListener('DOMContentLoaded', () => {
            loadTransactions();
        });

        async function loadTransactions() {
            const res = await apiCall('transactions.php?action=list');
            const tbody = document.getElementById('transactionList');
            if (res && res.success) {
                transactions = res.data;
                tbody.innerHTML = '';
                if (transactions.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="4" class="p-4 text-sm text-slate-500 text-center">No transactions found.</td></tr>';
                    return;
                }
                
                transactions.forEach(t => {
                    const tr = document.createElement('tr');
                    tr.className = 'hover:bg-slate-50 transition-colors';
                    tr.innerHTML = `
                        <td class="p-4 font-medium text-slate-800">${escapeHtml(t.TransactionName)}</td>
                        <td class="p-4 text-sm text-slate-600 max-w-md truncate" title="${escapeHtml(t.Requirements || '')}">${escapeHtml(t.Requirements || '-')}</td>
                        <td class="p-4 text-center font-medium text-indigo-600">${t.DailyQuota}</td>
                        <td class="p-4 text-right">
                            <button onclick="openModal(${t.TransactionID})" class="text-indigo-600 hover:text-indigo-800 font-medium text-sm transition-colors flex items-center justify-end w-full">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                Edit
                            </button>
                        </td>
                    `;
                    tbody.appendChild(tr);
                });
            } else {
                tbody.innerHTML = '<tr><td colspan="4" class="p-4 text-sm text-red-500 text-center">Failed to load transactions.</td></tr>';
            }
        }

        function escapeHtml(str) {
            if (!str) return '';
            const div = document.createElement('div');
            div.textContent = str;
            return div.innerHTML;
        }

        function openModal(id = null) {
            const modal = document.getElementById('transactionModal');
            const modalContent = document.getElementById('modalContent');
            const title = document.getElementById('modalTitle');
            const form = document.getElementById('transactionForm');
            
            form.reset();
            document.getElementById('transactionId').value = '';

            if (id) {
                const t = transactions.find(tx => tx.TransactionID == id);
                if (t) {
                    title.textContent = 'Edit Transaction Type';
                    document.getElementById('transactionId').value = t.TransactionID;
                    document.getElementById('transactionName').value = t.TransactionName;
                    document.getElementById('transactionRequirements').value = t.Requirements || '';
                    document.getElementById('transactionQuota').value = t.DailyQuota;
                }
            } else {
                title.textContent = 'Create Transaction Type';
                document.getElementById('transactionQuota').value = '0';
            }

            modal.classList.remove('hidden');
            // Small delay to allow display:block to apply before changing opacity
            setTimeout(() => {
                modal.classList.remove('opacity-0');
                modalContent.classList.remove('scale-95');
            }, 10);
        }

        function closeModal() {
            const modal = document.getElementById('transactionModal');
            const modalContent = document.getElementById('modalContent');
            
            modal.classList.add('opacity-0');
            modalContent.classList.add('scale-95');
            
            setTimeout(() => {
                modal.classList.add('hidden');
            }, 300);
        }

        async function handleFormSubmit(e) {
            e.preventDefault();
            const id = document.getElementById('transactionId').value;
            const name = document.getElementById('transactionName').value;
            const requirements = document.getElementById('transactionRequirements').value;
            const quota = document.getElementById('transactionQuota').value;

            const action = id ? 'update' : 'create';
            const payload = { name, requirements, quota: parseInt(quota, 10) };
            if (id) payload.id = id;

            const res = await apiCall(`transactions.php?action=${action}`, 'POST', payload);
            if (res && res.success) {
                showToast(`Transaction successfully ${id ? 'updated' : 'created'}`);
                closeModal();
                loadTransactions();
            } else {
                showToast(res?.error || 'An error occurred', 'error');
            }
        }
    </script>
</body>
</html>
