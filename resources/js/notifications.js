const notificationUserId = document.body?.dataset.notificationUser;
const notificationCount = document.querySelector('[data-notification-count]');
const notificationToast = document.querySelector('[data-notification-toast]');
const toastTitle = notificationToast?.querySelector('[data-notification-toast-title]');
const toastMessage = notificationToast?.querySelector('[data-notification-toast-message]');

if (notificationUserId && window.Echo) {
    const userChannel = window.Echo.private(
        `App.Models.User.${notificationUserId}`
    );

    let toastTimer;

    userChannel.listen('.notification.created', (notification) => {
        // Update jumlah notifikasi yang belum dibaca.
        if (
            notificationCount &&
            typeof notification.unread_count === 'number'
        ) {
            const count = notification.unread_count;

            notificationCount.textContent = count > 99 ? '99+' : String(count);
            notificationCount.hidden = count <= 0;
            notificationCount.style.display = count > 0 ? 'inline-flex' : 'none';
        }

        // Tampilkan pemberitahuan singkat tanpa memasukkan HTML dari server.
        if (notificationToast && toastTitle && toastMessage) {
            toastTitle.textContent = notification.title || 'Notifikasi baru';
            toastMessage.textContent = notification.message || '';

            notificationToast.style.display = 'block';

            window.clearTimeout(toastTimer);

            toastTimer = window.setTimeout(() => {
                notificationToast.style.display = 'none';
            }, 6000);
        }
    });
}