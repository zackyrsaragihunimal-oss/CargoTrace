const notificationUserId = document.body?.dataset.notificationUser;
const notificationLocale =
    document.body?.dataset.notificationLocale === 'en' ? 'en-US' : 'id-ID';

const notificationTranslations = {
    unread_count: document.body?.dataset.notificationUnreadCount || '',
    unread: document.body?.dataset.notificationUnread || '',
    view_shipment: document.body?.dataset.notificationViewShipment || '',
    mark_read: document.body?.dataset.notificationMarkRead || '',
    new_notification: document.body?.dataset.notificationNewNotification || '',
    shipment: document.body?.dataset.notificationShipment || '',
};

function translate(key, replacements = {}) {
    let value = notificationTranslations[key] || key;

    Object.entries(replacements).forEach(([name, replacement]) => {
        value = value.replaceAll(`:${name}`, String(replacement));
    });

    return value;
}
const notificationCount = document.querySelector('[data-notification-count]');
const notificationToast = document.querySelector('[data-notification-toast]');
const toastTitle = notificationToast?.querySelector('[data-notification-toast-title]');
const toastMessage = notificationToast?.querySelector('[data-notification-toast-message]');

const customerNotificationList = document.querySelector(
    '[data-customer-notification-list]'
);

const historyNotificationList = document.querySelector(
    '[data-notification-history-list]'
);

const unreadLabel = document.querySelector(
    '[data-notification-unread-label]'
);

const markAllForm = document.querySelector(
    '[data-notification-mark-all-form]'
);

const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

const shipmentUrlTemplate =
    historyNotificationList?.dataset.shipmentUrlTemplate;

const notificationReadUrlTemplate =
    historyNotificationList?.dataset.notificationReadUrlTemplate;

let toastTimer;

function formatNotificationDate(dateValue) {
    if (!dateValue) {
        return '-';
    }

    const date = new Date(dateValue);

    if (Number.isNaN(date.getTime())) {
        return '-';
    }

    return new Intl.DateTimeFormat(notificationLocale, {
        day: '2-digit',
        month: 'short',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    }).format(date);
}

function notificationAlreadyExists(container, notificationId) {
    if (!container || !notificationId) {
        return false;
    }

    return Array.from(
        container.querySelectorAll('[data-notification-id]')
    ).some((element) => {
        return element.dataset.notificationId === String(notificationId);
    });
}

function updateUnreadCount(notification) {
    if (typeof notification.unread_count !== 'number') {
        return;
    }

    const count = Math.max(0, notification.unread_count);

    if (notificationCount) {
        notificationCount.textContent = count > 99 ? '99+' : String(count);
        notificationCount.hidden = count <= 0;
        notificationCount.style.display = count > 0 ? 'inline-flex' : 'none';
    }

    if (unreadLabel) {
        unreadLabel.textContent = translate("unread_count", { count });
    }

    if (markAllForm) {
        markAllForm.hidden = count <= 0;
        markAllForm.style.display = count > 0 ? '' : 'none';
    }
}

function addCustomerNotification(notification) {
    if (!customerNotificationList || !notification.id) {
        return;
    }

    if (notificationAlreadyExists(customerNotificationList, notification.id)) {
        return;
    }

    customerNotificationList
        .querySelector('[data-customer-notification-empty]')
        ?.remove();

    const article = document.createElement('article');
    article.className = 'ct-customer-notification';
    article.dataset.notificationId = String(notification.id);

    const heading = document.createElement('h3');
    heading.textContent = notification.title || translate('new_notification');

    const unreadPill = document.createElement('span');
    unreadPill.className = 'ct-customer-pill';
    unreadPill.textContent = translate('unread');
    heading.appendChild(document.createTextNode(' '));
    heading.appendChild(unreadPill);

    const message = document.createElement('p');
    message.textContent = notification.message || '';

    const meta = document.createElement('small');
    let metaText = formatNotificationDate(notification.created_at);

    if (notification.shipment_number) {
        metaText += ` · ${translate("shipment")} ${notification.shipment_number}`;
    }

    meta.textContent = metaText;

    article.appendChild(heading);
    article.appendChild(message);
    article.appendChild(meta);

    const firstNotification = customerNotificationList.querySelector(
        '.ct-customer-notification'
    );

    const actions = customerNotificationList.querySelector(
        '.ct-customer-actions'
    );

    if (firstNotification) {
        customerNotificationList.insertBefore(article, firstNotification);
    } else if (actions) {
        customerNotificationList.insertBefore(article, actions);
    } else {
        customerNotificationList.appendChild(article);
    }

    // Dashboard hanya menampilkan lima notifikasi terbaru.
    const customerItems = customerNotificationList.querySelectorAll(
        '.ct-customer-notification'
    );

    customerItems.forEach((item, index) => {
        if (index >= 5) {
            item.remove();
        }
    });
}

function addHistoryNotification(notification) {
    if (!historyNotificationList || !notification.id) {
        return;
    }

    if (notificationAlreadyExists(historyNotificationList, notification.id)) {
        return;
    }

    historyNotificationList
        .querySelector('.notification-empty')
        ?.remove();

    const wrapper = document.createElement('div');
    wrapper.className = 'notification-list';

    const item = document.createElement('div');
    item.className = 'notification-item unread';
    item.dataset.notificationId = String(notification.id);

    const main = document.createElement('div');
    main.className = 'notification-main';

    const title = document.createElement('div');
    title.className = 'notification-title';
    title.textContent = notification.title || translate('new_notification');

    const message = document.createElement('div');
    message.className = 'notification-message';
    message.textContent = notification.message || '';

    const meta = document.createElement('div');
    meta.className = 'notification-meta';

    const date = document.createElement('span');
    date.textContent = formatNotificationDate(notification.created_at);
    meta.appendChild(date);

    if (notification.shipment_number) {
        const shipment = document.createElement('span');
        shipment.textContent = `${translate("shipment")} ${notification.shipment_number}`;
        meta.appendChild(shipment);
    }

    const status = document.createElement('span');
    status.className = 'notification-status notification-unread';
    status.textContent = translate('unread');
    meta.appendChild(status);

    main.appendChild(title);
    main.appendChild(message);
    main.appendChild(meta);

    const actions = document.createElement('div');
    actions.className = 'notification-item-actions';

    // Tombol membuka detail shipment.
    if (notification.shipment_id && shipmentUrlTemplate) {
        const shipmentLink = document.createElement('a');

        shipmentLink.href = shipmentUrlTemplate.replace(
            '__SHIPMENT_ID__',
            encodeURIComponent(notification.shipment_id)
        );

        shipmentLink.className =
            'notification-btn notification-btn-secondary';

        shipmentLink.textContent = translate('view_shipment');
        actions.appendChild(shipmentLink);
    }

    // Tombol menandai notifikasi sudah dibaca.
    if (notificationReadUrlTemplate && csrfToken) {
        const form = document.createElement('form');
        form.method = 'POST';

        form.action = notificationReadUrlTemplate.replace(
            '__NOTIFICATION_ID__',
            encodeURIComponent(notification.id)
        );

        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '_token';
        csrfInput.value = csrfToken;

        const methodInput = document.createElement('input');
        methodInput.type = 'hidden';
        methodInput.name = '_method';
        methodInput.value = 'PATCH';

        const readButton = document.createElement('button');
        readButton.type = 'submit';
        readButton.className = 'notification-btn';
        readButton.textContent = translate('mark_read');

        form.appendChild(csrfInput);
        form.appendChild(methodInput);
        form.appendChild(readButton);

        actions.appendChild(form);
    }

    item.appendChild(main);

    if (actions.children.length > 0) {
        item.appendChild(actions);
    }
    wrapper.appendChild(item);

    const firstItem = historyNotificationList.querySelector(
        '.notification-list'
    );

    const pagination = historyNotificationList.querySelector(
        '.notification-pagination'
    );

    if (firstItem) {
        historyNotificationList.insertBefore(wrapper, firstItem);
    } else if (pagination) {
        historyNotificationList.insertBefore(wrapper, pagination);
    } else {
        historyNotificationList.appendChild(wrapper);
    }
}

function showNotificationToast(notification) {
    if (!notificationToast || !toastTitle || !toastMessage) {
        return;
    }

    toastTitle.textContent = notification.title || translate('new_notification');
    toastMessage.textContent = notification.message || '';

    notificationToast.style.display = 'block';

    window.clearTimeout(toastTimer);

    toastTimer = window.setTimeout(() => {
        notificationToast.style.display = 'none';
    }, 6000);
}

if (notificationUserId && window.Echo) {
    const userChannel = window.Echo.private(
        `App.Models.User.${notificationUserId}`
    );

    userChannel.listen('.notification.created', (notification) => {
        if (!notification || !notification.id) {
            return;
        }

        updateUnreadCount(notification);
        addCustomerNotification(notification);
        addHistoryNotification(notification);
        showNotificationToast(notification);
    });
}
