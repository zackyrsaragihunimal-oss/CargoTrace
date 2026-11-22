const dashboard = document.querySelector('[data-admin-dashboard]');

if (dashboard && window.Echo) {
    const locale = dashboard.dataset.locale === 'id' ? 'id-ID' : 'en-US';
    const numberFormatter = new Intl.NumberFormat(locale);
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
                    `${dashboard.dataset.updatedPrefix} ${updatedTime.toLocaleTimeString(locale)}`;
            }
        }
    });

    channel.subscribed(() => {
        if (updateIndicator) {
            updateIndicator.textContent = dashboard.dataset.liveConnected;
        }
    });

    channel.error(() => {
        if (updateIndicator) {
            updateIndicator.textContent = dashboard.dataset.liveUnavailable;
        }
    });
}