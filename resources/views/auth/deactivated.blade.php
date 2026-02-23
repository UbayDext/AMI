<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sesi Berakhir — AMI</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600,700,800&display=swap" rel="stylesheet">
    <style>
        *,
        *::before,
        *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #0f0f1a;
            font-family: 'Figtree', sans-serif;
            overflow: hidden;
        }

        /* Animated starfield background */
        .stars {
            position: fixed;
            inset: 0;
            background: radial-gradient(ellipse at 20% 30%, rgba(99, 102, 241, 0.15) 0%, transparent 60%),
                radial-gradient(ellipse at 80% 70%, rgba(239, 68, 68, 0.12) 0%, transparent 60%);
            z-index: 0;
        }

        .stars::before {
            content: '';
            position: absolute;
            inset: 0;
            background-image:
                radial-gradient(circle, rgba(255, 255, 255, 0.7) 1px, transparent 1px),
                radial-gradient(circle, rgba(255, 255, 255, 0.5) 1px, transparent 1px),
                radial-gradient(circle, rgba(255, 255, 255, 0.3) 1px, transparent 1px);
            background-size: 400px 400px, 250px 250px, 150px 150px;
            background-position: 0 0, 100px 50px, 50px 120px;
            animation: twinkle 6s ease-in-out infinite alternate;
        }

        @keyframes twinkle {
            from {
                opacity: 0.5;
            }

            to {
                opacity: 1;
            }
        }

        .card {
            position: relative;
            z-index: 1;
            text-align: center;
            padding: 3rem 2.5rem;
            max-width: 480px;
            width: 100%;
            animation: fadeInUp 0.6s ease-out;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(24px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .gif-wrapper {
            display: inline-block;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 0 60px rgba(239, 68, 68, 0.4), 0 0 120px rgba(239, 68, 68, 0.15);
            border: 2px solid rgba(239, 68, 68, 0.4);
            margin-bottom: 2rem;
            animation: pulse-shadow 2s ease-in-out infinite;
        }

        @keyframes pulse-shadow {

            0%,
            100% {
                box-shadow: 0 0 40px rgba(239, 68, 68, 0.4), 0 0 80px rgba(239, 68, 68, 0.15);
            }

            50% {
                box-shadow: 0 0 70px rgba(239, 68, 68, 0.7), 0 0 140px rgba(239, 68, 68, 0.3);
            }
        }

        .gif-wrapper img {
            display: block;
            width: 280px;
            height: 200px;
            object-fit: cover;
        }

        .badge {
            display: inline-block;
            background: rgba(239, 68, 68, 0.15);
            border: 1px solid rgba(239, 68, 68, 0.4);
            color: #f87171;
            font-size: 0.7rem;
            font-weight: 700;
            letter-spacing: 0.15em;
            text-transform: uppercase;
            padding: 0.3rem 0.9rem;
            border-radius: 9999px;
            margin-bottom: 1.2rem;
        }

        h1 {
            font-size: 2rem;
            font-weight: 800;
            color: #ffffff;
            line-height: 1.2;
            margin-bottom: 0.75rem;
        }

        h1 span {
            background: linear-gradient(90deg, #f87171, #fbbf24);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        p {
            color: #9ca3af;
            font-size: 0.95rem;
            line-height: 1.6;
            margin-bottom: 2rem;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: white;
            color: #111827;
            font-weight: 600;
            font-size: 0.875rem;
            padding: 0.7rem 1.5rem;
            border-radius: 0.75rem;
            text-decoration: none;
            transition: opacity 0.15s;
        }

        .btn:hover {
            opacity: 0.85;
        }

        .countdown {
            font-size: 0.8rem;
            color: #6b7280;
            margin-top: 1rem;
        }

        .countdown span {
            color: #f87171;
            font-weight: 600;
        }
    </style>
</head>

<body>
    <div class="stars"></div>

    <div class="card">

        <div class="badge">🔒 Sesi Dihentikan</div>

        <h1>Time <span>Observasi</span><br>is Over.</h1>

        <p>
            Akun Anda telah dinonaktifkan oleh administrator.<br>
            Silakan hubungi admin untuk informasi lebih lanjut.
        </p>

        <a href="{{ route('login') }}" class="btn">
            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
            </svg>
            Kembali ke Login
        </a>

        <p class="countdown">
            Redirect otomatis dalam <span id="sec">30</span> detik…
        </p>
    </div>

    <script>
        let s = 30;
        const el = document.getElementById('sec');
        const t = setInterval(() => {
            s--;
            el.textContent = s;
            if (s <= 0) {
                clearInterval(t);
                window.location.href = "{{ route('login') }}";
            }
        }, 1000);
    </script>
</body>

</html>