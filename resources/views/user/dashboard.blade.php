<!DOCTYPE html>
<html lang="pl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Użytkownika</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .dashboard-container {
            background: white;
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            max-width: 600px;
            width: 100%;
            padding: 60px 40px;
            text-align: center;
        }

        .icon-container {
            width: 100px;
            height: 100px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 50%;
            margin: 0 auto 30px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .icon-container svg {
            width: 50px;
            height: 50px;
            color: white;
        }

        h1 {
            font-size: 28px;
            color: #1f2937;
            margin-bottom: 15px;
        }

        .user-email {
            color: #6b7280;
            font-size: 16px;
            margin-bottom: 30px;
        }

        .info-box {
            background: #f3f4f6;
            border-left: 4px solid #667eea;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
            text-align: left;
        }

        .info-box h2 {
            color: #374151;
            font-size: 18px;
            margin-bottom: 10px;
        }

        .info-box p {
            color: #6b7280;
            font-size: 14px;
            line-height: 1.6;
        }

        .btn-logout {
            display: inline-block;
            padding: 14px 32px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            transition: transform 0.2s;
        }

        .btn-logout:hover {
            transform: translateY(-2px);
        }
    </style>
</head>

<body>
    <div class="dashboard-container">
        <div class="icon-container">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
            </svg>
        </div>

        <h1>Witaj, {{ Auth::user()->name }}!</h1>
        <p class="user-email">{{ Auth::user()->email }}</p>

        <div class="info-box">
            <h2>🚧 Panel w budowie</h2>
            <p>Funkcje dla użytkowników standardowych są jeszcze w fazie rozwoju. Wkrótce będziesz mógł korzystać z
                pełnej funkcjonalności systemu korepetycji.</p>
        </div>

        <div class="info-box">
            <h2>📅 Co będzie dostępne?</h2>
            <p>W przyszłości będziesz mógł przeglądać swój harmonogram lekcji, sprawdzać postępy w nauce oraz zarządzać
                płatnościami.</p>
        </div>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn-logout">Wyloguj się</button>
        </form>
    </div>
</body>

</html>
