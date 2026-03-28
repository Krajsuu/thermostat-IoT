<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thermio</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="text-white bg-[radial-gradient(circle_at_center,#0B142F_0%,#020617_80%)]">

    <div class="relative min-h-screen overflow-hidden">

        <div class="relative z-10">
            <x-navbar />
            
            @yield('content')
        </div>

        <div class="pointer-events-none absolute bottom-[-120px] left-1/2 -translate-x-1/2 z-0">
            <div class="h-[500px] w-[1200px] rounded-full 
            bg-blue-900/10 blur-[140px]"></div>
        </div>

    </div>

</body>
</html>