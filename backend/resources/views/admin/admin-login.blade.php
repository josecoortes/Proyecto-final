<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración – Burguer Marina</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #111;
            color: #fff;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .card {
            background: #1a1a1a;
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 16px;
            padding: 48px 40px;
            width: 100%;
            max-width: 420px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.5);
        }
        .logo { text-align: center; margin-bottom: 32px; }
        .logo h1 { font-size: 1.5rem; color: #e3000f; }
        .logo p { color: #888; font-size: 0.9rem; margin-top: 6px; }
        .alert-error {
            background: rgba(227,0,15,0.1);
            border: 1px solid rgba(227,0,15,0.3);
            color: #ff5252;
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 0.9rem;
        }
        .form-group { margin-bottom: 20px; }
        label { display: block; color: #aaa; font-size: 0.85rem; margin-bottom: 8px; }
        input {
            width: 100%;
            padding: 12px 16px;
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 8px;
            color: #fff;
            font-size: 1rem;
            outline: none;
            transition: border-color 0.2s;
        }
        input:focus { border-color: #e3000f; }
        button[type="submit"] {
            width: 100%;
            padding: 14px;
            background: #e3000f;
            color: #fff;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            margin-top: 8px;
            transition: background 0.2s;
        }
        button[type="submit"]:hover { background: #c0000d; }
        .back-link { text-align: center; margin-top: 24px; }
        .back-link a { color: #888; font-size: 0.85rem; text-decoration: none; }
        .back-link a:hover { color: #fff; }
    </style>
</head>
<body>
    <div class="card">
        <div class="logo">
            <h1>🍔 Burguer Marina</h1>
            <p>Acceso exclusivo al panel de administración</p>
        </div>

        @if (session('status'))
            <div class="alert-error">{{ session('status') }}</div>
        @endif

        @if ($errors->any())
            <div class="alert-error">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="/admin/login">
            @csrf
            <div class="form-group">
                <label for="email">Correo electrónico</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}"
                       placeholder="admin@marinaburguer.com" required autofocus>
            </div>
            <div class="form-group">
                <label for="password">Contraseña</label>
                <input type="password" id="password" name="password"
                       placeholder="••••••••" required>
            </div>
            <button type="submit">Entrar al Panel</button>
        </form>

        <div class="back-link">
            <a href="/">← Volver a la tienda</a>
        </div>
    </div>
</body>
</html>
