<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --card-bg: rgba(38, 30, 26, 0.78);
            --card-border: rgba(255, 255, 255, 0.08);
            --text: #f7f3ef;
            --muted: rgba(255, 255, 255, 0.65);
            --accent: #f4b327;
            --accent-hover: #eaa51f;
            --line: rgba(255, 255, 255, 0.35);
            --danger: #ffb3b3;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: "Poppins", sans-serif;
            min-height: 100vh;
            color: var(--text);
            display: grid;
            place-items: center;
            position: relative;
            overflow: hidden;
        }

        body::before {
            content: "";
            position: fixed;
            inset: -20px;
            background: url("/images/bg-login.jpg") center / cover no-repeat;
            filter: blur(2.5px);
            transform: scale(1.02);
            z-index: 0;
        }

        body::after {
            content: "";
            position: fixed;
            inset: 0;
            background: radial-gradient(circle at 70% 20%, rgba(255, 198, 120, 0.15), transparent 55%),
                        linear-gradient(135deg, rgba(10, 10, 12, 0.65), rgba(60, 30, 10, 0.55));
            z-index: 1;
        }

        .login-card {
            width: min(440px, 90vw);
            background: var(--card-bg);
            border: 1px solid var(--card-border);
            border-radius: 18px;
            padding: 32px 32px 28px;
            position: relative;
            z-index: 2;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.35);
            backdrop-filter: blur(8px);
        }

        .close-icon {
            position: absolute;
            top: 14px;
            right: 16px;
            font-size: 20px;
            color: rgba(255, 255, 255, 0.55);
            user-select: none;
        }

        h1 {
            margin: 0 0 24px;
            text-align: center;
            font-size: 22px;
            font-weight: 600;
            letter-spacing: 0.3px;
        }

        .field {
            margin-bottom: 20px;
        }

        .field label {
            display: block;
            font-size: 13px;
            color: var(--muted);
            margin-bottom: 8px;
        }

        .input-line {
            display: flex;
            align-items: center;
            gap: 10px;
            border-bottom: 1px solid var(--line);
            padding-bottom: 8px;
        }

        .input-line input,
        .input-line select {
            flex: 1;
            background: transparent;
            border: none;
            outline: none;
            color: var(--text);
            font-size: 14px;
            padding: 4px 0;
            font-family: inherit;
            appearance: none;
            box-shadow: none;
        }

        .input-line input:-webkit-autofill,
        .input-line input:-webkit-autofill:hover,
        .input-line input:-webkit-autofill:focus,
        .input-line select:-webkit-autofill,
        .input-line select:-webkit-autofill:hover,
        .input-line select:-webkit-autofill:focus {
            -webkit-text-fill-color: var(--text);
            -webkit-box-shadow: 0 0 0px 1000px transparent inset;
            transition: background-color 9999s ease-in-out 0s;
        }

        .input-line select option {
            color: #1a1a1a;
        }

        .input-icon {
            width: 18px;
            height: 18px;
            opacity: 0.7;
            flex-shrink: 0;
        }

        .error {
            margin-top: 8px;
            font-size: 12px;
            color: var(--danger);
        }

        .status {
            font-size: 13px;
            text-align: center;
            color: var(--muted);
            margin-bottom: 18px;
        }

        .btn-login {
            width: 100%;
            margin-top: 10px;
            padding: 10px 18px;
            border-radius: 10px;
            border: none;
            background: var(--accent);
            color: #1a1a1a;
            font-weight: 600;
            letter-spacing: 0.2px;
            cursor: pointer;
            transition: transform 0.15s ease, background 0.2s ease;
        }

        .btn-login:hover {
            background: var(--accent-hover);
            transform: translateY(-1px);
        }

        .footer-note {
            text-align: center;
            font-size: 12px;
            color: rgba(255, 255, 255, 0.55);
            margin-top: 18px;
        }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="close-icon">x</div>
        <h1>Login</h1>

        @if (session('status'))
            <div class="status">{{ session('status') }}</div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="field">
                <label for="email">Email</label>
                <div class="input-line">
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username">
                    <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M3 5h18v14H3z"></path>
                        <path d="m3 7 9 6 9-6"></path>
                    </svg>
                </div>
                @error('email')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>

            <div class="field">
                <label for="role">Role</label>
                <div class="input-line">
                    <select id="role" name="role" required>
                        <option value="" disabled @selected(old('role') === null)>Select role</option>
                        <option value="admin" @selected(old('role') === 'admin')>Admin</option>
                        <option value="employee" @selected(old('role') === 'employee')>Employee</option>
                    </select>
                    <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                        <path d="m6 9 6 6 6-6"></path>
                    </svg>
                </div>
                @error('role')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>

            <div class="field">
                <label for="password">Password</label>
                <div class="input-line">
                    <input id="password" type="password" name="password" required autocomplete="current-password">
                    <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="4" y="11" width="16" height="9" rx="2"></rect>
                        <path d="M8 11V7a4 4 0 0 1 8 0v4"></path>
                    </svg>
                </div>
                @error('password')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn-login">Login</button>
        </form>

        <div class="footer-note">Construction Management System</div>
    </div>
</body>
</html>
