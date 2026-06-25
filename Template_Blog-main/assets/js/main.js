// NHAG Savings Management Platform - Main JavaScript File

$(document).ready(function () {
    // Initialize tooltips and popovers
    $('[data-toggle="tooltip"]').tooltip();
    $('[data-toggle="popover"]').popover();

    // Auto-hide alerts after 5 seconds
    $('.alert').not('.alert-permanent').delay(5000).slideUp(300, function () {
        $(this).remove();
    });

    // Form submission confirmation
    $('.confirm-submit').on('submit', function (e) {
        if (!confirm('Are you sure you want to proceed?')) {
            e.preventDefault();
            return false;
        }
    });
});

/**
 * Format currency values
 */
function formatCurrency(value, currency = 'N$') {
    return currency + ' ' + parseFloat(value).toLocaleString('en-US', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    });
}

/**
 * Format date
 */
function formatDate(date, format = 'MMM dd, yyyy') {
    const d = new Date(date);
    const day = String(d.getDate()).padStart(2, '0');
    const month = String(d.getMonth() + 1).padStart(2, '0');
    const year = d.getFullYear();

    return format
        .replace('dd', day)
        .replace('MM', month)
        .replace('yyyy', year);
}

/**
 * Show loading spinner
 */
function showLoading(message = 'Loading...') {
    const spinner = `
        <div class="spinner-border" role="status">
            <span class="sr-only">${message}</span>
        </div>
    `;
    return spinner;
}

/**
 * Show toast notification
 */
function showToast(message, type = 'info') {
    const toast = `
        <div class="toast" role="alert" aria-live="assertive">
            <div class="toast-header bg-${type} text-white">
                <strong class="mr-auto">Notification</strong>
                <button type="button" class="ml-2 mb-1 close" data-dismiss="toast">
                    <span>&times;</span>
                </button>
            </div>
            <div class="toast-body">
                ${message}
            </div>
        </div>
    `;

    $('body').append(toast);
    $('.toast:last').toast('show').delay(5000).queue(function () {
        $(this).remove();
    });
}

/**
 * Form validation helper
 */
function validateForm(formId) {
    const form = document.getElementById(formId);
    if (!form.checkValidity()) {
        event.preventDefault();
        event.stopPropagation();
        form.classList.add('was-validated');
        return false;
    }
    return true;
}

/**
 * File upload validation
 */
function validateFileUpload(input, maxSizeMB = 5, allowedTypes = ['pdf', 'jpg', 'jpeg', 'png', 'gif']) {
    const file = input.files[0];

    if (!file) {
        return { valid: false, message: 'No file selected' };
    }

    // Check file size
    const maxSizeBytes = maxSizeMB * 1024 * 1024;
    if (file.size > maxSizeBytes) {
        return {
            valid: false,
            message: `File size exceeds ${maxSizeMB}MB limit`
        };
    }

    // Check file type
    const fileType = file.name.split('.').pop().toLowerCase();
    if (!allowedTypes.includes(fileType)) {
        return {
            valid: false,
            message: `File type not allowed. Allowed types: ${allowedTypes.join(', ')}`
        };
    }

    return { valid: true, message: 'File validation passed' };
}

/**
 * AJAX helper function
 */
function sendAjaxRequest(url, method = 'GET', data = {}) {
    if (method.toUpperCase() === 'POST' && window.csrfTokenName && window.csrfHash) {
        data[window.csrfTokenName] = window.csrfHash;
    }

    return $.ajax({
        url: url,
        type: method,
        data: data,
        dataType: 'json',
        beforeSend: function () {
            // Show loading spinner if needed
        },
        error: function (jqXHR, textStatus, errorThrown) {
            console.error('AJAX Error:', textStatus, errorThrown);
            showToast('An error occurred. Please try again.', 'danger');
        }
    });
}

/**
 * Format table with DataTables
 */
function initializeDataTable(tableId, options = {}) {
    const defaultOptions = {
        pageLength: 25,
        responsive: true,
        lengthMenu: [10, 25, 50, 100],
        order: [[0, 'desc']]
    };

    const finalOptions = Object.assign(defaultOptions, options);
    return $(`#${tableId}`).DataTable(finalOptions);
}

/**
 * Handle form submission with AJAX
 */
$('form[data-ajax]').on('submit', function (e) {
    e.preventDefault();

    const form = $(this);
    const url = form.attr('action') || form.attr('data-url');
    const method = form.attr('method') || 'POST';
    const formData = new FormData(this);

    $.ajax({
        url: url,
        type: method,
        data: formData,
        processData: false,
        contentType: false,
        success: function (response) {
            if (response.success) {
                showToast(response.message || 'Success!', 'success');
                if (response.redirect) {
                    setTimeout(() => {
                        window.location.href = response.redirect;
                    }, 1500);
                }
                if (response.callback) {
                    eval(response.callback);
                }
            } else {
                showToast(response.message || 'Error occurred', 'danger');
            }
        },
        error: function () {
            showToast('An error occurred. Please try again.', 'danger');
        }
    });
});

/**
 * Handle confirmation dialogs
 */
$('[data-confirm]').on('click', function (e) {
    if (!confirm($(this).attr('data-confirm'))) {
        e.preventDefault();
        return false;
    }
});

/**
 * Currency input formatter
 */
$('.currency-input').on('keyup', function () {
    let value = $(this).val().replace(/,/g, '');
    if (!isNaN(value) && value !== '') {
        $(this).val(parseFloat(value).toLocaleString());
    }
});

/**
 * Mark notification as read (AJAX)
 */
function markNotificationAsRead(notificationId) {
    const data = { notification_id: notificationId };
    if (window.csrfTokenName && window.csrfHash) {
        data[window.csrfTokenName] = window.csrfHash;
    }

    $.ajax({
        url: window.baseUrl + 'dashboard/mark-notification-read',
        type: 'POST',
        data: data,
        success: function (response) {
            // Update notification count
            const badge = $('[data-notification-count]');
            if (badge.length) {
                const count = Math.max(parseInt(badge.text() || '0', 10) - 1, 0);
                if (count > 0) {
                    badge.text(count);
                } else {
                    badge.hide();
                }
            }
        }
    });
}

/**
 * Export table to CSV
 */
function exportTableToCSV(tableId, filename = 'export.csv') {
    let csv = [];

    // Get headers
    $(`#${tableId} thead tr`).each(function () {
        let row = [];
        $(this).find('th').each(function () {
            row.push($(this).text());
        });
        csv.push(row.join(','));
    });

    // Get body rows
    $(`#${tableId} tbody tr`).each(function () {
        let row = [];
        $(this).find('td').each(function () {
            row.push('"' + $(this).text() + '"');
        });
        csv.push(row.join(','));
    });

    // Create download link
    const csvContent = 'data:text/csv;charset=utf-8,' + encodeURIComponent(csv.join('\n'));
    const link = document.createElement('a');
    link.setAttribute('href', csvContent);
    link.setAttribute('download', filename);
    link.click();
}

/**
 * Print element
 */
function printElement(elementId) {
    const content = document.getElementById(elementId).innerHTML;
    const w = window.open('', '', 'height=500, width=500');
    w.document.write('<html><head><title>Print</title>');
    w.document.write('<link rel="stylesheet" href="' + window.baseUrl + 'assets/css/bootstrap.min.css">');
    w.document.write('</head><body>');
    w.document.write(content);
    w.document.write('</body></html>');
    w.print();
    w.close();
}

/**
 * Debounce function for search inputs
 */
function debounce(func, delay) {
    let timeoutId;
    return function (...args) {
        clearTimeout(timeoutId);
        timeoutId = setTimeout(() => func.apply(this, args), delay);
    };
}

/**
 * Search with debounce
 */
const searchMembers = debounce(function (term) {
    $.ajax({
        url: window.baseUrl + 'members/search',
        type: 'GET',
        data: { term: term },
        success: function (response) {
            // Handle search results
            console.log(response);
        }
    });
}, 300);
