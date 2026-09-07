// Auto-dismiss Flash & Alert Notifications after timeout (default 4s)
document.addEventListener('DOMContentLoaded', () => {
    const initAutoDismiss = () => {
        document.querySelectorAll('.js-auto-dismiss').forEach((alert) => {
            if (alert.dataset.dismissScheduled) return;
            alert.dataset.dismissScheduled = 'true';

            const timeoutMs = parseInt(alert.getAttribute('data-dismiss-after') || '4000', 10);
            setTimeout(() => {
                alert.style.transition = 'opacity 0.35s ease, transform 0.35s ease, margin 0.35s ease, padding 0.35s ease';
                alert.style.opacity = '0';
                alert.style.transform = 'scale(0.95) translateY(-5px)';
                setTimeout(() => {
                    if (alert.parentNode) {
                        alert.remove();
                    }
                }, 350);
            }, timeoutMs);
        });
    };

    initAutoDismiss();

    // Re-run for dynamically added elements if any
    const observer = new MutationObserver(initAutoDismiss);
    observer.observe(document.body, { childList: true, subtree: true });
});
