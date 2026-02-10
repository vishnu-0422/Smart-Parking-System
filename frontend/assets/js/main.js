/**
 * Smart Parking System - Main JavaScript File
 * Common utility functions and helpers
 */

// Utility Functions
const Utils = {
    /**
     * Format date to readable string
     */
    formatDate: function(date) {
        if (!date) return '-';
        const d = new Date(date);
        return d.toLocaleString('en-US', {
            year: 'numeric',
            month: 'short',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
    },

    /**
     * Format currency (Indian Rupees)
     */
    formatCurrency: function(amount) {
        return new Intl.NumberFormat('en-IN', {
            style: 'currency',
            currency: 'INR'
        }).format(amount);
    },

    /**
     * Calculate duration between two dates
     */
    calculateDuration: function(startDate, endDate) {
        const start = new Date(startDate);
        const end = new Date(endDate);
        const diff = end - start;
        
        const hours = Math.floor(diff / (1000 * 60 * 60));
        const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
        
        if (hours > 0) {
            return `${hours} hour${hours > 1 ? 's' : ''} ${minutes > 0 ? minutes + ' minute' + (minutes > 1 ? 's' : '') : ''}`;
        }
        return `${minutes} minute${minutes > 1 ? 's' : ''}`;
    },

    /**
     * Show notification message
     */
    showMessage: function(message, type = 'success') {
        const messageDiv = document.getElementById('message');
        if (messageDiv) {
            messageDiv.innerHTML = `<div class="${type}">${message}</div>`;
            setTimeout(() => {
                messageDiv.innerHTML = '';
            }, 5000);
        }
    },

    /**
     * Validate vehicle number format
     */
    validateVehicleNumber: function(vehicleNumber) {
        const pattern = /^[A-Z]{2,3}-[0-9]{4}$/;
        return pattern.test(vehicleNumber);
    },

    /**
     * Validate phone number
     */
    validatePhoneNumber: function(phone) {
        const pattern = /^\+?[1-9]\d{1,14}$/;
        return pattern.test(phone.replace(/\s/g, ''));
    },

    /**
     * Format vehicle number (auto-format as user types)
     */
    formatVehicleNumber: function(input) {
        let value = input.value.toUpperCase().replace(/[^A-Z0-9]/g, '');
        if (value.length > 3) {
            value = value.slice(0, 3) + '-' + value.slice(3, 7);
        }
        input.value = value;
    },

    /**
     * Format card number (add spaces every 4 digits)
     */
    formatCardNumber: function(input) {
        let value = input.value.replace(/\s/g, '').replace(/[^0-9]/g, '');
        if (value.length > 16) value = value.slice(0, 16);
        value = value.match(/.{1,4}/g)?.join(' ') || value;
        input.value = value;
    },

    /**
     * Format expiry date (MM/YY)
     */
    formatExpiryDate: function(input) {
        let value = input.value.replace(/\D/g, '');
        if (value.length >= 2) {
            value = value.slice(0, 2) + '/' + value.slice(2, 4);
        }
        input.value = value;
    },

    /**
     * Format CVV (3 digits only)
     */
    formatCVV: function(input) {
        input.value = input.value.replace(/\D/g, '').slice(0, 3);
    },

    /**
     * Make API request with error handling
     */
    apiRequest: async function(url, options = {}) {
        try {
            const response = await fetch(url, {
                ...options,
                headers: {
                    'Content-Type': 'application/json',
                    ...options.headers
                }
            });

            const data = await response.json();
            
            if (!response.ok) {
                throw new Error(data.message || 'Request failed');
            }

            return data;
        } catch (error) {
            console.error('API Error:', error);
            throw error;
        }
    },

    /**
     * Debounce function
     */
    debounce: function(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    },

    /**
     * Get URL parameters
     */
    getUrlParams: function() {
        const params = new URLSearchParams(window.location.search);
        const result = {};
        for (const [key, value] of params) {
            result[key] = value;
        }
        return result;
    },

    /**
     * Check if user is authenticated (admin)
     */
    checkAuth: function() {
        const token = localStorage.getItem('adminToken');
        if (!token) {
            window.location.href = '../admin/login.html';
            return false;
        }
        return true;
    },

    /**
     * Logout function
     */
    logout: function() {
        localStorage.removeItem('adminToken');
        window.location.href = '../admin/login.html';
    }
};

// Auto-format inputs on page load
document.addEventListener('DOMContentLoaded', function() {
    // Format vehicle number inputs
    const vehicleInputs = document.querySelectorAll('input[name="vehicleNumber"], input[id="vehicleNumber"]');
    vehicleInputs.forEach(input => {
        input.addEventListener('input', function() {
            Utils.formatVehicleNumber(this);
        });
    });

    // Format card number inputs
    const cardInputs = document.querySelectorAll('input[name="cardNumber"], input[id="cardNumber"]');
    cardInputs.forEach(input => {
        input.addEventListener('input', function() {
            Utils.formatCardNumber(this);
        });
    });

    // Format expiry date inputs
    const expiryInputs = document.querySelectorAll('input[name="expiryDate"], input[id="expiryDate"]');
    expiryInputs.forEach(input => {
        input.addEventListener('input', function() {
            Utils.formatExpiryDate(this);
        });
    });

    // Format CVV inputs
    const cvvInputs = document.querySelectorAll('input[name="cvv"], input[id="cvv"]');
    cvvInputs.forEach(input => {
        input.addEventListener('input', function() {
            Utils.formatCVV(this);
        });
    });
});

// Export for use in other scripts
if (typeof module !== 'undefined' && module.exports) {
    module.exports = Utils;
}




