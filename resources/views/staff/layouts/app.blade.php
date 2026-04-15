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

    $notificationsBaseUrl = url('/staff/notifications');
    $csrfToken = csrf_token();
@endphp

<div
    id="notificationConfig"
    data-index-url="{{ $notificationsIndexUrl }}"
    data-base-url="{{ $notificationsBaseUrl }}"
    data-csrf-token="{{ $csrfToken }}"
    hidden
></div>

<div style="display:flex; min-height:100vh;">

    <aside style="width:250px; background:#ffffff; border-right:1px solid #e2e8f0; flex-shrink:0;">
        @include('staff.partials.sidebar')
    </aside>

    <div style="flex:1; min-width:0; display:flex; flex-direction:column;">

        <header style="height:60px; background:#ffffff; border-bottom:1px solid #e2e8f0; display:flex; align-items:center; justify-content:flex-end; padding:0 20px; position:sticky; top:0; z-index:1000;">
            <div style="position:relative;">
                <button
                    id="notifBtn"
                    type="button"
                    style="
                        width:40px;
                        height:40px;
                        border:1px solid #e2e8f0;
                        background:#ffffff;
                        border-radius:10px;
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
                        "
                    >0</span>
                </button>

                <div
                    id="notifDropdown"
                    style="
                        display:none;
                        position:absolute;
                        top:48px;
                        right:0;
                        width:320px;
                        background:#ffffff;
                        border:1px solid #e2e8f0;
                        border-radius:12px;
                        box-shadow:0 10px 30px rgba(15, 23, 42, 0.10);
                        overflow:hidden;
                    "
                >
                    <div style="padding:12px 14px; border-bottom:1px solid #e2e8f0; font-size:13px; font-weight:800;">
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
    const notificationsBaseUrl = configEl?.dataset.baseUrl || '';
    const csrfToken = configEl?.dataset.csrfToken || '';

    if (!btn || !dropdown || !dropdownList || !count || !notificationsIndexUrl) {
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

    document.addEventListener('click', function () {
        dropdown.style.display = 'none';
    });

    function renderEmpty(message) {
        dropdownList.innerHTML = `
            <div style="padding:12px 14px; font-size:13px; color:#64748b;">
                ${message}
            </div>
        `;
    }

    async function loadNotifications() {
        try {
            const response = await fetch(notificationsIndexUrl, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                }
            });

            if (!response.ok) {
                throw new Error('Failed to load notifications.');
            }

            const data = await response.json();

            count.textContent = data.unread_count ?? 0;
            dropdownList.innerHTML = '';

            if (!data.notifications || data.notifications.length === 0) {
                renderEmpty('No notifications');
                return;
            }

            data.notifications.forEach((notification) => {
                const item = document.createElement('button');
                item.type = 'button';
                item.style.width = '100%';
                item.style.textAlign = 'left';
                item.style.border = 'none';
                item.style.background = notification.read_at ? '#ffffff' : '#f0fdf4';
                item.style.borderBottom = '1px solid #e2e8f0';
                item.style.padding = '12px 14px';
                item.style.cursor = 'pointer';

                item.innerHTML = `
                    <div style="font-size:13px; font-weight:700; color:#0f172a; margin-bottom:4px;">
                        ${notification.title}
                    </div>
                    <div style="font-size:12px; color:#64748b; margin-bottom:4px; line-height:1.5;">
                        ${notification.message}
                    </div>
                    <div style="font-size:11px; color:#94a3b8;">
                        ${notification.created_at}
                    </div>
                `;

                item.addEventListener('click', async function () {
                    try {
                        await fetch(`${notificationsBaseUrl}/${notification.id}/read`, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': csrfToken,
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest',
                            }
                        });
                    } catch (error) {
                        console.error(error);
                    }

                    window.location.href = notification.url || '#';
                });

                dropdownList.appendChild(item);
            });
        } catch (error) {
            console.error(error);
            renderEmpty('Unable to load notifications');
        }
    }

    loadNotifications();
    setInterval(loadNotifications, 20000);
});
</script>

</body>
</html>
