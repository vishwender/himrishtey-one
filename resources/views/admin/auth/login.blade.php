<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>Admin Login</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Outfit:wght@600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        body { min-height: 100vh; margin: 0; display: grid; place-items: center; background: radial-gradient(circle at top left, #e7e1ff 0, transparent 35%), #f6f7fc; color: #20243a; font-family: 'DM Sans', sans-serif; }
        .login-container { width: 390px; max-width: 90%; margin: 32px auto; padding: 36px; border: 1px solid #e9eaf2; border-radius: 20px; background: #fff; box-shadow: 0 20px 50px rgba(54, 45, 116, .12); }
        .login-brand { margin-bottom: 28px; text-align: center; }
        .login-mark { display: inline-flex; align-items: center; justify-content: center; width: 52px; height: 52px; margin-bottom: 14px; border-radius: 16px; background: linear-gradient(135deg, #9479ff, #6040ed); color: #fff; font-size: 1.35rem; box-shadow: 0 8px 18px rgba(96, 64, 237, .22); }
        h2 { margin: 0 0 6px; font-family: 'Outfit', sans-serif; }
        .login-brand p { margin: 0; color: #78809a; font-size: .92rem; }
        input[type="email"], input[type="password"] { width: 100%; box-sizing: border-box; margin: 6px 0 18px; padding: 12px 13px; border: 1px solid #dfe2ec; border-radius: 9px; font: inherit; }
        input:focus { outline: 0; border-color: #9a86ff; box-shadow: 0 0 0 4px rgba(109, 74, 255, .12); }
        label { color: #454b64; font-size: .88rem; font-weight: 600; }
        .remember { display: flex; align-items: center; gap: 8px; margin: 0 0 22px; font-weight: 500; }
        .remember input { width: 16px; height: 16px; margin: 0; accent-color: #6d4aff; }
        button[type="submit"] { width: 100%; padding: 12px; border: 0; border-radius: 9px; background: linear-gradient(135deg, #8063ff, #6040ed); color: #fff; cursor: pointer; font: 600 .95rem 'DM Sans', sans-serif; box-shadow: 0 7px 16px rgba(96, 64, 237, .2); }
        .error { margin-bottom: 15px; padding: 11px 13px; border-radius: 9px; background: #fff2f3; color: #b42336; font-size: .9rem; }
    </style>
</head>

<body>

    <div class="login-container">

        <div class="login-brand">
            <div class="login-mark"><i class="bi bi-heart-fill"></i></div>
            <h2>Welcome back</h2>
            <p>Sign in to manage your matrimonial sites.</p>
        </div>

        @if ($errors->any())
        <div class="error">
            {{ $errors->first() }}
        </div>
        @endif

        <form
            method="POST"
            action="{{ route('admin.login.submit') }}">

            @csrf

            <label>Email</label>

            <input
                type="email"
                name="email"
                value="{{ old('email') }}"
                required>

            <label>Password</label>

            <input
                type="password"
                name="password"
                required>

            <label class="remember">
                <input
                    type="checkbox"
                    name="remember"
                    value="1">

                Remember me
            </label>

            <button type="submit">
                Login
            </button>

        </form>

    </div>

</body>

</html>
