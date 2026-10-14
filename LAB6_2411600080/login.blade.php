<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Inventory System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        html, body {
            height: 100%;
            margin: 0;
        }
        body {
            display: flex;
            align-items: center;
            justify-content: center;
            background: #00c853;
            font-family: system-ui, -apple-system, "Segoe UI", Roboto, sans-serif;
        }
        .login-wrapper {
            width: 100%;
            max-width: 400px;
            padding: 20px;
        }
        .login-card {
            background: rgba(255, 255, 255, 0.18);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.25);
            border-radius: 16px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.15);
            overflow: hidden;
        }
        .login-header {
            background: rgba(0, 150, 60, 0.45);
            padding: 28px 24px 20px;
            text-align: center;
            color: #fff;
        }
        .login-header h1 {
            font-size: 1.5rem;
            font-weight: 700;
            letter-spacing: 1px;
            margin: 0 0 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        .login-header .divider {
            width: 60%;
            height: 2px;
            background: rgba(255,255,255,0.4);
            margin: 10px auto;
        }
        .login-header h2 {
            font-size: 1.15rem;
            font-weight: 600;
            margin: 0;
        }
        .login-header p {
            font-size: 0.85rem;
            opacity: 0.9;
            margin: 4px 0 0;
        }
        .login-body {
            padding: 28px 28px 24px;
        }
        .form-label {
            color: rgba(255,255,255,0.9);
            font-weight: 500;
            font-size: 0.9rem;
            margin-bottom: 6px;
        }
        .form-control {
            background: rgba(255, 255, 255, 0.22);
            border: 1px solid rgba(255, 255, 255, 0.3);
            color: #fff;
            border-radius: 8px;
            padding: 12px 14px 12px 42px;
        }
        .form-control::placeholder {
            color: rgba(255, 255, 255, 0.65);
        }
        .form-control:focus {
            background: rgba(255, 255, 255, 0.28);
            border-color: rgba(255, 255, 255, 0.55);
            box-shadow: 0 0 0 0.2rem rgba(255, 255, 255, 0.2);
            color: #fff;
        }
        .input-group-icon {
            position: relative;
        }
        .input-group-icon i {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: rgba(255, 255, 255, 0.75);
            z-index: 5;
        }
        .form-check-label {
            color: rgba(255, 255, 255, 0.9);
            font-size: 0.9rem;
        }
        .form-check-input {
            background-color: rgba(255,255,255,0.25);
            border-color: rgba(255,255,255,0.4);
        }
        .form-check-input:checked {
            background-color: #00e676;
            border-color: #00e676;
        }
        .btn-login {
            background: #00e676;
            border: none;
            color: #004d25;
            font-weight: 700;
            font-size: 1rem;
            padding: 12px;
            border-radius: 8px;
            letter-spacing: 0.5px;
            transition: all 0.2s ease;
        }
        .btn-login:hover {
            background: #00c853;
            color: #fff;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        }
        .demo-creds {
            text-align: center;
            font-size: 0.8rem;
            color: rgba(255, 255, 255, 0.75);
            margin-top: 14px;
        }
        .demo-creds strong {
            color: #fff;
        }
        .alert {
            border-radius: 8px;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
    <div class="login-wrapper">
        <div class="login-card">
            <div class="login-header">
                <h1>
                    INVENTORY SYSTEM
                    <i class="bi bi-box-seam"></i>
                </h1>
                <div class="divider"></div>
                <h2>WELCOME ADMIN</h2>
                <p>Login to your dashboard</p>
            </div>

            <div class="login-body">
                @if (session('status'))
                    <div class="alert alert-success mb-3">
                        {{ session('status') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <div class="mb-3">
                        <label for="email" class="form-label">Username</label>
                        <div class="input-group-icon">
                            <i class="bi bi-person"></i>
                            <input type="email"
                                   class="form-control @error('email') is-invalid @enderror"
                                   id="email"
                                   name="email"
                                   value="{{ old('email') }}"
                                   placeholder="Enter username"
                                   required
                                   autofocus>
                        </div>
                        @error('email')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <div class="input-group-icon">
                            <i class="bi bi-lock"></i>
                            <input type="password"
                                   class="form-control @error('password') is-invalid @enderror"
                                   id="password"
                                   name="password"
                                   placeholder="Enter password"
                                   required>
                        </div>
                        @error('password')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4 form-check">
                        <input type="checkbox" class="form-check-input" id="remember_me" name="remember">
                        <label class="form-check-label" for="remember_me">Remember Me</label>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-login">
                            Login
                        </button>
                    </div>

                    <p class="demo-creds">
                        Demo credentials: <strong>admin / password123</strong>
                    </p>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
