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
    <title>Admin Dashboard - Tax Appointment System</title>
    <!-- Tailwind CSS Browser CDN -->
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="../../css/index.css">
</head>
<body class="bg-zinc-900 flex h-screen overflow-hidden">

    <?php include '../../includes/admin_sidebar.php'; ?>

    <!-- Main Content Area -->
    <main class="flex-1 overflow-y-auto p-8 lg:p-12 bg-zinc-900">

        <!-- Appointments View -->
        <div class="fade-in max-w-6xl mx-auto">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-2xl font-bold text-zinc-50">All Appointments</h2>
                    <p class="text-zinc-400 mt-1">Manage and update user appointment statuses</p>
                </div>
                <button onclick="loadAppointments()" class="px-5 py-2.5 bg-zinc-800 text-[#D4AF37] border border-[#D4AF37]/30 hover:bg-zinc-700 hover:border-[#D4AF37]/60 font-semibold rounded-xl transition-all shadow-sm text-sm flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                    Refresh List
                </button>
            </div>

            <!-- Filters & Search -->
            <div class="bg-zinc-800 p-4 rounded-2xl shadow-sm border border-zinc-700 mb-6 flex flex-col md:flex-row md:items-center space-y-4 md:space-y-0 md:space-x-4">
                <div class="flex-1 relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="w-5 h-5 text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>
                    <input type="text" id="searchInput" placeholder="Search email or transaction..." class="w-full pl-10 p-2.5 bg-zinc-900 border border-zinc-700 text-zinc-100 rounded-xl text-sm focus:ring-2 focus:ring-[#D4AF37] focus:outline-none transition-all">
                </div>
                <div class="w-full md:w-48">
                    <select id="statusFilter" class="w-full p-2.5 bg-zinc-900 border border-zinc-700 rounded-xl text-sm focus:ring-2 focus:ring-[#D4AF37] focus:outline-none transition-all text-zinc-200">
                        <option value="">All Statuses</option>
                        <option value="Pending">Pending</option>
                        <option value="Confirmed">Confirmed</option>
                        <option value="Pending_Reschedule">Waiting on User</option>
                        <option value="Completed">Completed</option>
                        <option value="Cancelled">Cancelled</option>
                    </select>
                </div>
                <div class="w-full md:w-48">
                    <input type="date" id="dateFilter" class="w-full p-2.5 bg-zinc-900 border border-zinc-700 rounded-xl text-sm focus:ring-2 focus:ring-[#D4AF37] focus:outline-none transition-all text-zinc-200">
                </div>
                <button onclick="resetFilters()" class="w-full md:w-auto px-5 py-2.5 text-zinc-300 bg-zinc-700 hover:bg-zinc-600 font-medium rounded-xl transition-colors text-sm">
                    Clear
                </button>
            </div>

            <section class="bg-zinc-800 rounded-2xl shadow-sm border border-zinc-700 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-zinc-300">
                        <thead class="bg-zinc-900/60 border-b border-zinc-700 text-zinc-400 font-semibold tracking-wide uppercase text-xs">
                            <tr>
                                <th class="px-6 py-5">Date & Time</th>
                                <th class="px-6 py-5">User Account</th>
                                <th class="px-6 py-5">Transaction Type</th>
                                <th class="px-6 py-5">Status</th>
                                <th class="px-6 py-5 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="appointmentsTableBody" class="divide-y divide-zinc-700">
                            <tr><td colspan="5" class="px-6 py-12 text-center text-zinc-500">Loading appointments...</td></tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="px-6 py-4 border-t border-zinc-700 bg-zinc-900/40 flex flex-col sm:flex-row items-center justify-between gap-4">
                    <div class="flex items-center space-x-3 text-sm text-zinc-400">
                        <span>Show</span>
                        <select id="entriesLimit" class="p-1.5 bg-zinc-900 border border-zinc-700 rounded-lg focus:ring-2 focus:ring-[#D4AF37] focus:outline-none transition-all text-zinc-200">
                            <option value="10">10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                            <option value="75">75</option>
                            <option value="100">100</option>
                        </select>
                        <span>entries</span>
                        <span class="pl-2 border-l border-zinc-600">
                            Showing <span class="font-bold text-[#D4AF37]" id="pageInfo">Page 1 of 1</span> (<span id="totalRowsInfo">0</span> total records)
                        </span>
                    </div>
                    <div class="flex space-x-2">
                        <button id="prevPageBtn" onclick="changePage(-1)" class="px-4 py-2 border border-zinc-700 text-zinc-300 bg-zinc-800 hover:bg-zinc-700 rounded-lg text-sm font-semibold transition-colors disabled:opacity-50 disabled:cursor-not-allowed shadow-sm">Previous</button>
                        <button id="nextPageBtn" onclick="changePage(1)" class="px-4 py-2 border border-zinc-700 text-zinc-300 bg-zinc-800 hover:bg-zinc-700 rounded-lg text-sm font-semibold transition-colors disabled:opacity-50 disabled:cursor-not-allowed shadow-sm">Next</button>
                    </div>
                </div>
            </section>
        </div>

    </main>

    <!-- Modal Template for Action (Hidden by default) -->
    <div id="actionModal" class="fixed inset-0 modal-overlay z-50 hidden flex items-center justify-center p-4 fade-in">
        <div class="bg-zinc-800 rounded-2xl shadow-xl max-w-md w-full p-6 relative border border-zinc-700">
            <button onclick="closeModal()" class="absolute top-4 right-4 text-zinc-400 hover:text-zinc-200 bg-zinc-700 hover:bg-zinc-600 p-1.5 rounded-lg transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
            <h3 class="text-xl font-bold text-zinc-50 mb-6" id="modalTitle">Manage Appointment</h3>

            <form id="actionForm" class="space-y-5">
                <input type="hidden" id="modalApptId">
                <div id="modalContent"></div>
                <div class="flex justify-end space-x-3 mt-8 pt-4 border-t border-zinc-700">
                    <button type="button" onclick="closeModal()" class="px-5 py-2.5 text-zinc-300 font-medium hover:bg-zinc-700 rounded-xl transition-colors">Cancel</button>
                    <button type="submit" class="px-5 py-2.5 bg-[#D4AF37] text-zinc-900 font-semibold rounded-xl hover:bg-[#C29A2B] transition-colors shadow-sm" id="modalSubmitBtn">Save Changes</button>
                </div>
            </form>
        </div>
    </div>

    <script src="../../js/main.js"></script>
    <script>
        let currentPage = 1;
        let totalPages = 1;

        document.addEventListener('DOMContentLoaded', async () => {
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.has('page')) currentPage = parseInt(urlParams.get('page')) || 1;
            if (urlParams.has('search')) document.getElementById('searchInput').value = urlParams.get('search');
            if (urlParams.has('status')) document.getElementById('statusFilter').value = urlParams.get('status');
            if (urlParams.has('date')) document.getElementById('dateFilter').value = urlParams.get('date');
            if (urlParams.has('limit')) document.getElementById('entriesLimit').value = urlParams.get('limit');

            setupFilters();
            loadAppointments();
            document.getElementById('actionForm').addEventListener('submit', handleActionSubmit);
        });

        function setupFilters() {
            let timeout = null;
            document.getElementById('searchInput').addEventListener('input', () => {
                clearTimeout(timeout);
                timeout = setTimeout(() => { currentPage = 1; loadAppointments(); }, 500);
            });
            document.getElementById('statusFilter').addEventListener('change', () => { currentPage = 1; loadAppointments(); });
            document.getElementById('dateFilter').addEventListener('change', () => { currentPage = 1; loadAppointments(); });
            document.getElementById('entriesLimit').addEventListener('change', () => { currentPage = 1; loadAppointments(); });
        }

        function resetFilters() {
            document.getElementById('searchInput').value = '';
            document.getElementById('statusFilter').value = '';
            document.getElementById('dateFilter').value = '';
            currentPage = 1;
            loadAppointments();
        }

        function changePage(delta) {
            const newPage = currentPage + delta;
            if (newPage >= 1 && newPage <= totalPages) {
                currentPage = newPage;
                loadAppointments();
            }
        }

        async function loadAppointments() {
            const search = document.getElementById('searchInput').value;
            const status = document.getElementById('statusFilter').value;
            const date = document.getElementById('dateFilter').value;
            const limit = document.getElementById('entriesLimit').value;

            const tbody = document.getElementById('appointmentsTableBody');
            tbody.innerHTML = '<tr><td colspan="5" class="px-6 py-12 text-center text-zinc-500">Loading appointments...</td></tr>';

            const params = new URLSearchParams();
            params.append('page', currentPage);
            params.append('limit', limit);
            if(search) params.append('search', search);
            if(status) params.append('status', status);
            if(date) params.append('date', date);

            // Update Browser URL seamlessly
            window.history.replaceState(null, '', '?' + params.toString());

            const res = await apiCall('appointments.php?action=list_admin&' + params.toString());

            if(res && res.success) {
                tbody.innerHTML = '';

                // Update Pagination UI
                currentPage = parseInt(res.meta.current_page) || 1;
                totalPages = parseInt(res.meta.total_pages) || 1;
                document.getElementById('pageInfo').textContent = `Page ${currentPage} of ${totalPages}`;
                document.getElementById('totalRowsInfo').textContent = res.meta.total_rows || 0;

                document.getElementById('prevPageBtn').disabled = (currentPage <= 1);
                document.getElementById('nextPageBtn').disabled = (currentPage >= totalPages);

                if(res.data.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="5" class="px-6 py-12 text-center text-zinc-500">No appointments found matching your filters.</td></tr>';
                    return;
                }

                res.data.forEach(a => {
                    let statusColor = 'bg-green-500/10 text-green-400 border-green-500/30';
                    let statusIcon = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>';
                    let displayStatus = a.Status;

                    if(a.Status === 'Pending') {
                        statusColor = 'bg-yellow-500/10 text-yellow-400 border-yellow-500/30';
                        statusIcon = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>';
                    } else if(a.Status === 'Cancelled') {
                        statusColor = 'bg-red-500/10 text-red-400 border-red-500/30';
                        statusIcon = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>';
                    } else if(a.Status === 'Pending_Reschedule') {
                        statusColor = 'bg-orange-500/10 text-orange-400 border-orange-500/30';
                        statusIcon = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>';
                        displayStatus = 'Waiting on User';
                    } else if(a.Status === 'Completed') {
                        statusColor = 'bg-blue-500/10 text-blue-400 border-blue-500/30';
                        statusIcon = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>';
                    }

                    const tr = document.createElement('tr');
                    tr.className = "hover:bg-zinc-700/50 transition-colors group";
                    tr.innerHTML = `
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="font-bold text-zinc-100">${formatDate(a.ApptDate)}</div>
                            <div class="text-xs font-medium text-[#D4AF37] mt-1">${formatTime(a.ApptTime)}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="w-8 h-8 rounded-full bg-zinc-700 text-zinc-300 flex items-center justify-center font-bold text-xs mr-3">
                                    ${a.Email.charAt(0).toUpperCase()}
                                </div>
                                <span class="text-zinc-100 font-medium">${a.Email}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-medium bg-zinc-700 text-zinc-200 border border-zinc-600">
                                ${a.TransactionName}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold border ${statusColor}">
                                <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">${statusIcon}</svg>
                                ${displayStatus}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right">
                            ${(a.Status === 'Pending') ? `
                                <button onclick="markAsConfirmed(${a.AppointmentID})" class="inline-flex items-center px-3 py-1.5 bg-green-500/10 hover:bg-green-600 text-green-400 hover:text-white border border-green-500/30 hover:border-green-600 text-xs font-bold rounded-lg mr-2 transition-all shadow-sm">
                                    <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                    Accept
                                </button>
                                <button onclick="openModal('reschedule', ${a.AppointmentID})" class="inline-flex items-center px-3 py-1.5 bg-zinc-800 hover:bg-zinc-700 text-zinc-200 border border-zinc-700 text-xs font-semibold rounded-lg mr-2 transition-all shadow-sm">
                                    Reschedule
                                </button>
                                <button onclick="openModal('cancel', ${a.AppointmentID})" class="inline-flex items-center px-3 py-1.5 bg-zinc-800 hover:bg-red-500/10 text-red-400 border border-zinc-700 hover:border-red-500/30 text-xs font-semibold rounded-lg transition-all shadow-sm">
                                    Cancel
                                </button>
                            ` : ''}
                            ${(a.Status === 'Confirmed') ? `
                                <button onclick="markAsCompleted(${a.AppointmentID})" class="inline-flex items-center px-3 py-1.5 bg-blue-500/10 hover:bg-blue-600 text-blue-400 hover:text-white border border-blue-500/30 hover:border-blue-600 text-xs font-bold rounded-lg mr-2 transition-all shadow-sm">
                                    <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                    Done
                                </button>
                                <button onclick="openModal('reschedule', ${a.AppointmentID})" class="inline-flex items-center px-3 py-1.5 bg-zinc-800 hover:bg-zinc-700 text-zinc-200 border border-zinc-700 text-xs font-semibold rounded-lg mr-2 transition-all shadow-sm">
                                    Reschedule
                                </button>
                                <button onclick="openModal('cancel', ${a.AppointmentID})" class="inline-flex items-center px-3 py-1.5 bg-zinc-800 hover:bg-red-500/10 text-red-400 border border-zinc-700 hover:border-red-500/30 text-xs font-semibold rounded-lg transition-all shadow-sm">
                                    Cancel
                                </button>
                            ` : ''}
                            ${(a.Status === 'Pending_Reschedule') ? `
                                <button onclick="openModal('cancel', ${a.AppointmentID})" class="inline-flex items-center px-3 py-1.5 bg-zinc-800 hover:bg-red-500/10 text-red-400 border border-zinc-700 hover:border-red-500/30 text-xs font-semibold rounded-lg transition-all shadow-sm">
                                    Cancel
                                </button>
                            ` : ''}
                        </td>
                    `;
                    tbody.appendChild(tr);
                });
            } else {
                tbody.innerHTML = '<tr><td colspan="5" class="px-6 py-12 text-center text-red-400">Failed to load appointments.</td></tr>';
            }
        }

        let currentModalAction = '';

        function openModal(action, id) {
            currentModalAction = action;
            document.getElementById('modalApptId').value = id;
            document.getElementById('actionModal').classList.remove('hidden');

            const content = document.getElementById('modalContent');
            if(action === 'cancel') {
                document.getElementById('modalTitle').textContent = 'Cancel Appointment';
                content.innerHTML = `
                    <div class="bg-red-500/10 border-l-4 border-red-500 p-4 mb-6 rounded-r-lg">
                        <p class="text-sm text-red-400">You are about to cancel this appointment. This action cannot be undone.</p>
                    </div>
                    <label class="block text-sm font-semibold text-zinc-300 mb-2">Reason for Cancellation</label>
                    <textarea id="modalReason" required placeholder="e.g. User requested via phone" class="w-full p-4 bg-zinc-900 border border-zinc-700 text-zinc-100 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all outline-none resize-none" rows="3"></textarea>
                `;
                const submitBtn = document.getElementById('modalSubmitBtn');
                submitBtn.textContent = 'Confirm Cancellation';
                submitBtn.className = 'px-5 py-2.5 bg-red-600 text-white font-semibold rounded-xl hover:bg-red-700 transition-all shadow-sm hover:shadow';
            } else if (action === 'reschedule') {
                document.getElementById('modalTitle').textContent = 'Reschedule Appointment';
                content.innerHTML = `
                    <div class="bg-[#D4AF37]/10 border-l-4 border-[#D4AF37] p-4 mb-6 rounded-r-lg">
                        <p class="text-sm text-[#D4AF37]">Select a new date and time for this user's appointment.</p>
                    </div>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-semibold text-zinc-300 mb-2">New Date</label>
                            <input type="date" id="modalNewDate" required class="w-full p-3.5 bg-zinc-900 border border-zinc-700 rounded-xl focus:ring-2 focus:ring-[#D4AF37] focus:border-[#D4AF37] transition-all outline-none text-zinc-100">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-zinc-300 mb-2">New Time</label>
                            <input type="time" id="modalNewTime" required class="w-full p-3.5 bg-zinc-900 border border-zinc-700 rounded-xl focus:ring-2 focus:ring-[#D4AF37] focus:border-[#D4AF37] transition-all outline-none text-zinc-100">
                        </div>
                    </div>
                `;
                const submitBtn = document.getElementById('modalSubmitBtn');
                submitBtn.textContent = 'Save New Schedule';
                submitBtn.className = 'px-5 py-2.5 bg-[#D4AF37] text-zinc-900 font-semibold rounded-xl hover:bg-[#C29A2B] transition-all shadow-sm hover:shadow';
            }
        }

        function closeModal() {
            document.getElementById('actionModal').classList.add('hidden');
            document.getElementById('actionForm').reset();
        }

        async function handleActionSubmit(e) {
            e.preventDefault();
            const id = document.getElementById('modalApptId').value;
            let payload = { id: id };

            if(currentModalAction === 'cancel') {
                payload.status = 'Cancelled';
                payload.reason = document.getElementById('modalReason').value;
            } else if (currentModalAction === 'reschedule') {
                payload.status = 'Rescheduled';
                payload.new_date = document.getElementById('modalNewDate').value;
                payload.new_time = document.getElementById('modalNewTime').value;
            }

            const res = await apiCall('appointments.php?action=update_status', 'POST', payload);
            if(res && res.success) {
                showToast(`Appointment ${currentModalAction}led successfully`);
                closeModal();
                loadAppointments();
            } else {
                showToast(res ? res.error : 'Action failed', 'error');
            }
        }

        async function markAsConfirmed(id) {
            if(!confirm('Are you sure you want to Accept this appointment?')) return;
            const res = await apiCall('appointments.php?action=update_status', 'POST', { id: id, status: 'Confirmed' });
            if(res && res.success) {
                showToast('Appointment accepted and confirmed');
                loadAppointments();
            } else {
                showToast(res ? res.error : 'Failed to confirm', 'error');
            }
        }

        async function markAsCompleted(id) {
            if(!confirm('Are you sure you want to mark this appointment as Done?')) return;
            const res = await apiCall('appointments.php?action=update_status', 'POST', { id: id, status: 'Completed' });
            if(res && res.success) {
                showToast('Appointment marked as completed');
                loadAppointments();
            } else {
                showToast(res ? res.error : 'Failed to mark completed', 'error');
            }
        }
    </script>
</body>
</html>