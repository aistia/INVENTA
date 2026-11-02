
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'INVENTA') — Lab RPL</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/inventa-icon-v3.svg') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">

    <script src="https://cdn.tailwindcss.com"></script>
    <script id="tailwind-config">
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        display: ['"Plus Jakarta Sans"', 'sans-serif'],
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                    },
                }
            }
        }
    </script>

    <style>
        * {
            box-sizing: border-box;
        }

        html,
        body {
            width: 100%;
            height: 100%;
            margin: 0;
            overflow: hidden;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        ::-webkit-scrollbar {
            display: none;
        }

        .material-symbols-outlined {
            font-variation-settings:
                'FILL' 0,
                'wght' 400,
                'GRAD' 0,
                'opsz' 24;
        }

        @media (max-width: 480px) {
            form .grid.grid-cols-2 {
                grid-template-columns: minmax(0, 1fr);
            }

            form .grid.grid-cols-2 > .col-span-2 {
                grid-column: span 1 / span 1;
            }
        }
    </style>
</head>

<body class="bg-[#f7f6fb] text-slate-900 antialiased">

    <main class="w-full h-[100svh] flex items-center justify-center p-3 sm:p-4 overflow-hidden">

        <div class="w-full h-full max-w-6xl mx-auto relative flex items-center justify-center">

            <div class="absolute -top-24 -left-20 w-96 h-96 rounded-full bg-violet-200/40 blur-3xl pointer-events-none -z-10"></div>

            <div class="absolute -bottom-24 -right-16 w-96 h-96 rounded-full bg-indigo-200/40 blur-3xl pointer-events-none -z-10"></div>

            @yield('content')

        </div>

    </main>

</body>
</html>
