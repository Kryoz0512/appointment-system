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
    <title>User Dashboard - Book Appointment</title>
    <!-- Tailwind CSS Browser CDN -->
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="../../css/index.css">
</head>
<body class="bg-slate-50 flex h-screen overflow-hidden">
    
    <?php include '../../includes/user_sidebar.php'; ?>

    <!-- Main Content Area -->
    <main class="flex-1 overflow-y-auto p-8 lg:p-12">
        
        <!-- BOOKING VIEW -->
        <div class="max-w-4xl mx-auto fade-in">
            <div class="mb-8">
                <h2 class="text-2xl font-bold text-slate-800">Book an Appointment</h2>
                <p class="text-slate-500 mt-1">Select a transaction type and schedule your visit.</p>
            </div>

            <!-- Step 1: Select Transaction -->
            <section id="step1" class="bg-white rounded-2xl shadow-sm border border-slate-200 p-8 mb-8 transition-all">
                <div class="flex items-center mb-6">
                    <div class="w-8 h-8 rounded-full bg-indigo-100 text-indigo-700 flex items-center justify-center font-bold mr-3">1</div>
                    <h3 class="text-lg font-bold text-slate-800">Select Transaction Type</h3>
                </div>
                <div id="transactionsList" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Populated by JS -->
                    <p class="text-slate-500 text-sm">Loading transactions...</p>
                </div>
                
                <div id="requirementsBox" class="mt-6 p-5 bg-indigo-50 border border-indigo-100 rounded-xl hidden">
                    <h4 class="text-sm font-bold text-indigo-900 mb-2 uppercase tracking-wide">Required Documents</h4>
                    <p id="reqText" class="text-sm text-indigo-800 leading-relaxed"></p>
                </div>
            </section>

            <!-- Step 2: Calendar Selection -->
            <section id="step2" class="bg-white rounded-2xl shadow-sm border border-slate-200 p-8 mb-8 hidden fade-in transition-all">
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center">
                        <div class="w-8 h-8 rounded-full bg-indigo-100 text-indigo-700 flex items-center justify-center font-bold mr-3">2</div>
                        <h3 class="text-lg font-bold text-slate-800">Select Date</h3>
                    </div>
                    <div class="flex items-center space-x-4 bg-slate-50 px-4 py-2 rounded-xl border border-slate-100">
                        <button id="prevMonth" class="p-1 hover:bg-slate-200 rounded-full transition-colors"><svg class="w-5 h-5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg></button>
                        <span id="currentMonthYear" class="font-bold text-slate-700 min-w-[100px] text-center"></span>
                        <button id="nextMonth" class="p-1 hover:bg-slate-200 rounded-full transition-colors"><svg class="w-5 h-5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg></button>
                    </div>
                </div>

                <div class="grid grid-cols-7 gap-2 text-center text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">
                    <div>Sun</div><div>Mon</div><div>Tue</div><div>Wed</div><div>Thu</div><div>Fri</div><div>Sat</div>
                </div>
                <div id="calendarGrid" class="grid grid-cols-7 gap-2">
                    <!-- Populated by JS -->
                </div>
                
                <div class="mt-8 flex items-center justify-center space-x-6 text-xs font-medium text-slate-500 bg-slate-50 py-3 rounded-lg border border-slate-100">
                    <div class="flex items-center"><div class="w-3 h-3 rounded-full bg-white border border-slate-200 mr-2 shadow-sm"></div> Available</div>
                    <div class="flex items-center"><div class="w-3 h-3 rounded-full bg-red-50 border border-red-100 mr-2 shadow-sm"></div> Full</div>
                    <div class="flex items-center"><div class="w-3 h-3 rounded-full bg-slate-100 border border-slate-200 mr-2 strip-pattern shadow-sm"></div> Blocked / Weekend</div>
                </div>
            </section>

            <!-- Step 3: Time Slot Selection -->
            <section id="step3" class="bg-white rounded-2xl shadow-sm border border-slate-200 p-8 mb-8 hidden fade-in transition-all">
                <div class="flex items-center mb-6">
                    <div class="w-8 h-8 rounded-full bg-indigo-100 text-indigo-700 flex items-center justify-center font-bold mr-3">3</div>
                    <h3 class="text-lg font-bold text-slate-800">Select Time</h3>
                </div>
                <div id="timeSlots" class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                    <button class="time-btn py-3 font-medium border border-slate-200 rounded-xl hover:border-indigo-300 hover:bg-indigo-50 text-slate-700 transition-all shadow-sm" data-time="08:00:00">08:00 AM</button>
                    <button class="time-btn py-3 font-medium border border-slate-200 rounded-xl hover:border-indigo-300 hover:bg-indigo-50 text-slate-700 transition-all shadow-sm" data-time="09:00:00">09:00 AM</button>
                    <button class="time-btn py-3 font-medium border border-slate-200 rounded-xl hover:border-indigo-300 hover:bg-indigo-50 text-slate-700 transition-all shadow-sm" data-time="10:00:00">10:00 AM</button>
                    <button class="time-btn py-3 font-medium border border-slate-200 rounded-xl hover:border-indigo-300 hover:bg-indigo-50 text-slate-700 transition-all shadow-sm" data-time="11:00:00">11:00 AM</button>
                    <button class="time-btn py-3 font-medium border border-slate-200 rounded-xl hover:border-indigo-300 hover:bg-indigo-50 text-slate-700 transition-all shadow-sm" data-time="13:00:00">01:00 PM</button>
                    <button class="time-btn py-3 font-medium border border-slate-200 rounded-xl hover:border-indigo-300 hover:bg-indigo-50 text-slate-700 transition-all shadow-sm" data-time="14:00:00">02:00 PM</button>
                    <button class="time-btn py-3 font-medium border border-slate-200 rounded-xl hover:border-indigo-300 hover:bg-indigo-50 text-slate-700 transition-all shadow-sm" data-time="15:00:00">03:00 PM</button>
                    <button class="time-btn py-3 font-medium border border-slate-200 rounded-xl hover:border-indigo-300 hover:bg-indigo-50 text-slate-700 transition-all shadow-sm" data-time="16:00:00">04:00 PM</button>
                </div>
                
                <div class="mt-10 pt-6 border-t border-slate-100 flex justify-end hidden fade-in" id="submitWrap">
                    <button id="bookBtn" class="bg-indigo-600 text-white font-bold text-lg py-4 px-10 rounded-xl hover:bg-indigo-700 transition-all shadow-lg hover:shadow-xl hover:-translate-y-1">
                        Confirm Appointment
                    </button>
                </div>
            </section>
        </div>

    </main>

    <!-- Global JS -->
    <script src="../../js/main.js"></script>
    <script>
        let state = {
            transactions: [],
            selectedTransactionId: null,
            selectedDate: null,
            selectedTime: null,
            currentMonth: new Date().getMonth() + 1,
            currentYear: new Date().getFullYear(),
            availabilityData: null
        };

        document.addEventListener('DOMContentLoaded', async () => {
            loadTransactions();
            setupCalendarListeners();
            setupTimeListeners();
            
            document.getElementById('bookBtn').addEventListener('click', submitBooking);
        });

        async function loadTransactions() {
            const res = await apiCall('transactions.php?action=list');
            if (res && res.success) {
                state.transactions = res.data;
                const list = document.getElementById('transactionsList');
                list.innerHTML = '';
                
                res.data.forEach(t => {
                    const card = document.createElement('div');
                    card.className = 'cursor-pointer p-5 border-2 border-slate-100 rounded-xl hover:border-indigo-300 hover:shadow-md transition-all bg-white group';
                    card.innerHTML = `
                        <h3 class="font-bold text-slate-800 group-hover:text-indigo-700">${t.TransactionName}</h3>
                    `;
                    card.onclick = () => selectTransaction(t.TransactionID, card);
                    list.appendChild(card);
                });
            }
        }

        function selectTransaction(id, el) {
            state.selectedTransactionId = id;
            
            document.querySelectorAll('#transactionsList > div').forEach(c => {
                c.classList.remove('ring-indigo-500', 'border-indigo-500', 'bg-indigo-50/50', 'shadow-sm');
                c.classList.add('border-slate-100');
            });
            el.classList.remove('border-slate-100');
            el.classList.add('border-indigo-500', 'bg-indigo-50/50', 'shadow-sm');

            const t = state.transactions.find(x => x.TransactionID == id);
            document.getElementById('requirementsBox').classList.remove('hidden');
            document.getElementById('reqText').textContent = t.Requirements;

            document.getElementById('step2').classList.remove('hidden');
            state.selectedDate = null;
            document.getElementById('step3').classList.add('hidden');
            loadCalendar();
        }

        function setupCalendarListeners() {
            document.getElementById('prevMonth').onclick = () => {
                state.currentMonth--;
                if(state.currentMonth < 1) { state.currentMonth = 12; state.currentYear--; }
                loadCalendar();
            };
            document.getElementById('nextMonth').onclick = () => {
                state.currentMonth++;
                if(state.currentMonth > 12) { state.currentMonth = 1; state.currentYear++; }
                loadCalendar();
            };
        }

        async function loadCalendar() {
            const res = await apiCall(`appointments.php?action=availability&transaction_id=${state.selectedTransactionId}&month=${state.currentMonth}&year=${state.currentYear}`);
            if (res && res.success) {
                state.availabilityData = res;
                renderCalendar();
            }
        }

        function renderCalendar() {
            const grid = document.getElementById('calendarGrid');
            grid.innerHTML = '';
            
            const monthName = new Date(state.currentYear, state.currentMonth - 1).toLocaleString('default', { month: 'long' });
            document.getElementById('currentMonthYear').textContent = `${monthName} ${state.currentYear}`;

            const firstDay = new Date(state.currentYear, state.currentMonth - 1, 1).getDay();
            const daysInMonth = new Date(state.currentYear, state.currentMonth, 0).getDate();

            for(let i=0; i<firstDay; i++) {
                grid.appendChild(document.createElement('div'));
            }

            const today = new Date();
            today.setHours(0,0,0,0);

            const quota = state.availabilityData.quota;
            const blocked = state.availabilityData.blocked_dates;
            const counts = state.availabilityData.booked_counts;

            for(let i=1; i<=daysInMonth; i++) {
                const cellDate = new Date(state.currentYear, state.currentMonth - 1, i);
                const dateStr = `${state.currentYear}-${String(state.currentMonth).padStart(2, '0')}-${String(i).padStart(2, '0')}`;
                
                const cell = document.createElement('div');
                cell.className = 'py-3.5 rounded-xl flex flex-col items-center justify-center transition-all duration-200 select-none shadow-sm';
                
                const dayNumber = document.createElement('span');
                dayNumber.className = 'text-sm font-semibold mb-1';
                dayNumber.textContent = i;
                cell.appendChild(dayNumber);

                const isWeekend = cellDate.getDay() === 0 || cellDate.getDay() === 6;
                const isPast = cellDate < today;
                const isBlocked = blocked.includes(dateStr);
                const bookedCount = counts[dateStr] || 0;
                const isFull = bookedCount >= quota;

                const indicator = document.createElement('div');
                indicator.className = 'w-1.5 h-1.5 rounded-full';
                cell.appendChild(indicator);

                if (isPast || isWeekend || isBlocked) {
                    cell.classList.add('bg-slate-100', 'text-slate-400', 'border', 'border-slate-200', 'cursor-not-allowed', 'strip-pattern');
                    indicator.classList.add('bg-slate-300');
                } else if (isFull) {
                    cell.classList.add('bg-red-50', 'text-red-400', 'border', 'border-red-100', 'cursor-not-allowed');
                    indicator.classList.add('bg-red-400');
                } else {
                    cell.classList.add('bg-white', 'text-slate-700', 'border', 'border-slate-200', 'cursor-pointer', 'hover:bg-indigo-50', 'hover:border-indigo-300', 'hover:-translate-y-0.5');
                    indicator.classList.add('bg-green-400');
                    if (state.selectedDate === dateStr) {
                        cell.className = 'py-3.5 rounded-xl flex flex-col items-center justify-center transition-all duration-200 select-none bg-indigo-600 text-white shadow-lg ring-4 ring-indigo-100 cursor-default transform scale-105';
                        indicator.classList.remove('bg-green-400');
                        indicator.classList.add('bg-white');
                    } else {
                        cell.onclick = () => selectDate(dateStr);
                    }
                }
                
                grid.appendChild(cell);
            }
        }

        function selectDate(dateStr) {
            state.selectedDate = dateStr;
            renderCalendar(); 
            
            document.getElementById('step3').classList.remove('hidden');
            state.selectedTime = null;
            document.getElementById('submitWrap').classList.add('hidden');
            
            document.querySelectorAll('.time-btn').forEach(btn => {
                btn.className = 'time-btn py-3 font-medium border border-slate-200 rounded-xl hover:border-indigo-300 hover:bg-indigo-50 text-slate-700 transition-all shadow-sm bg-white';
            });
        }

        function setupTimeListeners() {
            document.querySelectorAll('.time-btn').forEach(btn => {
                btn.onclick = (e) => {
                    document.querySelectorAll('.time-btn').forEach(b => {
                        b.className = 'time-btn py-3 font-medium border border-slate-200 rounded-xl hover:border-indigo-300 hover:bg-indigo-50 text-slate-700 transition-all shadow-sm bg-white';
                    });
                    const target = e.target;
                    target.className = 'time-btn py-3 font-bold border-2 border-indigo-600 rounded-xl bg-indigo-50 text-indigo-700 transition-all shadow-md';
                    
                    state.selectedTime = target.getAttribute('data-time');
                    document.getElementById('submitWrap').classList.remove('hidden');
                };
            });
        }

        async function submitBooking() {
            if(!state.selectedTransactionId || !state.selectedDate || !state.selectedTime) {
                showToast('Please complete all steps', 'error');
                return;
            }

            const btn = document.getElementById('bookBtn');
            const originalText = btn.innerHTML;
            btn.innerHTML = `<svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Booking...`;
            btn.disabled = true;

            const res = await apiCall('appointments.php?action=book', 'POST', {
                transaction_id: state.selectedTransactionId,
                date: state.selectedDate,
                time: state.selectedTime
            });

            if(res && res.success) {
                showToast('Appointment Confirmed!');
                setTimeout(() => {
                    window.location.href = 'my_appointments.php';
                }, 1500);
            } else {
                showToast(res ? res.error : 'Failed to book', 'error');
                btn.innerHTML = originalText;
                btn.disabled = false;
                loadCalendar();
            }
        }
    </script>
</body>
</html>
