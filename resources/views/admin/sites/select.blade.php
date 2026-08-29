<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>Select Site</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Outfit:wght@600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        body {
            font-family: 'DM Sans', sans-serif;
            background: radial-gradient(circle at top right, #e6e0ff 0, transparent 32%), #f6f7fc;
            color: #20243a;
            margin: 0;
            padding: 64px 24px;
        }

        .container {
            max-width: 1040px;
            margin: auto;
        }

        .eyebrow { color: #6d4aff; font-size: .78rem; font-weight: 700; letter-spacing: .1em; text-transform: uppercase; }
        h1 { margin: 8px 0; font-family: 'Outfit', sans-serif; font-size: clamp(2rem, 5vw, 3rem); }
        .intro { max-width: 540px; margin: 0 0 34px; color: #78809a; }

        .sites {
            display: grid;
            grid-template-columns:
                repeat(auto-fit, minmax(250px, 1fr));

            gap: 22px;
        }

        .site {
            background: #fff;
            padding: 26px;
            border: 1px solid #e9eaf2;
            border-radius: 18px;
            box-shadow: 0 10px 26px rgba(32, 36, 58, .05);
            transition: transform .2s ease, box-shadow .2s ease;
        }

        .site:hover { transform: translateY(-4px); box-shadow: 0 18px 30px rgba(53, 45, 111, .11); }

        .site h3 {
            margin: 0 0 8px;
            font-family: 'Outfit', sans-serif;
        }

        .site-icon { display: inline-flex; align-items: center; justify-content: center; width: 44px; height: 44px; margin-bottom: 18px; border-radius: 13px; background: #eeeaff; color: #6d4aff; font-size: 1.2rem; }
        .site p { margin: 0 0 24px; color: #78809a; }

        button {
            width: 100%;
            padding: 12px;
            border: 0;
            border-radius: 9px;
            background: linear-gradient(135deg, #8063ff, #6040ed);
            color: #fff;
            cursor: pointer;
            font: 600 .9rem 'DM Sans', sans-serif;
            box-shadow: 0 7px 16px rgba(96, 64, 237, .18);
        }

        .error { margin-bottom: 24px; padding: 12px 14px; border-radius: 10px; background: #fff2f3; color: #b42336; }
    </style>

</head>

<body>

    <div class="container">

        <div class="eyebrow">Matrimonial Admin</div>
        <h1>Select a website</h1>

        <p class="intro">
            Choose the matrimonial website you want
            to manage.
        </p>

        @if ($errors->any())

        <div class="error">
            {{ $errors->first() }}
        </div>

        @endif

        <div class="sites">

            @foreach ($sites as $site)

            <div class="site">

                <div class="site-icon"><i class="bi bi-globe2"></i></div>

                <h3>
                    {{ $site->name }}
                </h3>

                <p>
                    {{ $site->domain }}
                </p>

                <form
                    method="POST"
                    action="{{ route('admin.site.switch', $site->id) }}">

                    @csrf

                    <button type="submit">
                        Manage {{ $site->name }}
                    </button>

                </form>

            </div>

            @endforeach

        </div>

    </div>

</body>

</html>
