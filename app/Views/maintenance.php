<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembaruan Sistem | SINDEN</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; }

        html, body {
            height: 100vh;
            margin: 0;
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #0f2027, #203a43, #2c5364);
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            padding: 3rem;
            border-radius: 25px;
            text-align: center;
            max-width: 600px;
            width: 90%;
            z-index: 10;
            box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.37);
        }

        .icon-box {
            font-size: 4rem;
            margin-bottom: 1.5rem;
            color: #4facfe;
        }

        h1 { font-weight: 600; margin-bottom: 1rem; }
        p { font-weight: 300; font-size: 1.1rem; line-height: 1.6; color: rgba(255, 255, 255, 0.9); }

        .btn-action {
            display: inline-block;
            margin-top: 1.5rem;
            padding: 0.8rem 2rem;
            background: #4facfe;
            color: white;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .btn-action:hover { background: #00f2fe; transform: scale(1.05); }

        .footer-socials {
            margin-top: 2.5rem;
            display: flex;
            justify-content: center;
            gap: 15px;
        }

        .footer-socials a {
            color: rgba(255, 255, 255, 0.6);
            font-size: 1.2rem;
            transition: color 0.3s;
        }
        .footer-socials a:hover { color: #fff; }

        /* Wave Animation */
        .wave { opacity: .4; position: absolute; bottom: -20%; left: 50%; width: 6000px; height: 6000px; background: rgba(0,0,0,0.2); transform-origin: 50% 48%; border-radius: 46%; animation: wave 15s infinite linear; pointer-events: none; }
        .wave2 { animation: wave 25s infinite linear; opacity: .2; }
        @keyframes wave { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
    </style>
    <link rel="stylesheet" href="<?= base_url('/assets/plugins/fontawesome-free/css/all.min.css'); ?>">
</head>

<body>
    <div class="glass-card">
        <div class="icon-box"><i class="fas fa-tools"></i></div>
        <h1>Sedang Kami Tingkatkan</h1>
        <p>Sistem saat ini sedang dalam proses pembaruan untuk meningkatkan pengalaman Anda. Kami berjanji akan segera kembali dengan fitur yang lebih baik.</p>
        
        <div class="mt-3">
            <a href="javascript:history.back()" class="btn-action">Kembali</a>
            <a href="<?= base_url(); ?>" style="margin-left: 10px; color: #4facfe;">Ke Dashboard</a>
        </div>

        <div class="footer-socials">
            <a href='https://fb.com/sutarsarian' target='_blank'><i class='fab fa-facebook-f'></i></a>
            <a href='https://twitter.com/riansutarsa' target='_blank'><i class='fab fa-twitter'></i></a>
            <a href='https://github.com/rsoeta' target='_blank'><i class='fab fa-github'></i></a>
            <a href='https://www.instagram.com/sutarsarian' target='_blank'><i class='fab fa-instagram'></i></a>
        </div>
    </div>

    <div class='wave'></div>
    <div class='wave wave2'></div>
</body>
</html>