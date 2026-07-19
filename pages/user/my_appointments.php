<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Appointments - Tax Portal</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="../../css/index.css">
</head>
<body class="bg-slate-50 flex h-screen overflow-hidden">
    
    <?php include '../../includes/user_sidebar.php'; ?>

    <main class="flex-1 overflow-y-auto p-8 lg:p-12">
        <div class="max-w-5xl mx-auto fade-in">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-2xl font-bold text-slate-800">My Appointments</h2>
                    <p class="text-slate-500 mt-1">Review or manage your booked appointments.</p>
                </div>
                <button onclick="loadMyAppointments()" class="px-5 py-2.5 bg-white text-indigo-600 border border-indigo-200 hover:bg-indigo-50 font-semibold rounded-xl transition-all shadow-sm text-sm">Refresh List</button>
            </div>
            
            <!-- Filters & Search -->
            <div class="bg-white p-4 rounded-2xl shadow-sm border border-slate-200 mb-6 flex flex-col md:flex-row md:items-center space-y-4 md:space-y-0 md:space-x-4">
                <div class="flex-1 relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>
                    <input type="text" id="searchInput" placeholder="Search transaction..." class="w-full pl-10 p-2.5 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none transition-all">
                </div>
                <div class="w-full md:w-48">
                    <select id="statusFilter" class="w-full p-2.5 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none transition-all text-slate-700 bg-white">
                        <option value="">All Statuses</option>
                        <option value="Pending">Pending</option>
                        <option value="Confirmed">Confirmed</option>
                        <option value="Pending_Reschedule">Action Required</option>
                        <option value="Completed">Completed</option>
                        <option value="Cancelled">Cancelled</option>
                    </select>
                </div>
                <div class="w-full md:w-48">
                    <input type="date" id="dateFilter" class="w-full p-2.5 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none transition-all text-slate-700">
                </div>
                <button onclick="resetFilters()" class="w-full md:w-auto px-5 py-2.5 text-slate-600 bg-slate-100 hover:bg-slate-200 font-medium rounded-xl transition-colors text-sm">
                    Clear
                </button>
            </div>

            <section class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-slate-600">
                        <thead class="bg-slate-50/80 border-b border-slate-200 text-slate-500 font-semibold tracking-wide uppercase text-xs">
                            <tr>
                                <th class="px-6 py-5">Date & Time</th>
                                <th class="px-6 py-5">Transaction Type</th>
                                <th class="px-6 py-5">Status</th>
                                <th class="px-6 py-5 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="myAppointmentsTableBody" class="divide-y divide-slate-100">
                            <tr><td colspan="4" class="px-6 py-12 text-center text-slate-400">Loading your appointments...</td></tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="px-6 py-4 border-t border-slate-200 bg-slate-50 flex flex-col sm:flex-row items-center justify-between gap-4">
                    <div class="flex items-center space-x-3 text-sm text-slate-500">
                        <span>Show</span>
                        <select id="entriesLimit" class="p-1.5 border border-slate-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:outline-none transition-all text-slate-700 bg-white">
                            <option value="10">10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                            <option value="75">75</option>
                            <option value="100">100</option>
                        </select>
                        <span>entries</span>
                        <span class="pl-2 border-l border-slate-300">
                            Showing <span class="font-bold text-indigo-600" id="pageInfo">Page 1 of 1</span> (<span id="totalRowsInfo">0</span> total records)
                        </span>
                    </div>
                    <div class="flex space-x-2">
                        <button id="prevPageBtn" onclick="changePage(-1)" class="px-4 py-2 border border-slate-200 text-slate-600 bg-white hover:bg-slate-50 rounded-lg text-sm font-semibold transition-colors disabled:opacity-50 disabled:cursor-not-allowed shadow-sm">Previous</button>
                        <button id="nextPageBtn" onclick="changePage(1)" class="px-4 py-2 border border-slate-200 text-slate-600 bg-white hover:bg-slate-50 rounded-lg text-sm font-semibold transition-colors disabled:opacity-50 disabled:cursor-not-allowed shadow-sm">Next</button>
                    </div>
                </div>
            </section>
        </div>
    </main>

    <!-- Cancel Modal -->
    <div id="cancelModal" class="fixed inset-0 modal-overlay z-50 hidden flex items-center justify-center p-4 fade-in">
        <div class="bg-white rounded-2xl shadow-xl max-w-md w-full p-6 relative">
            <button onclick="closeCancelModal()" class="absolute top-4 right-4 text-slate-400 hover:text-slate-600 bg-slate-50 hover:bg-slate-100 p-1.5 rounded-lg transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
            <h3 class="text-xl font-bold text-slate-800 mb-6">Cancel Appointment</h3>
            
            <form id="cancelForm" class="space-y-5">
                <input type="hidden" id="cancelApptId">
                <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-4 rounded-r-lg">
                    <p class="text-sm text-red-700 font-medium">Are you sure you want to cancel this appointment?</p>
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Reason for Cancellation</label>
                    <textarea id="cancelReason" required placeholder="e.g. Schedule conflict" class="w-full p-4 border border-slate-200 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all outline-none resize-none" rows="3"></textarea>
                </div>
                
                <div class="flex justify-end space-x-3 mt-8 pt-4 border-t border-slate-100">
                    <button type="button" onclick="closeCancelModal()" class="px-5 py-2.5 text-slate-600 font-medium hover:bg-slate-100 rounded-xl transition-colors">Go Back</button>
                    <button type="submit" class="px-5 py-2.5 bg-red-600 text-white font-semibold rounded-xl hover:bg-red-700 transition-colors shadow-sm">Confirm Cancellation</button>
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
            loadMyAppointments();
            document.getElementById('cancelForm').addEventListener('submit', handleCancelSubmit);
        });

        function setupFilters() {
            let timeout = null;
            document.getElementById('searchInput').addEventListener('input', () => {
                clearTimeout(timeout);
                timeout = setTimeout(() => { currentPage = 1; loadMyAppointments(); }, 500);
            });
            document.getElementById('statusFilter').addEventListener('change', () => { currentPage = 1; loadMyAppointments(); });
            document.getElementById('dateFilter').addEventListener('change', () => { currentPage = 1; loadMyAppointments(); });
            document.getElementById('entriesLimit').addEventListener('change', () => { currentPage = 1; loadMyAppointments(); });
        }

        function resetFilters() {
            document.getElementById('searchInput').value = '';
            document.getElementById('statusFilter').value = '';
            document.getElementById('dateFilter').value = '';
            currentPage = 1;
            loadMyAppointments();
        }

        function changePage(delta) {
            const newPage = currentPage + delta;
            if (newPage >= 1 && newPage <= totalPages) {
                currentPage = newPage;
                loadMyAppointments();
            }
        }

        async function loadMyAppointments() {
            const search = document.getElementById('searchInput').value;
            const status = document.getElementById('statusFilter').value;
            const date = document.getElementById('dateFilter').value;
            const limit = document.getElementById('entriesLimit').value;
            
            const tbody = document.getElementById('myAppointmentsTableBody');
            tbody.innerHTML = '<tr><td colspan="4" class="px-6 py-12 text-center text-slate-400">Loading your appointments...</td></tr>';
            
            const params = new URLSearchParams();
            params.append('page', currentPage);
            params.append('limit', limit);
            if(search) params.append('search', search);
            if(status) params.append('status', status);
            if(date) params.append('date', date);

            // Update Browser URL seamlessly
            window.history.replaceState(null, '', '?' + params.toString());

            const res = await apiCall('appointments.php?action=list_user&' + params.toString());
            
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
                    tbody.innerHTML = '<tr><td colspan="4" class="px-6 py-12 text-center text-slate-400">No appointments found matching your filters.</td></tr>';
                    return;
                }
                
                res.data.forEach(a => {
                    let statusColor = 'bg-green-50 text-green-700 border-green-200';
                    let statusIcon = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>';
                    let displayStatus = a.Status;

                    if(a.Status === 'Pending') {
                        statusColor = 'bg-yellow-50 text-yellow-700 border-yellow-200';
                        statusIcon = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>';
                    } else if(a.Status === 'Cancelled') {
                        statusColor = 'bg-red-50 text-red-700 border-red-200';
                        statusIcon = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>';
                    } else if(a.Status === 'Pending_Reschedule') {
                        statusColor = 'bg-orange-50 text-orange-700 border-orange-200 ring-2 ring-orange-400';
                        statusIcon = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>';
                        displayStatus = 'Action Required';
                    } else if(a.Status === 'Completed') {
                        statusColor = 'bg-blue-50 text-blue-700 border-blue-200';
                        statusIcon = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>';
                    }

                    const tr = document.createElement('tr');
                    tr.className = "hover:bg-slate-50 transition-colors";
                    tr.innerHTML = `
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="font-bold text-slate-800">${formatDate(a.ApptDate)}</div>
                            <div class="text-xs font-medium text-indigo-600 mt-1">${formatTime(a.ApptTime)}</div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-md text-sm font-semibold bg-white text-slate-700 border border-slate-200 shadow-sm">
                                ${a.TransactionName}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-bold border ${statusColor}">
                                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">${statusIcon}</svg>
                                ${displayStatus}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right">
                            ${(a.Status === 'Pending_Reschedule') ? `
                                <button onclick="acceptReschedule(${a.AppointmentID})" class="inline-flex items-center px-4 py-2 bg-green-50 hover:bg-green-600 text-green-700 hover:text-white border border-green-200 hover:border-green-600 text-sm font-bold rounded-xl mr-2 transition-all shadow-sm">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                    Accept New Time
                                </button>
                            ` : ''}
                            ${(a.Status !== 'Cancelled' && a.Status !== 'Completed') ? `
                                <button onclick="openCancelModal(${a.AppointmentID})" class="inline-flex items-center px-4 py-2 bg-white hover:bg-red-50 text-red-600 border border-slate-200 hover:border-red-200 text-sm font-semibold rounded-xl transition-all shadow-sm">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                    Cancel
                                </button>
                            ` : ''}
                        </td>
                    `;
                    tbody.appendChild(tr);
                });
            } else {
                tbody.innerHTML = '<tr><td colspan="4" class="px-6 py-12 text-center text-red-500">Failed to load appointments.</td></tr>';
            }
        }

        function openCancelModal(id) {
            document.getElementById('cancelApptId').value = id;
            document.getElementById('cancelModal').classList.remove('hidden');
        }

        function closeCancelModal() {
            document.getElementById('cancelModal').classList.add('hidden');
            document.getElementById('cancelForm').reset();
        }

        async function handleCancelSubmit(e) {
            e.preventDefault();
            const id = document.getElementById('cancelApptId').value;
            const reason = document.getElementById('cancelReason').value;

            const res = await apiCall('appointments.php?action=update_status', 'POST', {
                id: id,
                status: 'Cancelled',
                reason: reason
            });

            if(res && res.success) {
                showToast(`Appointment cancelled successfully`);
                closeCancelModal();
                loadMyAppointments();
            } else {
                showToast(res ? res.error : 'Cancellation failed', 'error');
            }
        }

        async function acceptReschedule(id) {
            if(!confirm('Do you accept this new date and time?')) return;
            const res = await apiCall('appointments.php?action=update_status', 'POST', { id: id, status: 'Confirmed' });
            if(res && res.success) {
                showToast('Appointment rescheduled and confirmed!');
                loadMyAppointments();
            } else {
                showToast(res ? res.error : 'Failed to accept', 'error');
            }
        }
    </script>
</body>
</html>
