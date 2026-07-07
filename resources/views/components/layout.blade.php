<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Korepetycje</title>
    @viteReactRefresh
    @vite(['resources/css/app.css', 'resources/css/chatbot.css', 'resources/js/app.jsx'])
</head>

<body>
    <div class="admin-container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="logo">
                <h2>Korepetycje</h2>
            </div>
            <nav class="nav">
                <a href="{{ route('student.index') }}"
                    class="nav-item {{ request()->routeIs('student.*') ? 'active' : '' }}">
                    Uczniowie
                </a>
                <a href="{{ route('lesson.index') }}"
                    class="nav-item {{ request()->routeIs('lesson.*') ? 'active' : '' }}">
                    Lekcje
                </a>
                <a href="{{ route('payment.index') }}"
                    class="nav-item {{ request()->routeIs('payment.*') ? 'active' : '' }}">
                    Płatności
                </a>
                <a href="{{ route('prompt.edit') }}"
                    class="nav-item {{ request()->routeIs('prompt.*') ? 'active' : '' }}">
                    Prompt
                </a>
                @if(request()->routeIs('student.*') || request()->routeIs('lesson.*') || request()->routeIs('payment.*'))
                <button id="help-tour-btn" class="nav-item"
                    style="background:none;border:none;cursor:pointer;width:100%;text-align:left;">
                    Pomoc / Tour
                </button>
                @endif

                <!-- Wylogowanie -->
                <form method="POST" action="{{ route('logout') }}" style="margin-top: auto; padding: 20px;">
                    @csrf
                    <button type="submit" class="nav-item"
                        style="width: 100%; text-align: left; background: none; border: none; cursor: pointer; color: #ecf0f1;">
                        Wyloguj się
                    </button>
                </form>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            @if(config('app.demo_mode'))
                <div class="demo-banner">
                    🎬 Środowisko demo. Dane sesji resetują się po ~1h bezczynności.
                </div>
            @endif
            {{ $slot }}
            <!-- Kontener dla chatbota -->
            <div id="chatbot-root"></div>
        </main>
    </div>
</body>

</html>
