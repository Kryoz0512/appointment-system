const API_BASE = '/appointment_system/api/';

// Utility: Make an API call
async function apiCall(endpoint, method = 'GET', body = null) {
    try {
        const options = {
            method,
            headers: {
                'Content-Type': 'application/json'
            }
        };
        if (body) {
            options.body = JSON.stringify(body);
        }

        const response = await fetch(`${API_BASE}${endpoint}`, options);
        if (!response.ok && response.status === 401) {
            window.location.href = '/appointment_system/login.php'; // Redirect to login on unauthorized
            return null;
        }
        return await response.json();
    } catch (error) {
        console.error("API Error:", error);
        return { success: false, error: 'Network error occurred' };
    }
}

// Utility: Show a simple toast notification
function showToast(message, type = 'success') {
    const toast = document.createElement('div');
    toast.className = `fixed bottom-4 right-4 px-6 py-3 rounded-lg shadow-lg text-white fade-in z-50 ${type === 'success' ? 'bg-green-600' : 'bg-red-600'
        }`;
    toast.textContent = message;

    document.body.appendChild(toast);

    setTimeout(() => {
        toast.style.opacity = '0';
        toast.style.transition = 'opacity 0.3s ease';
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}

// Utility: Format Date (YYYY-MM-DD to readable)
function formatDate(dateStr) {
    const options = { year: 'numeric', month: 'long', day: 'numeric' };
    return new Date(dateStr).toLocaleDateString(undefined, options);
}

// Utility: Format Time (HH:MM:SS to HH:MM AM/PM)
function formatTime(timeStr) {
    const [h, m] = timeStr.split(':');
    let hours = parseInt(h);
    const ampm = hours >= 12 ? 'PM' : 'AM';
    hours = hours % 12 || 12;
    return `${hours}:${m} ${ampm}`;
}

// Handle Logout globally
document.addEventListener('DOMContentLoaded', () => {
    const logoutBtn = document.getElementById('logoutBtn');
    if (logoutBtn) {
        logoutBtn.addEventListener('click', async (e) => {
            e.preventDefault();
            const res = await apiCall('auth.php?action=logout', 'POST');
            if (res && res.success) {
                window.location.href = '/appointment_system/login.php';
            }
        });
    }
});
