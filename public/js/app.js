// ================================================
// MINIMAL UX UTILITIES (Custom Pure JS)
// ================================================

// Auto-dismiss alerts after 5 seconds
document.addEventListener('DOMContentLoaded', function () {
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        if (alert.classList.contains('alert-success')) {
            setTimeout(() => {
                alert.style.opacity = '0';
                alert.style.transition = 'opacity 0.3s';
                setTimeout(() => alert.remove(), 300);
            }, 5000);
        }
    });
});

// Add form validation feedback
document.addEventListener('DOMContentLoaded', function () {
    const forms = document.querySelectorAll('form');

    forms.forEach(form => {
        form.addEventListener('submit', function (e) {
            const submitBtn = form.querySelector('[type="submit"]');
            if (submitBtn && !submitBtn.hasAttribute('data-no-loading')) {
                submitBtn.disabled = true;
                submitBtn.setAttribute('data-original-text', submitBtn.textContent);
                submitBtn.textContent = 'Processing...';
            }
        });
    });
});

// Add row click navigation for tables
document.addEventListener('DOMContentLoaded', function () {
    const tableRows = document.querySelectorAll('tbody tr');

    tableRows.forEach(row => {
        const link = row.querySelector('a[data-row-link]');
        if (link) {
            row.style.cursor = 'pointer';
            row.addEventListener('click', function (e) {
                // Don't navigate if clicking on another button or link inside the row
                if (e.target.tagName !== 'BUTTON' && e.target.tagName !== 'A' && !e.target.closest('button') && !e.target.closest('a')) {
                    window.location.href = link.href;
                }
            });
        }
    });
});

// Add confirmation for destructive actions
function confirmAction(message = 'Are you sure?') {
    return confirm(message);
}

// Add loading state to buttons helper
function addLoadingState(button) {
    button.disabled = true;
    button.innerHTML = '<span>Loading...</span>';
}

function removeLoadingState(button, originalText) {
    button.disabled = false;
    button.textContent = originalText;
}
