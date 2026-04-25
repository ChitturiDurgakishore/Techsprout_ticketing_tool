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

// Add form validation feedback (loading state on submit)
// Skip logout forms to avoid breaking auth session on mobile
document.addEventListener('DOMContentLoaded', function () {
    const forms = document.querySelectorAll('form');

    forms.forEach(form => {
        // Skip logout form
        const action = form.getAttribute('action') || '';
        if (action.includes('logout')) return;

        form.addEventListener('submit', function (e) {
            const submitBtn = form.querySelector('[type="submit"]');
            if (submitBtn && !submitBtn.hasAttribute('data-no-loading')) {
                // Re-enable after 10s in case of server error
                setTimeout(() => {
                    submitBtn.disabled = false;
                    submitBtn.textContent = submitBtn.getAttribute('data-original-text') || submitBtn.textContent;
                }, 10000);
                submitBtn.setAttribute('data-original-text', submitBtn.textContent);
                submitBtn.disabled = true;
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

// ================================================
// MOBILE SIDEBAR / HAMBURGER MENU
// ================================================
document.addEventListener('DOMContentLoaded', function () {
    const hamburger = document.getElementById('hamburger-btn');
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebar-overlay');

    if (!hamburger || !sidebar || !overlay) return;

    function openSidebar() {
        sidebar.classList.add('is-open');
        overlay.classList.add('is-visible');
        hamburger.classList.add('is-active');
        document.body.style.overflow = 'hidden';
    }

    function closeSidebar() {
        sidebar.classList.remove('is-open');
        overlay.classList.remove('is-visible');
        hamburger.classList.remove('is-active');
        document.body.style.overflow = '';
    }

    hamburger.addEventListener('click', function () {
        if (sidebar.classList.contains('is-open')) {
            closeSidebar();
        } else {
            openSidebar();
        }
    });

    overlay.addEventListener('click', closeSidebar);

    // Close sidebar when a nav link is clicked (mobile)
    const navItems = sidebar.querySelectorAll('.nav-item:not(.nav-item-logout)');
    navItems.forEach(item => {
        item.addEventListener('click', function () {
            if (window.innerWidth <= 768) {
                closeSidebar();
            }
        });
    });

    // Close sidebar on window resize past breakpoint
    window.addEventListener('resize', function () {
        if (window.innerWidth > 768) {
            closeSidebar();
        }
    });

    // Close sidebar on Escape key
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && sidebar.classList.contains('is-open')) {
            closeSidebar();
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
