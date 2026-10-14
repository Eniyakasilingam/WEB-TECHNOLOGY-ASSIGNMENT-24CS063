<!DOCTYPE html>
<html>
<head>
    <title>Student Portal | Login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <style>
        * { box-sizing: border-box; }

        body {
            margin: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: "Segoe UI", sans-serif;
            color: #e5e7eb;
            background:
                radial-gradient(ellipse at top left, #123c2c, transparent 45%),
                radial-gradient(ellipse at bottom right, #102a22, transparent 45%),
                #070b09;
        }

        .login-card {
            width: 90%;
            max-width: 420px;
            padding: 42px 35px;
            border: 1px solid rgba(74, 222, 128, .25);
            border-radius: 24px;
            background: rgba(255, 255, 255, .045);
            backdrop-filter: blur(20px);
            box-shadow: 0 20px 70px rgba(0, 0, 0, .45);
        }

        .logo {
            width: 58px;
            height: 58px;
            display: grid;
            place-items: center;
            margin: 0 auto 20px;
            border-radius: 18px;
            background: rgba(74, 222, 128, .12);
            border: 1px solid rgba(74, 222, 128, .4);
            color: #4ade80;
            font-size: 28px;
        }

        h1 {
            text-align: center;
            font-size: 27px;
            margin: 0 0 10px;
        }

        .subtitle {
            text-align: center;
            color: #9ca3af;
            font-size: 14px;
            margin-bottom: 32px;
        }

        label {
            display: block;
            margin: 18px 0 8px;
            color: #d1d5db;
            font-size: 13px;
        }

        input {
            width: 100%;
            padding: 14px;
            border: 1px solid #303c35;
            border-radius: 12px;
            outline: none;
            background: rgba(0, 0, 0, .25);
            color: white;
            font-size: 14px;
        }

        input:focus {
            border-color: #4ade80;
            box-shadow: 0 0 0 3px rgba(74, 222, 128, .1);
        }

        button {
            width: 100%;
            margin-top: 28px;
            padding: 14px;
            border: none;
            border-radius: 12px;
            background: linear-gradient(100deg, #22c55e, #4ade80);
            color: #052e16;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            transition: .2s;
        }

        button:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(74, 222, 128, .2);
        }

        .footer {
            margin-top: 28px;
            text-align: center;
            color: #6b7280;
            font-size: 12px;
        }

        .footer span { color: #4ade80; }
    </style>
</head>

<body>
    <main class="login-card">
        <div class="logo">&#9783;</div>

        <h1>Student Portal</h1>
        <p class="subtitle">Your academic space, securely connected.</p>

        <form action="login" method="post">
            <label for="username">Username</label>
            <input id="username" type="text"
                   name="username" placeholder="Enter your username" required>

            <label for="password">Password</label>
            <input id="password" type="password"
                   name="password" placeholder="Enter your password" required>

            <button type="submit">Sign In &rarr;</button>
        </form>

        <div class="footer">
            SECURE ACCESS <span>●</span> STUDENT MANAGEMENT
        </div>
    </main>
</body>
</html>