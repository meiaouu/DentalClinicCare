<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'Dental Clinic Care') }} - Dentist</title>
</head>
<body style="margin: 0; background: #f8fafc; color: #0f172a; font-family: Arial, Helvetica, sans-serif;">
    @php
        $dentistNotifications = auth()->check()
            ? auth()->user()->notifications()->latest()->limit(8)->get()
            : collect();

        $dentistUnreadCount = auth()->check()
            ? auth()->user()->unreadNotifications()->count()
            : 0;
    @endphp

    <style>
        * {
            box-sizing: border-box;
        }

        html,
        body {
            height: 100%;
            margin: 0;
        }

        body {
            background: #f8fafc;
            color: #0f172a;
            font-family: Arial, Helvetica, sans-serif;
            overflow: hidden;
        }

        .dentist-shell {
            height: 100vh;
            display: flex;
            overflow: hidden;
            background: #f8fafc;
        }

        .dentist-shell-sidebar {
            width: 260px;
            height: 100vh;
            background: #ffffff;
            border-right: 1px solid #e5e7eb;
            flex-shrink: 0;
            overflow: hidden;
            display: flex;
        }

        .dentist-shell-main {
            min-width: 0;
            flex: 1;
            height: 100vh;
            overflow-y: auto;
            overflow-x: hidden;
            padding: 24px;
        }

        .dentist-topbar {
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 16px;
            padding: 14px 18px;
            margin-bottom: 18px;
            display: flex;
            justify-content: flex-end;
            align-items: center;
            gap: 12px;
        }

        .dentist-topbar-right {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-left: auto;
        }

        .dentist-notification {
            position: relative;
        }

        .dentist-notification-btn {
            border: 1px solid #d1d5db;
            background: #ffffff;
            color: #0f172a;
            border-radius: 10px;
            padding: 10px 12px;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            position: relative;
        }

        .dentist-notification-badge {
            position: absolute;
            top: -6px;
            right: -6px;
            min-width: 20px;
            height: 20px;
            padding: 0 6px;
            border-radius: 999px;
            background: #ef4444;
            color: #ffffff;
            font-size: 11px;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .dentist-notification-menu {
            position: absolute;
            top: calc(100% + 10px);
            right: 0;
            width: 360px;
            max-height: 420px;
            overflow-y: auto;
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 14px;
            box-shadow: 0 10px 25px rgba(15, 23, 42, 0.10);
            display: none;
            z-index: 1000;
        }

        .dentist-notification.open .dentist-notification-menu {
            display: block;
        }

        .dentist-notification-header {
            padding: 14px 16px;
            border-bottom: 1px solid #e5e7eb;
            font-size: 14px;
            font-weight: 800;
            color: #0f172a;
        }

        .dentist-notification-list {
            display: flex;
            flex-direction: column;
        }

        .dentist-notification-item {
            display: block;
            padding: 12px 16px;
            border-bottom: 1px solid #f1f5f9;
            text-decoration: none;
            color: #0f172a;
            background: #ffffff;
        }

        .dentist-notification-item:hover {
            background: #f8fafc;
        }

        .dentist-notification-item.unread {
            background: #f1f5f9;
        }

        .dentist-notification-title {
            font-size: 13px;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 4px;
        }

        .dentist-notification-text {
            font-size: 12px;
            color: #475569;
            line-height: 1.5;
        }

        .dentist-notification-time {
            font-size: 11px;
            color: #94a3b8;
            margin-top: 6px;
        }

        .dentist-notification-empty {
            padding: 18px 16px;
            font-size: 13px;
            color: #64748b;
            text-align: center;
        }

        .dentist-user-chip {
            padding: 8px 12px;
            border-radius: 999px;
            background: #ecfeff;
            color: #0f766e;
            font-size: 12px;
            font-weight: 800;
            white-space: nowrap;
        }

        .dentist-content-card {
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 16px;
            padding: 18px;
        }

        .dentist-alert {
            border-radius: 12px;
            padding: 14px 16px;
            margin-bottom: 16px;
            font-size: 14px;
            font-weight: 600;
        }

        .dentist-alert-success {
            background: #ecfdf5;
            border: 1px solid #bbf7d0;
            color: #166534;
        }

        .dentist-alert-danger {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #991b1b;
        }

        .dentist-alert-danger ul {
            margin: 8px 0 0;
            padding-left: 18px;
        }

        @media (max-width: 991.98px) {
            body {
                overflow: auto;
            }

            .dentist-shell {
                height: auto;
                min-height: 100vh;
                flex-direction: column;
                overflow: visible;
            }

            .dentist-shell-sidebar {
                width: 100%;
                height: auto;
                border-right: none;
                border-bottom: 1px solid #e5e7eb;
            }

            .dentist-shell-main {
                height: auto;
                overflow: visible;
                padding: 16px;
            }

            .dentist-topbar {
                justify-content: space-between;
                flex-wrap: wrap;
            }

            .dentist-topbar-right {
                width: 100%;
                justify-content: space-between;
                margin-left: 0;
            }

            .dentist-notification-menu {
                width: 300px;
                max-width: calc(100vw - 32px);
                right: 0;
            }
        }
    </style>

    <div class="dentist-shell">
        <div class="dentist-shell-sidebar">
            @include('dentist.partials.sidebar')
        </div>

        <main class="dentist-shell-main">
            <div class="dentist-topbar">
                <div class="dentist-topbar-right">
                    <div class="dentist-notification" id="dentistNotification">
                        <button type="button" class="dentist-notification-btn" id="dentistNotificationBtn">
                            Notifications
                            @if($dentistUnreadCount > 0)
                                <span class="dentist-notification-badge">{{ $dentistUnreadCount }}</span>
                            @endif
                        </button>

                        <div class="dentist-notification-menu" id="dentistNotificationMenu">
                            <div class="dentist-notification-header">Notifications</div>

                            @if($dentistNotifications->isEmpty())
                                <div class="dentist-notification-empty">
                                    No notifications available.
                                </div>
                            @else
                                <div class="dentist-notification-list">
                                    @foreach($dentistNotifications as $notification)
                                        @php
                                            $data = $notification->data ?? [];
                                            $targetUrl = $data['url'] ?? route('dentist.schedule.index');
                                        @endphp

                                        <a href="{{ route('dentist.notifications.read', $notification->id) }}?redirect={{ urlencode($targetUrl) }}"
                                           class="dentist-notification-item {{ is_null($notification->read_at) ? 'unread' : '' }}">
                                            <div class="dentist-notification-title">
                                                {{ $data['title'] ?? 'Notification' }}
                                            </div>
                                            <div class="dentist-notification-text">
                                                {{ $data['message'] ?? 'You have a new notification.' }}
                                            </div>
                                            <div class="dentist-notification-time">
                                                {{ $notification->created_at?->diffForHumans() }}
                                            </div>
                                        </a>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>

                    <span class="dentist-user-chip">
                        Dr. {{ auth()->user()->first_name ?? '' }} {{ auth()->user()->last_name ?? '' }}
                    </span>
                </div>
            </div>

            @if(session('success'))
                <div class="dentist-alert dentist-alert-success">
                    {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="dentist-alert dentist-alert-danger">
                    <strong>Please fix the following:</strong>
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="dentist-content-card">
                @hasSection('dentist_content')
                    @yield('dentist_content')
                @else
                    @yield('content')
                @endif
            </div>
        </main>
    </div>

    <script>
        (function () {
            const wrapper = document.getElementById('dentistNotification');
            const button = document.getElementById('dentistNotificationBtn');

            if (!wrapper || !button) return;

            button.addEventListener('click', function (event) {
                event.stopPropagation();
                wrapper.classList.toggle('open');
            });

            document.addEventListener('click', function (event) {
                if (!wrapper.contains(event.target)) {
                    wrapper.classList.remove('open');
                }
            });
        })();
    </script>
</body>
</html>
