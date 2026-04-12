<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'Dental Clinic Care') }} - Dentist</title>

</head>
<body style="margin: 0; background: #f8fafc; color: #0f172a; font-family: Arial, Helvetica, sans-serif;">
    <style>
        .dentist-shell {
            min-height: 100vh;
            display: grid;
            grid-template-columns: 290px minmax(0, 1fr);
            background: #f8fafc;
        }

        .dentist-shell-sidebar {
            min-height: 100vh;
            background: #ffffff;
            border-right: 1px solid #e5e7eb;
        }

        .dentist-shell-main {
            min-width: 0;
            padding: 24px;
        }

        .dentist-topbar {
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 20px;
            padding: 18px 22px;
            margin-bottom: 22px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 16px;
        }

        .dentist-topbar-title {
            margin: 0;
            font-size: 26px;
            font-weight: 800;
            color: #0f172a;
        }

        .dentist-topbar-subtitle {
            margin: 6px 0 0;
            font-size: 14px;
            color: #64748b;
        }

        .dentist-topbar-right {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        .dentist-topbar-badge {
            padding: 8px 12px;
            border-radius: 999px;
            background: #ecfeff;
            color: #0f766e;
            font-size: 12px;
            font-weight: 800;
        }

        .dentist-content-card {
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 20px;
            padding: 22px;
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.04);
        }

        .dentist-alert {
            border-radius: 16px;
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
            .dentist-shell {
                grid-template-columns: 1fr;
            }

            .dentist-shell-sidebar {
                min-height: auto;
                border-right: none;
                border-bottom: 1px solid #e5e7eb;
            }

            .dentist-shell-main {
                padding: 16px;
            }

            .dentist-topbar {
                flex-direction: column;
                align-items: flex-start;
            }
        }
    </style>

    <div class="dentist-shell">
        <div class="dentist-shell-sidebar">
            @include('dentist.partials.sidebar')
        </div>

        <main class="dentist-shell-main">
            <div class="dentist-topbar">
                <div>
                    <h1 class="dentist-topbar-title">@yield('page_title', 'Dentist Dashboard')</h1>
                    <p class="dentist-topbar-subtitle">
                        Welcome, Dr. {{ auth()->user()->first_name ?? '' }} {{ auth()->user()->last_name ?? '' }}
                    </p>
                </div>

                <div class="dentist-topbar-right">
                    <span class="dentist-topbar-badge">Dentist Workspace</span>
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
                @yield('dentist_content')
            </div>
        </main>
    </div>
</body>
</html>
