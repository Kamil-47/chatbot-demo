<!DOCTYPE html>
<html lang="pl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>System Korepetycji - Logowanie</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: #3b82f6;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .welcome-container {
            background: white;
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            max-width: 450px;
            width: 100%;
            overflow: hidden;
        }

        .forms-container {
            padding: 25px 40px 35px;
        }

        .tabs {
            display: flex;
            margin-bottom: 30px;
            border-bottom: 2px solid #e5e7eb;
        }

        .tab {
            flex: 1;
            padding: 15px;
            text-align: center;
            cursor: pointer;
            font-weight: 600;
            color: #6b7280;
            border-bottom: 3px solid transparent;
            transition: all 0.3s;
        }

        .tab.active {
            color: #667eea;
            border-bottom-color: #667eea;
        }

        .form-content {
            display: none;
        }

        .form-content.active {
            display: block;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #374151;
            font-size: 14px;
        }

        .form-input {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            font-size: 14px;
            transition: border-color 0.3s;
        }

        .form-input:focus {
            outline: none;
            border-color: #667eea;
        }

        .form-checkbox {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 20px;
        }

        .form-checkbox input {
            width: auto;
        }

        .btn-submit {
            width: 100%;
            padding: 14px;
            background: #3b82f6;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            background: #2563eb;
        }

        .error-message {
            background: #fee2e2;
            color: #991b1b;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
        }

        @media (max-width: 768px) {
            .welcome-container {
                flex-direction: column;
            }

            .welcome-sidebar {
                padding: 40px 30px;
            }

            .forms-container {
                padding: 40px 30px;
            }
        }
    </style>
</head>

<body>
    <div class="welcome-container">

        <div class="forms-container">
            <div class="tabs">
                <div class="tab active" onclick="showTab(this, 'login')">Logowanie</div>
                @unless(config('app.demo_mode'))
                    <div class="tab" onclick="showTab(this, 'register')">Rejestracja</div>
                @endunless
            </div>

            <!-- Formularz logowania -->
            <div id="login-form" class="form-content active">
                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    @if ($errors->has('email'))
                        <div class="error-message">
                            {{ $errors->first('email') }}
                        </div>
                    @endif

                    <div class="form-group">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-input" value="{{ old('email') }}" required
                            autofocus>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Hasło</label>
                        <input type="password" name="password" class="form-input" required>
                    </div>

                    <div class="form-checkbox">
                        <input type="checkbox" name="remember" id="remember">
                        <label for="remember">Zapamiętaj mnie</label>
                    </div>

                    <button type="submit" class="btn-submit">Zaloguj się</button>
                </form>

                @if(config('app.demo_mode'))
                    <div style="margin-top: 24px; padding-top: 10px; border-top: 1px solid #e5e7eb; text-align: center;">
                        <p style="color: #6b7280; font-size: 13px; margin-bottom: 12px;">— lub —</p>
                        <form method="POST" action="{{ route('login.demo') }}">
                            @csrf
                            <button type="submit" class="btn-submit" style="background: #10b981;">🎬 Zaloguj jako demo</button>
                        </form>
                    </div>
                @endif
            </div>

            @unless(config('app.demo_mode'))
            <!-- Formularz rejestracji -->
            <div id="register-form" class="form-content">
                <form method="POST" action="{{ route('register') }}">
                    @csrf

                    @if ($errors->any() && !$errors->has('email'))
                        <div class="error-message">
                            <ul style="margin-left: 20px;">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="form-group">
                        <label class="form-label">Imię i nazwisko</label>
                        <input type="text" name="name" class="form-input" value="{{ old('name') }}" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-input" value="{{ old('email') }}" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Hasło</label>
                        <input type="password" name="password" class="form-input" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Potwierdź hasło</label>
                        <input type="password" name="password_confirmation" class="form-input" required>
                    </div>

                    <button type="submit" class="btn-submit">Zarejestruj się</button>
                </form>
            </div>
            @endunless
        </div>
    </div>

    <script>
        function showTab(clickedTab, tabName) {
            document.querySelectorAll('.form-content').forEach(content => {
                content.classList.remove('active');
            });

            document.querySelectorAll('.tab').forEach(tab => {
                tab.classList.remove('active');
            });

            document.getElementById(tabName + '-form').classList.add('active');
            clickedTab.classList.add('active');
        }
    </script>
</body>

</html>
