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
        <!-- Hamburger toggle (mobile) -->
        <button id="sidebar-toggle" class="sidebar-toggle" aria-label="Menu" aria-controls="app-sidebar" aria-expanded="false">
            <svg class="hamburger-icon" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="3" y1="6" x2="21" y2="6"></line>
                <line x1="3" y1="12" x2="21" y2="12"></line>
                <line x1="3" y1="18" x2="21" y2="18"></line>
            </svg>
            <svg class="close-icon" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="18" y1="6" x2="6" y2="18"></line>
                <line x1="6" y1="6" x2="18" y2="18"></line>
            </svg>
        </button>

        <!-- Overlay (mobile) -->
        <div id="sidebar-overlay" class="sidebar-overlay" hidden></div>

        <!-- Sidebar -->
        <aside id="app-sidebar" class="sidebar">
            <div class="logo">
                <h2>Korepetycje</h2>
            </div>
            <nav class="nav">
                <a href="{{ route('student.index') }}"
                    class="nav-item {{ request()->routeIs('student.*') ? 'active' : '' }}"
                    title="Uczniowie">
                    <svg class="nav-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                        <circle cx="9" cy="7" r="4"></circle>
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                    </svg>
                    <span class="nav-label">Uczniowie</span>
                </a>
                <a href="{{ route('lesson.index') }}"
                    class="nav-item {{ request()->routeIs('lesson.*') ? 'active' : '' }}"
                    title="Lekcje">
                    <svg class="nav-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                        <line x1="16" y1="2" x2="16" y2="6"></line>
                        <line x1="8" y1="2" x2="8" y2="6"></line>
                        <line x1="3" y1="10" x2="21" y2="10"></line>
                    </svg>
                    <span class="nav-label">Lekcje</span>
                </a>
                <a href="{{ route('payment.index') }}"
                    class="nav-item {{ request()->routeIs('payment.*') ? 'active' : '' }}"
                    title="Platnosci">
                    <svg class="nav-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="1" y="4" width="22" height="16" rx="2" ry="2"></rect>
                        <line x1="1" y1="10" x2="23" y2="10"></line>
                    </svg>
                    <span class="nav-label">Platnosci</span>
                </a>
                <a href="{{ route('prompt.edit') }}"
                    class="nav-item {{ request()->routeIs('prompt.*') ? 'active' : '' }}"
                    title="Prompt">
                    <svg class="nav-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                    </svg>
                    <span class="nav-label">Prompt</span>
                </a>
                @if(request()->routeIs('student.*') || request()->routeIs('lesson.*') || request()->routeIs('payment.*'))
                <button id="help-tour-btn" class="nav-item"
                    style="background:none;border:none;cursor:pointer;width:100%;text-align:left;"
                    title="Pomoc / Tour">
                    <svg class="nav-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path>
                        <line x1="12" y1="17" x2="12.01" y2="17"></line>
                    </svg>
                    <span class="nav-label">Pomoc / Tour</span>
                </button>
                @endif

                <!-- Wylogowanie -->
                <form method="POST" action="{{ route('logout') }}" style="margin-top: auto; padding: 20px;">
                    @csrf
                    <button type="submit" class="nav-item"
                        style="width: 100%; text-align: left; background: none; border: none; cursor: pointer; color: #ecf0f1;"
                        title="Wyloguj sie">
                        <svg class="nav-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                            <polyline points="16 17 21 12 16 7"></polyline>
                            <line x1="21" y1="12" x2="9" y2="12"></line>
                        </svg>
                        <span class="nav-label">Wyloguj sie</span>
                    </button>
                </form>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            @if(config('app.demo_mode'))
                <div class="demo-banner">
                    🎬 Srodowisko demo. Dane sesji resetuja sie po ~1h bezczynnosci.
                </div>
            @endif
            {{ $slot }}
            <!-- Kontener dla chatbota -->
            <div id="chatbot-root"></div>
        </main>
    </div>
</body>

</html>
