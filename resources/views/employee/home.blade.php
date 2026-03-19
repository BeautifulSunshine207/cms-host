<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Employee Home</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg: #0f1115;
            --card: #1b1f27;
            --text: #f1f4f9;
            --muted: #8f98a8;
            --accent: #f4b327;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            display: grid;
            place-items: center;
            font-family: "Manrope", sans-serif;
            color: var(--text);
            background: radial-gradient(circle at top left, rgba(244, 179, 39, 0.2), transparent 45%),
                        linear-gradient(135deg, #0b0c10 0%, #141824 50%, #0b0c10 100%);
        }

        .card {
            width: min(520px, 92vw);
            background: var(--card);
            border-radius: 18px;
            padding: 32px;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.35);
        }

        h1 {
            margin: 0 0 10px;
            font-size: 24px;
            font-weight: 700;
        }

        p {
            margin: 0 0 22px;
            color: var(--muted);
            font-size: 14px;
            line-height: 1.6;
        }

        .actions {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }

        .btn {
            border: none;
            background: var(--accent);
            color: #1a1a1a;
            padding: 10px 16px;
            border-radius: 10px;
            font-weight: 600;
            cursor: pointer;
        }

        .btn-outline {
            background: transparent;
            color: var(--text);
            border: 1px solid rgba(255, 255, 255, 0.15);
        }
    </style>
</head>
<body>
    <div class="card">
        <h1>Welcome, {{ auth()->user()->name ?? 'Employee' }}</h1>
        <p>TEST EMPLOYEE SIDE.</p>
        <div class="actions">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn">Logout</button>
            </form>
        </div>
    </div>
</body>
</html>
