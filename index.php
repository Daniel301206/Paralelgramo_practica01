<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#edf4f8">
    <meta name="description" content="Bienvenido a ProyectParalelogramo.">
    <title>Bienvenido | ProyectParalelogramo</title>
    <style>
        :root {
            color-scheme: light;
            font-family: Inter, "Segoe UI", Arial, sans-serif;
            color: #18334b;
            background: #edf4f8;
            font-synthesis: none;
            text-rendering: optimizeLegibility;
        }

        * {
            box-sizing: border-box;
        }

        body {
            min-height: 100vh;
            min-height: 100svh;
            margin: 0;
            display: grid;
            place-items: center;
            padding: 28px 16px;
            background:
                radial-gradient(circle at 15% 15%, #d5eaff 0, transparent 32%),
                radial-gradient(circle at 90% 85%, #d8f0ec 0, transparent 30%),
                #edf4f8;
        }

        .welcome-card {
            width: min(100%, 520px);
            padding: clamp(28px, 7vw, 46px);
            border: 1px solid #dce7ef;
            border-radius: 22px;
            background: #fff;
            box-shadow: 0 20px 55px #22415b18;
            text-align: center;
            animation: card-arrive 650ms cubic-bezier(.2, .75, .25, 1) both;
            transition: opacity 350ms ease, transform 350ms ease;
        }

        .welcome-illustration {
            display: block;
            width: 132px;
            height: 108px;
            margin: 0 auto 22px;
            object-fit: contain;
            animation: illustration-arrive 800ms 120ms both;
        }

        .eyebrow {
            margin: 0 0 9px;
            color: #177e89;
            font-size: 0.78rem;
            font-weight: 800;
            letter-spacing: 0.12em;
            text-transform: uppercase;
        }

        h1 {
            margin: 0;
            font-size: clamp(1.8rem, 7vw, 2.55rem);
            letter-spacing: -0.035em;
            line-height: 1.16;
        }

        .welcome-copy {
            margin: 14px 0 27px;
            color: #64788a;
            font-size: clamp(1rem, 3.5vw, 1.1rem);
            line-height: 1.55;
        }

        .loading {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            color: #536b7d;
            font-size: 0.92rem;
            font-weight: 650;
        }

        .spinner {
            width: 21px;
            height: 21px;
            flex: 0 0 auto;
            border: 3px solid #dcebef;
            border-top-color: #167e8a;
            border-radius: 50%;
            animation: spin 850ms linear infinite;
        }

        .progress {
            height: 5px;
            margin-top: 22px;
            overflow: hidden;
            border-radius: 99px;
            background: #edf4f8;
        }

        .progress-bar {
            width: 100%;
            height: 100%;
            border-radius: inherit;
            background: #167e8a;
            transform: scaleX(0);
            transform-origin: left;
            animation: progress 3s linear forwards;
        }

        body.is-leaving .welcome-card {
            opacity: 0;
            transform: translateY(-10px) scale(.99);
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        @keyframes progress {
            to { transform: scaleX(1); }
        }

        @keyframes card-arrive {
            from { opacity: 0; transform: translateY(14px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes illustration-arrive {
            from { opacity: 0; transform: scale(.88); }
            to { opacity: 1; transform: scale(1); }
        }

        @media (max-width: 480px) {
            body {
                padding: 20px 16px;
            }

            .welcome-card {
                border-radius: 18px;
            }
        }

        @media (prefers-reduced-motion: reduce) {
            *,
            *::before,
            *::after {
                animation-duration: 0.01ms !important;
                animation-iteration-count: 1 !important;
                scroll-behavior: auto !important;
                transition-duration: 0.01ms !important;
            }
        }
    </style>
</head>
<body>
    <main class="welcome-card" aria-labelledby="welcome-title">
        <img class="welcome-illustration"
             src="/Imagenes/Paralelogramo%20azul%20con%20%C3%A1ngulos%20marcados.png"
             alt="Ilustración de un paralelogramo">
        <p class="eyebrow">Geometría fácil</p>
        <h1 id="welcome-title">Bienvenido a ProyectParalelogramo</h1>
        <p class="welcome-copy">Calcula y guarda tus resultados en un solo lugar.</p>
        <div class="loading" role="status" aria-live="polite">
            <span class="spinner" aria-hidden="true"></span>
            <span>Preparando el sistema…</span>
        </div>
        <div class="progress" aria-hidden="true">
            <div class="progress-bar"></div>
        </div>
    </main>
    <script>
        const navigationEntry = performance.getEntriesByType('navigation')[0];
        const progressBar = document.querySelector('.progress-bar');
        let transitionTimer;
        let redirectTimer;

        function startLoginRedirect(replaceHistoryEntry) {
            window.clearTimeout(transitionTimer);
            window.clearTimeout(redirectTimer);
            document.body.classList.remove('is-leaving');

            progressBar.style.animation = 'none';
            progressBar.offsetHeight;
            progressBar.style.animation = '';

            transitionTimer = window.setTimeout(function () {
                document.body.classList.add('is-leaving');
            }, 2600);

            redirectTimer = window.setTimeout(function () {
                if (replaceHistoryEntry) {
                    window.location.replace('/views/login.php');
                    return;
                }

                window.location.href = '/views/login.php';
            }, 3000);
        }

        startLoginRedirect(navigationEntry && navigationEntry.type === 'back_forward');

        window.addEventListener('pageshow', function (event) {
            if (event.persisted) {
                startLoginRedirect(true);
            }
        });
    </script>
</body>
</html>
