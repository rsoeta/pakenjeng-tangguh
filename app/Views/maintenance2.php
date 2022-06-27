<!doctype html>
<title>Opr NewDTKS | Site Maintenance</title>
<style>
    @font-face {
        font-family: 'Work Sans';
        font-style: normal;
        font-weight: 300;
        src: local('Work Sans Light'), local('WorkSans-Light'), url(https://fonts.gstatic.com/s/worksans/v2/FD_Udbezj8EHXbdsqLUpl3hCUOGz7vYGh680lGh-uXM.woff) format('woff')
    }

    *,
    *::before,
    *::after {
        box-sizing: border-box;
    }

    html,
    body {
        font-size: 12px;
        overflow: hidden;
        text-align: center;
        font-family: 'Work Sans', sans-serif;
        line-height: 1.4;
        overflow: hidden;
        width: 100%;
    }

    .under,
    html,
    body {
        height: 100vh;
    }

    @keyframes charge {
        from {
            transform: translateY(2rem);
            opacity: 0
        }

        to {
            transform: translateY(0);
            opacity: 1
        }
    }

    @keyframes wave {
        from {
            transform: rotate(0deg);
        }

        to {
            transform: rotate(360deg);
        }
    }

    .under__content {
        color: #fff;
        font-weight: 300;
        padding: 0 2rem
    }

    .under__content,
    .under__footer {
        width: 100%;
        position: relative;
        z-index: 100;
    }

    .under,
    .under__content,
    .under__footer {
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .under,
    .under__content {
        flex-direction: column;
    }

    .under__footer,
    .under__text,
    .under__title {
        animation: charge .5s both;
    }

    .under__footer {
        flex-wrap: wrap;
        max-width: 600px;
        opacity: 0;
        animation-delay: .5s;
    }

    .under__subtitle,
    .under__title {
        margin: 0;
    }

    .under__footer a {
        font-size: 20px;
        color: #fff;
        padding: 14px;
        background-color: rgba(0, 0, 0, .5);
        margin: 2px;
        border-radius: 3px;
        width: 51px;
        transition: background .3s;
    }

    .under__footer a:active,
    .under__footer a:focus,
    .under__footer a:hover {
        text-decoration: none
    }

    .under__footer a:hover {
        background-color: rgba(0, 0, 0, .9)
    }

    .under__subtitle,
    .under__text,
    .under__title {
        backface-visibility: hidden
    }

    .under__title {
        font-size: 2.4rem;
        font-weight: 300;
    }

    .under__text {
        max-width: 50rem;
        font-weight: 300;
        padding: 2rem 0;
        font-size: 1.3rem;
        color: rgba(255, 255, 255, .8);
        animation-delay: .3s;
    }

    a {
        color: #fff;
        text-decoration: none;
    }

    @media (min-width: 768px) {
        html {
            font-size: 14px;
        }

        .under__title {
            font-size: 3.4rem;
        }

        .under__text {
            font-size: 1.5rem;
        }
    }

    /*
=> Wave: https://codepen.io/zkreations/pen/VGWzYv
*/
    .wave {
        opacity: .6;
        position: absolute;
        bottom: 40%;
        left: 50%;
        width: 6000px;
        height: 6000px;
        background: #000;
        margin-left: -3000px;
        transform-origin: 50% 48%;
        border-radius: 46%;
        animation: wave 12s infinite linear;
        pointer-events: none;
    }

    .wave2 {
        animation: wave 28s infinite linear;
        opacity: .3;
    }

    .wave3 {
        animation: wave 20s infinite linear;
        opacity: .1;
    }

    /*
=> Personalizar
*/
    /* Wave
--------------------------------------------*/
    .wave {
        background: #000;
        /*color de fondo*/
    }

    /* Under
--------------------------------------------*/
    .under {
        background-color: #061c2d;
    }
</style>
<link rel="stylesheet" href="<?= base_url(); ?>/assets/plugins/fontawesome-free/css/all.min.css">


<div class='under'>
    <header class='under__content'>
        <h1 class='under__title'>Maintenance :(</h1>
        <div class='under__text'>Mohon maaf atas ketidaknyamanan Anda, <br>Menu / Halaman ini tidak tersedia / sedang dalam tahap perbaikan.</div>
        <div class='under__text'>Silakan <a href="<?= base_url(); ?>">Kembali</a> ke <a style="color: white;" href="<?= base_url(); ?>">Dashboard</a></div>
        <!-- , silahkan ikuti sosial media kami untuk tetap mendapatkan informasi terkini. -->
    </header>
    <footer class='under__footer'>
        <a href='https://fb.com/sutarsarian' target='_blank'>
            <i class='fab fa-facebook-f'></i>
        </a>
        <a href='https://twitter.com/riansutarsa' target='_blank'>
            <i class='fab fa-twitter'></i>
        </a>
        <a href='https://github.com/rsoeta' target='_blank'>
            <i class='fab fa-github'></i>
        </a>
        <a href='https://www.instagram.com/sutarsarian' target='_blank'>
            <i class='fab fa-instagram'></i>
        </a>
        <a href='https://youtube.com/' target='_blank'>
            <i class='fab fa-youtube'></i>
        </a>
    </footer>
    <div class='wave'></div>
    <div class='wave wave2'></div>
    <div class='wave wave3'></div>
</div>