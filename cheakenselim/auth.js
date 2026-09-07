// Authentication and User Management
let currentUser = null;
let userBalance = 0;

// Check if user is logged in on page load
async function checkAuth() {
    try {
        const response = await fetch('api/user.php');
        const data = await response.json();
        
        if (data.success && data.user) {
            currentUser = data.user;
            window.currentUser = data.user;
            userBalance = parseFloat(currentUser.balance);
            updateUserInterface();
            // Dispatch event for other scripts
            document.dispatchEvent(new CustomEvent('user-authenticated'));
            return true;
        } else {
            // Redirect to unified login page
            window.location.href = '../login.php?game=chicken';
            return false;
        }
    } catch (error) {
        console.error('Auth check failed:', error);
        window.location.href = '../login.php?game=chicken';
        return false;
    }
}

// Update UI with user data
function updateUserInterface() {
    if (currentUser) {
        // Update balance display
        const balanceElement = document.getElementById('balanceValue') || document.getElementById('balance');
        if (balanceElement) {
            balanceElement.textContent = `₹${userBalance.toFixed(2)}`;
        }
        
        // Update avatar
        const avatarElement = document.getElementById('selectedAvatar');
        if (avatarElement && currentUser.avatar) {
            avatarElement.src = currentUser.avatar;
        }
        
        // Update username in menu
        const usernameElements = document.querySelectorAll('.text-white.font-medium.whitespace-nowrap, .player-name, #userHeaderName');
        usernameElements.forEach(el => {
            if (el.id === 'userHeaderName') {
                el.textContent = `Welcome, ${currentUser.username}!`;
            } else if (el.textContent === 'Player Name' || el.classList.contains('player-name')) {
                el.textContent = currentUser.username;
            }
        });
    }
}

// Update balance after game actions
async function updateBalance(newBalance) {
    try {
        const response = await fetch('api/update_balance.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ balance: newBalance })
        });
        
        const data = await response.json();
        
        if (data.success) {
            userBalance = newBalance;
            updateUserInterface();
            return true;
        } else {
            console.error('Failed to update balance:', data.error);
            return false;
        }
    } catch (error) {
        console.error('Balance update failed:', error);
        return false;
    }
}

// Save bet result to database
async function saveBetResult(betData) {
    try {
        const response = await fetch('api/save_bet.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(betData)
        });
        
        const data = await response.json();
        return data.success;
    } catch (error) {
        console.error('Failed to save bet:', error);
        return false;
    }
}

// Add logout function
function logout() {
    fetch('api/logout.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' }
    })
    .then(r => r.json())
    .then(data => {
        window.location.href = '../login.php?game=chicken';
    })
    .catch(err => {
        console.error('Logout error:', err);
        window.location.href = '../login.php?game=chicken';
    });
}

// Initialize authentication on page load
document.addEventListener('DOMContentLoaded', async () => {
    const isAuthenticated = await checkAuth();
    if (isAuthenticated) {
        // Add logout functionality to logout link
        const logoutLinks = document.querySelectorAll('a[href="#"]');
        logoutLinks.forEach(link => {
            if (link.textContent.includes('Logout')) {
                link.addEventListener('click', (e) => {
                    e.preventDefault();
                    logout();
                });
            }
        });
    }
});

// Override the original balance update functions
const originalUpdateBalanceDisplay = window.updateBalanceDisplay;
window.updateBalanceDisplay = function() {
    if (originalUpdateBalanceDisplay) {
        originalUpdateBalanceDisplay();
    }
    updateUserInterface();
};

// Override the original setBetAmount function
const originalSetBetAmount = window.setBetAmount;
window.setBetAmount = function(val) {
    const amount = parseFloat(val) || 10;
    const input = document.querySelector('.minmax-input') || document.getElementById('betInput');
    if (input) {
        input.value = amount;
    }
    if (originalSetBetAmount && typeof originalSetBetAmount === 'function') {
        originalSetBetAmount(amount);
    }
};