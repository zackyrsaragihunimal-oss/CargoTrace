const dashboard = document.querySelector('[data-admin-dashboard]');

if (dashboard && window.Echo) {
    const numberFormatter = new Intl.NumberFormat('id-ID');
    const updateIndicator = dashboard.querySelector('[data-stats-updated]');

    const channel = window.Echo.private('admin.dashboard');

    channel.listen('.admin.dashboard.stats.updated', (stats) => {
        dashboard.querySelectorAll('[data-stat]').forEach((element) => {
            const value = stats[element.dataset.stat];

            if (typeof value === 'number' && Number.isFinite(value)) {
                element.textContent = numberFormatter.format(value);
            }
        });

        if (updateIndicator && stats.updatedAt) {
            const updatedTime = new Date(stats.updatedAt);

            if (!Number.isNaN(updatedTime.getTime())) {
                updateIndicator.textContent =
                    `Updated ${updatedTime.toLocaleTimeString('id-ID')}`;
            }
        }
    });

    channel.subscribed(() => {
        if (updateIndicator) {
            updateIndicator.textContent = 'Live updates connected';
        }
    });

    channel.error(() => {
        if (updateIndicator) {
            updateIndicator.textContent = 'Live updates unavailable';
        }
    });
}