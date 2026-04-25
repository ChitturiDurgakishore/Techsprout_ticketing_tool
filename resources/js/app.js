// ================================================
// MINIMAL UX UTILITIES
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
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.textContent = 'Processing...';
            }
        });
    });

    // Re-enable buttons on validation error
    const errorAlerts = document.querySelector('.alert-error');
    if (errorAlerts) {
        const submitBtns = document.querySelectorAll('[type="submit"]');
        submitBtns.forEach(btn => {
            btn.disabled = false;
            btn.textContent = btn.getAttribute('data-original-text') || 'Submit';
        });
    }
});

// Add row click navigation for tables
document.addEventListener('DOMContentLoaded', function () {
    const tableRows = document.querySelectorAll('tbody tr');

    tableRows.forEach(row => {
        const link = row.querySelector('a[data-row-link]');
        if (link) {
            row.style.cursor = 'pointer';
            row.addEventListener('click', function (e) {
                if (e.target.tagName !== 'BUTTON' && e.target.tagName !== 'A') {
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

// Handle filter changes
document.addEventListener('DOMContentLoaded', function () {
    const filterForm = document.getElementById('filter-form');
    if (filterForm) {
        const filters = filterForm.querySelectorAll('select, input[type="checkbox"]');
        filters.forEach(filter => {
            filter.addEventListener('change', function () {
                // Auto-submit form on filter change (optional)
                // filterForm.submit();
            });
        });
    }
});

// Format date inputs
function formatDate(date) {
    const options = { year: 'numeric', month: 'short', day: 'numeric' };
    return new Date(date).toLocaleDateString('en-US', options);
}

// Add tooltip functionality (simple)
document.addEventListener('DOMContentLoaded', function () {
    const tooltips = document.querySelectorAll('[data-tooltip]');
    tooltips.forEach(element => {
        element.addEventListener('mouseenter', function () {
            const tooltipText = this.getAttribute('data-tooltip');
            const tooltip = document.createElement('div');
            tooltip.className = 'tooltip';
            tooltip.textContent = tooltipText;
            tooltip.style.cssText = `
                position: absolute;
                background: #1e293b;
                color: white;
                padding: 6px 10px;
                border-radius: 4px;
                font-size: 12px;
                z-index: 1000;
                white-space: nowrap;
                top: ${this.offsetTop - 30}px;
                left: ${this.offsetLeft}px;
            `;
            document.body.appendChild(tooltip);

            this.addEventListener('mouseleave', function () {
                tooltip.remove();
            });
        });
    });
});

// Add loading state to buttons
function addLoadingState(button) {
    button.disabled = true;
    button.innerHTML = '<span>Loading...</span>';
}

function removeLoadingState(button, originalText) {
    button.disabled = false;
    button.textContent = originalText;
}
