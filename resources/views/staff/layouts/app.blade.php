<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Staff Panel</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body style="margin:0; font-family:system-ui, sans-serif; background:#f8fafc; color:#0f172a;">

@php
    $notificationsIndexUrl = Route::has('staff.notifications.index')
        ? route('staff.notifications.index')
        : '';

    $notificationOpenUrlTemplate = Route::has('staff.notifications.open')
        ? route('staff.notifications.open', ['id' => '__ID__'])
        : '';
@endphp

<div
    id="notificationConfig"
    data-index-url="{{ $notificationsIndexUrl }}"
    data-open-url-template="{{ $notificationOpenUrlTemplate }}"
    hidden
></div>

<div style="display:flex; min-height:100vh;">
    <aside style="width:250px; background:#ffffff; border-right:1px solid #e2e8f0; flex-shrink:0;">
        @include('staff.partials.sidebar')
    </aside>

    <div style="flex:1; min-width:0; display:flex; flex-direction:column;">
        <header style="height:60px; background:#ffffff; border-bottom:1px solid #e2e8f0; display:flex; align-items:center; justify-content:flex-end; padding:0 20px; position:sticky; top:0; z-index:1000;">
            <div id="notifWrapper" style="position:relative; z-index:1300;">
                <button
                    id="notifBtn"
                    type="button"
                    style="
                        width:40px;
                        height:40px;
                        border:1px solid #dbe4ea;
                        background:#ffffff;
                        border-radius:8px;
                        cursor:pointer;
                        position:relative;
                        display:flex;
                        align-items:center;
                        justify-content:center;
                        font-size:18px;
                    "
                    aria-label="Notifications"
                >
                    🔔
                    <span
                        id="notifCount"
                        style="
                            position:absolute;
                            top:-6px;
                            right:-6px;
                            min-width:18px;
                            height:18px;
                            padding:0 5px;
                            border-radius:999px;
                            background:#dc2626;
                            color:#ffffff;
                            font-size:10px;
                            line-height:18px;
                            text-align:center;
                            font-weight:700;
                            display:none;
                        "
                    >0</span>
                </button>

                <div
                    id="notifDropdown"
                    style="
                        display:none;
                        position:absolute;
                        top:46px;
                        right:0;
                        width:340px;
                        background:#ffffff;
                        border:1px solid #dbe4ea;
                        border-radius:10px;
                        box-shadow:0 8px 20px rgba(0,0,0,0.08);
                        overflow:hidden;
                        z-index:1400;
                        pointer-events:auto;
                    "
                >
                    <div style="padding:12px 14px; border-bottom:1px solid #e2e8f0; font-size:14px; font-weight:700;">
                        Notifications
                    </div>

                    <div id="notifDropdownList" style="max-height:360px; overflow-y:auto;">
                        <div style="padding:12px 14px; font-size:13px; color:#64748b;">
                            Loading...
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <main style="flex:1; padding:24px; min-width:0;">
            @yield('content')
        </main>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const configEl = document.getElementById('notificationConfig');
    const btn = document.getElementById('notifBtn');
    const dropdown = document.getElementById('notifDropdown');
    const dropdownList = document.getElementById('notifDropdownList');
    const count = document.getElementById('notifCount');

    const notificationsIndexUrl = configEl?.dataset.indexUrl || '';
    const notificationOpenUrlTemplate = configEl?.dataset.openUrlTemplate || '';

    if (!btn || !dropdown || !dropdownList || !count || !notificationsIndexUrl || !notificationOpenUrlTemplate) {
        console.error('Notification config incomplete.');
        return;
    }

    btn.addEventListener('click', function (e) {
        e.preventDefault();
        e.stopPropagation();
        dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
    });

    dropdown.addEventListener('click', function (e) {
        e.stopPropagation();
    });

    document.addEventListener('click', function (e) {
        const wrapper = document.getElementById('notifWrapper');

        if (!wrapper) {
            return;
        }

        if (!wrapper.contains(e.target)) {
            dropdown.style.display = 'none';
        }
    });

    function escapeHtml(value) {
        return String(value ?? '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function formatTime(value) {
        if (!value) return '';

        const date = new Date(value);
        if (isNaN(date.getTime())) return value;

        return date.toLocaleString([], {
            month: 'short',
            day: 'numeric',
            hour: 'numeric',
            minute: '2-digit'
        });
    }

    function renderEmpty(message) {
        dropdownList.innerHTML = `
            <div style="padding:12px 14px; font-size:13px; color:#64748b;">
                ${escapeHtml(message)}
            </div>
        `;
    }

    async function loadNotifications() {
        try {
            const res = await fetch(notificationsIndexUrl, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            if (!res.ok) {
                throw new Error('Failed to load notifications.');
            }

            const data = await res.json();
            const unreadCount = Number(data.unread_count || 0);

            count.innerText = unreadCount > 99 ? '99+' : unreadCount;
            count.style.display = unreadCount > 0 ? 'inline-block' : 'none';

            dropdownList.innerHTML = '';

            if (!Array.isArray(data.notifications) || data.notifications.length === 0) {
                renderEmpty('No notifications yet.');
                return;
            }

            data.notifications.forEach(function (n) {
                const openUrl = notificationOpenUrlTemplate.replace('__ID__', encodeURIComponent(n.id));

                const item = document.createElement('a');
                item.href = openUrl;
                item.style.display = 'block';
                item.style.width = '100%';
                item.style.padding = '12px 14px';
                item.style.textAlign = 'left';
                item.style.cursor = 'pointer';
                item.style.background = n.read_at ? '#ffffff' : '#f1f5f9';
                item.style.borderBottom = '1px solid #e2e8f0';
                item.style.boxSizing = 'border-box';
                item.style.textDecoration = 'none';

                const title = escapeHtml(n.data?.title || 'Notification');
                const message = escapeHtml(n.data?.message || '');
                const type = escapeHtml(n.data?.type || 'general');
                const time = escapeHtml(formatTime(n.created_at));

                item.innerHTML = `
                    <div style="display:flex; justify-content:space-between; align-items:flex-start; gap:10px;">
                        <div style="flex:1; min-width:0;">
                            <div style="font-size:13px; font-weight:700; color:#0f172a; margin-bottom:3px;">
                                ${title}
                            </div>
                            <div style="font-size:12px; color:#475569; line-height:1.5; margin-bottom:6px;">
                                ${message}
                            </div>
                            <div style="font-size:11px; color:#94a3b8;">
                                ${type} • ${time}
                            </div>
                        </div>
                        ${!n.read_at ? `
                            <span style="
                                width:8px;
                                height:8px;
                                border-radius:999px;
                                background:#64748b;
                                margin-top:4px;
                                flex-shrink:0;
                            "></span>
                        ` : ''}
                    </div>
                `;

                dropdownList.appendChild(item);
            });
        } catch (error) {
            console.error(error);
            renderEmpty('Unable to load notifications.');
        }
    }

    loadNotifications();
    setInterval(loadNotifications, 20000);
});
</script>

</body>
</html>
