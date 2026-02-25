<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Akun Terblokir — AMI</title>
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
                radial-gradient(ellipse at 80% 70%, rgba(239, 68, 68, 0.15) 0%, transparent 60%);
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
            background: rgba(15, 15, 26, 0.6);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(239, 68, 68, 0.2);
            border-radius: 24px;
            box-shadow: 0 25px 50px -12px rgba(239, 68, 68, 0.15);
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

        .icon-wrapper {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 80px;
            height: 80px;
            border-radius: 20px;
            background: rgba(239, 68, 68, 0.1);
            color: #ef4444;
            margin-bottom: 2rem;
            box-shadow: 0 0 40px rgba(239, 68, 68, 0.3), inset 0 0 20px rgba(239, 68, 68, 0.2);
            border: 1px solid rgba(239, 68, 68, 0.3);
            animation: pulse-shadow 2s ease-in-out infinite;
        }

        @keyframes pulse-shadow {

            0%,
            100% {
                box-shadow: 0 0 20px rgba(239, 68, 68, 0.2), inset 0 0 10px rgba(239, 68, 68, 0.1);
            }

            50% {
                box-shadow: 0 0 50px rgba(239, 68, 68, 0.5), inset 0 0 25px rgba(239, 68, 68, 0.3);
            }
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
            background: linear-gradient(90deg, #f87171, #ef4444);
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        p {
            color: #9ca3af;
            font-size: 0.95rem;
            line-height: 1.6;
            margin-bottom: 2rem;
        }

        strong {
            color: #f3f4f6;
            font-weight: 700;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            background: #ef4444;
            color: #ffffff;
            font-weight: 600;
            font-size: 0.875rem;
            padding: 0.8rem 1.75rem;
            border-radius: 0.75rem;
            text-decoration: none;
            transition: all 0.2s;
            box-shadow: 0 4px 14px 0 rgba(239, 68, 68, 0.39);
        }

        .btn:hover {
            background: #dc2626;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(239, 68, 68, 0.4);
        }

        .countdown {
            font-size: 0.8rem;
            color: #6b7280;
            margin-top: 1.5rem;
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
        <div class="icon-wrapper">
            <svg width="36" height="36" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
            </svg>
        </div>

        <div class="badge">Akses Ditolak</div>

        <h1>Akun Anda <br><span>Telah Diblokir</span></h1>

        <p>
            Demi keamanan, sistem telah mengunci akun Anda <strong>karena Anda melebihi batas 3 kali percobaan login</strong> yang gagal pada bulan ini.<br><br>
            Silakan hubungi administrator sistem untuk meminta verifikasi dan membuka kembali akses akun Anda.
        </p>

        <a href="{{ route('login') }}" class="btn">
            Kembali ke Halaman Login
        </a>
    </div>

</body>

</html>