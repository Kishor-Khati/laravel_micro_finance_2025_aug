import Swal from 'sweetalert2';

/**
 * SweetAlert utility for CRUD operations
 */
const SweetAlertUtil = {
    /**
     * Show a success message
     * 
     * @param {string} title - The title of the alert
     * @param {string} message - The message to display
     * @param {function} callback - Optional callback function to execute after confirmation
     */
    success: (title, message, callback = null) => {
        Swal.fire({
            icon: 'success',
            title: title,
            text: message,
            confirmButtonColor: '#3085d6',
        }).then((result) => {
            if (result.isConfirmed && callback) {
                callback();
            }
        });
    },

    /**
     * Show an error message
     * 
     * @param {string} title - The title of the alert
     * @param {string} message - The message to display
     */
    error: (title, message) => {
        Swal.fire({
            icon: 'error',
            title: title,
            text: message,
            confirmButtonColor: '#3085d6',
        });
    },

    /**
     * Show a warning message
     * 
     * @param {string} title - The title of the alert
     * @param {string} message - The message to display
     */
    warning: (title, message) => {
        Swal.fire({
            icon: 'warning',
            title: title,
            text: message,
            confirmButtonColor: '#3085d6',
        });
    },

    /**
     * Show a confirmation dialog for delete operations
     * 
     * @param {string} title - The title of the confirmation
     * @param {string} message - The confirmation message
     * @param {function} confirmCallback - Function to execute on confirmation
     * @param {function} cancelCallback - Optional function to execute on cancellation
     */
    deleteConfirm: (title, message, confirmCallback, cancelCallback = null) => {
        Swal.fire({
            title: title,
            text: message,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                confirmCallback();
            } else if (cancelCallback) {
                cancelCallback();
            }
        });
    },

    /**
     * Show a confirmation dialog for general operations
     * 
     * @param {string} title - The title of the confirmation
     * @param {string} message - The confirmation message
     * @param {string} confirmText - Text for the confirm button
     * @param {function} confirmCallback - Function to execute on confirmation
     * @param {function} cancelCallback - Optional function to execute on cancellation
     */
    confirm: (title, message, confirmText, confirmCallback, cancelCallback = null) => {
        Swal.fire({
            title: title,
            text: message,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: confirmText,
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                confirmCallback();
            } else if (cancelCallback) {
                cancelCallback();
            }
        });
    },

    /**
     * Show a toast notification
     * 
     * @param {string} message - The message to display
     * @param {string} icon - The icon type (success, error, warning, info, question)
     * @param {number} timer - Duration in milliseconds
     */
    toast: (message, icon = 'success', timer = 3000) => {
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: timer,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer);
                toast.addEventListener('mouseleave', Swal.resumeTimer);
            }
        });

        Toast.fire({
            icon: icon,
            title: message
        });
    }
};

// Make it available globally
window.SweetAlert = SweetAlertUtil;

export default SweetAlertUtil;