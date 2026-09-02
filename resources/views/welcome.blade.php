<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="The secure operations portal for the Him Rishtey team.">
    <title>{{ config('app.name', 'Him Rishtey') }} — Admin Portal</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Outfit:wght@600;700&display=swap" rel="stylesheet">
    <style>
        :root { --ink:#20243a; --muted:#707892; --purple:#6847ef; --lavender:#eeeaff; --line:rgba(76,59,151,.12); }
        * { box-sizing:border-box; }
        html { scroll-behavior:smooth; }
        body { min-height:100vh; margin:0; background:radial-gradient(circle at 6% 8%,rgba(184,165,255,.28),transparent 27rem),radial-gradient(circle at 91% 88%,rgba(255,190,214,.25),transparent 26rem),#f8f8fc; color:var(--ink); font-family:'DM Sans',sans-serif; }
        a { color:inherit; text-decoration:none; }
        .page-shell { display:flex; min-height:100vh; flex-direction:column; overflow:hidden; }
        .nav,.hero,.trust-row,.footer { width:min(1160px,calc(100% - 48px)); margin-inline:auto; }
        .nav { display:flex; min-height:92px; align-items:center; justify-content:space-between; }
        .brand { display:inline-flex; align-items:center; gap:12px; }
        .brand-mark { display:grid; width:42px; height:42px; place-items:center; border-radius:13px; background:linear-gradient(145deg,#9479ff,#5f3fe8); box-shadow:0 9px 22px rgba(96,64,237,.24); }
        .brand-mark svg { width:22px; color:#fff; }
        .brand-copy { display:grid; line-height:1.05; }
        .brand-name { font:700 1.12rem 'Outfit',sans-serif; }
        .brand-label { margin-top:5px; color:var(--muted); font-size:.67rem; font-weight:700; letter-spacing:.15em; text-transform:uppercase; }
        .nav-actions { display:flex; align-items:center; gap:20px; }
        .secure-label { display:flex; align-items:center; gap:7px; color:var(--muted); font-size:.83rem; font-weight:600; }
        .secure-label svg { width:15px; color:#42a977; }
        .button { display:inline-flex; min-height:46px; align-items:center; justify-content:center; gap:9px; padding:0 21px; border-radius:11px; font-size:.9rem; font-weight:700; transition:transform .2s ease,box-shadow .2s ease; }
        .button:hover { transform:translateY(-2px); }
        .button svg { width:17px; }
        .button-primary { background:linear-gradient(135deg,#8063ff,var(--purple)); color:#fff; box-shadow:0 10px 24px rgba(96,64,237,.22); }
        .button-primary:hover { box-shadow:0 14px 29px rgba(96,64,237,.3); }
        .button-secondary { border:1px solid var(--line); background:rgba(255,255,255,.7); color:#4e5670; }
        .hero { display:grid; flex:1; grid-template-columns:minmax(0,1.04fr) minmax(400px,.96fr); align-items:center; gap:clamp(48px,7vw,94px); padding:66px 0 84px; }
        .eyebrow { display:inline-flex; align-items:center; gap:9px; margin-bottom:25px; padding:8px 12px; border:1px solid #ddd5ff; border-radius:999px; background:rgba(244,241,255,.78); color:#6246ca; font-size:.75rem; font-weight:700; letter-spacing:.08em; text-transform:uppercase; }
        .eyebrow-dot { width:7px; height:7px; border-radius:50%; background:#8b6cff; box-shadow:0 0 0 4px rgba(139,108,255,.13); }
        h1 { max-width:680px; margin:0; font:700 clamp(3rem,5.8vw,5.25rem)/.98 'Outfit',sans-serif; letter-spacing:-.055em; }
        h1 span { color:var(--purple); }
        .hero-copy>p { max-width:590px; margin:27px 0 33px; color:var(--muted); font-size:clamp(1rem,1.5vw,1.13rem); line-height:1.75; }
        .hero-actions { display:flex; flex-wrap:wrap; gap:12px; }
        .portal-card { position:relative; padding:18px; border:1px solid rgba(255,255,255,.75); border-radius:30px; background:rgba(255,255,255,.48); box-shadow:0 30px 70px rgba(52,41,112,.13); backdrop-filter:blur(18px); }
        .portal-card::before { position:absolute; z-index:-1; top:-45px; right:-45px; width:150px; height:150px; border-radius:50%; background:linear-gradient(135deg,#8c70ff,#efb3d2); content:''; opacity:.55; }
        .dashboard { overflow:hidden; min-height:430px; border:1px solid var(--line); border-radius:21px; background:#fff; }
        .dashboard-head { display:flex; align-items:center; justify-content:space-between; padding:21px 23px; border-bottom:1px solid #eeedf4; }
        .dashboard-title { font-weight:700; }
        .live-pill { display:flex; align-items:center; gap:7px; color:#348960; font-size:.73rem; font-weight:700; }
        .live-pill::before { width:7px; height:7px; border-radius:50%; background:#51bd86; content:''; }
        .dashboard-body { padding:23px; }
        .metric-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:12px; }
        .metric { padding:17px 15px; border:1px solid #edebf5; border-radius:14px; background:#fbfaff; }
        .metric-icon { display:grid; width:32px; height:32px; margin-bottom:14px; place-items:center; border-radius:9px; background:var(--lavender); color:var(--purple); }
        .metric-icon svg { width:16px; }
        .metric strong { display:block; font:700 1.2rem 'Outfit',sans-serif; }
        .metric small { color:var(--muted); font-size:.67rem; }
        .activity { margin-top:20px; padding:20px; border-radius:15px; background:#f8f8fc; }
        .activity-head { display:flex; justify-content:space-between; margin-bottom:18px; font-size:.82rem; font-weight:700; }
        .activity-head span:last-child { color:var(--purple); font-size:.7rem; }
        .activity-row { display:grid; grid-template-columns:34px 1fr auto; align-items:center; gap:11px; padding:10px 0; }
        .activity-row+.activity-row { border-top:1px solid #e9e8f0; }
        .avatar { display:grid; width:34px; height:34px; place-items:center; border-radius:10px; background:#e9e3ff; color:#674bd6; font-size:.68rem; font-weight:800; }
        .activity-copy { display:grid; gap:3px; font-size:.75rem; font-weight:600; }
        .activity-copy small { color:var(--muted); font-size:.66rem; font-weight:500; }
        .status { padding:5px 8px; border-radius:999px; background:#e9f8f0; color:#2f8b5d; font-size:.62rem; font-weight:700; }
        .trust-row { display:grid; grid-template-columns:repeat(3,1fr); border-top:1px solid var(--line); }
        .trust-item { display:flex; align-items:center; gap:13px; padding:25px 0; }
        .trust-item+.trust-item { justify-content:center; border-left:1px solid var(--line); }
        .trust-icon { display:grid; width:38px; height:38px; flex:0 0 auto; place-items:center; border-radius:11px; background:var(--lavender); color:var(--purple); }
        .trust-icon svg { width:18px; }
        .trust-copy { display:grid; gap:3px; }
        .trust-copy strong { font-size:.82rem; }
        .trust-copy span { color:var(--muted); font-size:.7rem; }
        .footer { display:flex; justify-content:space-between; padding:24px 0 28px; color:#888fa3; font-size:.72rem; }
        .footer span:last-child { display:flex; align-items:center; gap:6px; }
        @media (max-width:900px) { .hero { grid-template-columns:1fr; padding-top:50px; } .hero-copy { text-align:center; } .hero-copy>p { margin-inline:auto; } .hero-actions { justify-content:center; } .portal-card { width:min(570px,100%); margin-inline:auto; } }
        @media (max-width:620px) { .nav,.hero,.trust-row,.footer { width:min(100% - 30px,1160px); } .nav { min-height:78px; } .secure-label { display:none; } .nav-actions .button { min-height:42px; padding:0 15px; } .hero { padding:42px 0 62px; } h1 { font-size:clamp(2.75rem,15vw,4.2rem); } .portal-card { padding:9px; border-radius:23px; } .dashboard { min-height:auto; } .dashboard-body { padding:16px; } .metric-grid { grid-template-columns:1fr; } .metric { display:grid; grid-template-columns:34px 1fr auto; align-items:center; gap:10px; padding:12px; } .metric-icon { margin:0; } .activity { padding:15px; } .activity-row { grid-template-columns:32px 1fr; } .status { display:none; } .trust-row { grid-template-columns:1fr; } .trust-item,.trust-item+.trust-item { justify-content:flex-start; border-left:0; } .trust-item+.trust-item { border-top:1px solid var(--line); } .footer { flex-direction:column; gap:8px; } }
        @media (prefers-reduced-motion:no-preference) { .hero-copy { animation:reveal .7s ease both; } .portal-card { animation:reveal .7s .12s ease both; } @keyframes reveal { from { opacity:0; transform:translateY(18px); } } }
    </style>
</head>
<body>
<div class="page-shell">
    <nav class="nav" aria-label="Primary navigation">
        <a class="brand" href="{{ url('/') }}" aria-label="{{ config('app.name', 'Him Rishtey') }} home">
            <span class="brand-mark"><svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 21s-7.1-4.5-9.5-8.75C.2 8.2 2.5 3.5 6.8 3.5c2.2 0 3.8 1.25 5.2 3 1.4-1.75 3-3 5.2-3 4.3 0 6.6 4.7 4.3 8.75C19.1 16.5 12 21 12 21Z"/></svg></span>
            <span class="brand-copy"><span class="brand-name">{{ config('app.name', 'Him Rishtey') }}</span><span class="brand-label">Operations portal</span></span>
        </a>
        <div class="nav-actions">
            <span class="secure-label"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10Z"/><path d="m9 12 2 2 4-4"/></svg>Secure access</span>
            <a class="button button-primary" href="{{ route('admin.login') }}">Staff sign in <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="m9 18 6-6-6-6"/></svg></a>
        </div>
    </nav>
    <main class="hero">
        <section class="hero-copy">
            <div class="eyebrow"><span class="eyebrow-dot"></span> Private team workspace</div>
            <h1>Meaningful matches.<br><span>Managed beautifully.</span></h1>
            <p>A focused operations hub for the people behind every connection. Manage members, review profiles, coordinate teams, and keep every Him Rishtey community running smoothly.</p>
            <div class="hero-actions"><a class="button button-primary" href="{{ route('admin.login') }}">Enter admin portal <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg></a><a class="button button-secondary" href="#portal-overview">What you can manage</a></div>
        </section>
        <section class="portal-card" id="portal-overview" aria-label="Admin portal preview">
            <div class="dashboard">
                <div class="dashboard-head"><span class="dashboard-title">Today at a glance</span><span class="live-pill">Systems ready</span></div>
                <div class="dashboard-body">
                    <div class="metric-grid">
                        <div class="metric"><span class="metric-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg></span><strong>Members</strong><small>Profiles &amp; activity</small></div>
                        <div class="metric"><span class="metric-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20.8 4.6a5.5 5.5 0 0 0-7.8 0L12 5.6l-1-1a5.5 5.5 0 0 0-7.8 7.8L12 21l8.8-8.6a5.5 5.5 0 0 0 0-7.8Z"/></svg></span><strong>Matches</strong><small>Interests &amp; stories</small></div>
                        <div class="metric"><span class="metric-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M8 17v-5M12 17V7M16 17v-3"/></svg></span><strong>Insights</strong><small>Sites &amp; performance</small></div>
                    </div>
                    <div class="activity">
                        <div class="activity-head"><span>Team workflow</span><span>Protected workspace</span></div>
                        <div class="activity-row"><span class="avatar">MR</span><span class="activity-copy">Relationship management<small>Support members with care</small></span><span class="status">Connected</span></div>
                        <div class="activity-row"><span class="avatar">CM</span><span class="activity-copy">Content moderation<small>Keep profiles trusted and current</small></span><span class="status">Protected</span></div>
                        <div class="activity-row"><span class="avatar">OP</span><span class="activity-copy">Site operations<small>Manage every community in one place</small></span><span class="status">Ready</span></div>
                    </div>
                </div>
            </div>
        </section>
    </main>
    <section class="trust-row" aria-label="Platform qualities">
        <div class="trust-item"><span class="trust-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="10" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg></span><span class="trust-copy"><strong>Role-based access</strong><span>Only the right tools for each team</span></span></div>
        <div class="trust-item"><span class="trust-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 3v18h18"/><path d="m7 16 4-5 4 3 5-7"/></svg></span><span class="trust-copy"><strong>Clear operations</strong><span>One view across your sites</span></span></div>
        <div class="trust-item"><span class="trust-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2 4 5v6c0 5.1 3.4 9.8 8 11 4.6-1.2 8-5.9 8-11V5l-8-3Z"/><path d="m9 12 2 2 4-4"/></svg></span><span class="trust-copy"><strong>Privacy by design</strong><span>Member information stays protected</span></span></div>
    </section>
    <footer class="footer"><span>&copy; {{ date('Y') }} {{ config('app.name', 'Him Rishtey') }}. Internal operations portal.</span><span><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="m9 12 2 2 4-4"/></svg>Authorised staff only</span></footer>
</div>
</body>
</html>
