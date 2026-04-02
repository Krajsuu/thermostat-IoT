@extends('layouts.apphome')

@section('content')
    <section class="relative min-h-screen overflow-hidden bg-[#050816] text-white">
        <div class="pointer-events-none absolute inset-0">
            <div class="absolute left-[-120px] top-[140px] h-[280px] w-[280px] rounded-full bg-blue-500/10 blur-[120px]"></div>
            <div class="absolute right-[-100px] top-[180px] h-[320px] w-[320px] rounded-full bg-blue-400/10 blur-[140px]"></div>
            <div class="absolute left-1/2 top-[180px] h-[500px] w-[500px] -translate-x-1/2 rounded-full bg-blue-500/10 blur-[180px]"></div>
            <div class="absolute right-[18%] top-[320px] h-[320px] w-[320px] rounded-full bg-orange-500/10 blur-[150px]"></div>
            <div class="absolute bottom-[80px] right-[8%] h-[240px] w-[240px] rounded-full bg-orange-500/10 blur-[120px]"></div>

            <div class="absolute inset-0 bg-[radial-gradient(circle_at_center,rgba(37,99,235,0.08),transparent_55%)]"></div>
            <div class="absolute inset-0 opacity-[0.08] [background:linear-gradient(to_right,transparent,rgba(255,255,255,0.04),transparent)] bg-[length:600px_100%]"></div>
        </div>

        <div class="relative z-10 mx-auto flex w-full max-w-[1440px] flex-col items-center px-6 pb-16 pt-10 lg:px-14">

            <h1 class="max-w-[900px] text-center text-4xl font-semibold leading-tight text-white sm:text-5xl lg:text-7xl">
                Komfort w Twoich rękach
            </h1>

            <p class="mt-6 max-w-[850px] text-center text-base leading-8 text-slate-300 sm:text-xl">
                Zarządzaj temperaturą w swoim domu za pomocą jednej aplikacji
            </p>

            <div class="mt-16 grid w-full grid-cols-1 items-center gap-10 xl:grid-cols-[1fr_520px_1fr]">

                <x-status-card
                    align="xl:justify-end"
                    border="border-blue-400/20"
                    shadow="shadow-[0_0_40px_rgba(59,130,246,0.15)]"
                    iconBorder="border-blue-400/30"
                    iconBg="bg-blue-500/10"
                    iconColor="text-blue-400"
                    iconShadow="shadow-[0_0_25px_rgba(59,130,246,0.25)]"
                    label="Tryb pracy"
                    value="AUTO"
                    valueColor="text-white"
                    description="Optymalny komfort"
                    icon='
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M14 14.76V3.5a2 2 0 10-4 0v11.26a4 4 0 104 0z" />
                        </svg>
                    '
                />

                <div class="flex flex-col items-center">
                    <div class="relative flex h-[420px] w-[420px] items-center justify-center rounded-full">

                        <div class="absolute inset-0 rounded-full bg-[conic-gradient(from_180deg,rgba(59,130,246,1),rgba(59,130,246,0.1),rgba(249,115,22,0.1),rgba(249,115,22,1),rgba(59,130,246,1))] p-[3px] shadow-[0_0_80px_rgba(59,130,246,0.25)]">
                            <div class="h-full w-full rounded-full bg-[#081124]/90 backdrop-blur-xl"></div>
                        </div>

                        <div class="absolute left-0 top-1/2 h-4 w-4 -translate-y-1/2 rounded-full bg-blue-400 shadow-[0_0_25px_rgba(96,165,250,1)]"></div>
                        <div class="absolute right-0 top-1/2 h-4 w-4 -translate-y-1/2 rounded-full bg-orange-400 shadow-[0_0_25px_rgba(251,146,60,1)]"></div>

                        <div class="absolute h-[360px] w-[360px] rounded-full border border-white/5"></div>
                        <div class="absolute h-[300px] w-[300px] rounded-full border border-white/5"></div>

                        <div class="relative z-10 text-center">
                            <p class="text-2xl text-slate-300">Aktualna temperatura</p>
                            <div class="mt-4 text-7xl font-bold tracking-tight text-white lg:text-8xl">
                                18°C
                            </div>
                            <div class="mt-4 text-3xl text-slate-200">
                                Salon
                            </div>

                            <div class="mx-auto mt-8 inline-flex items-center gap-2 rounded-full border border-white/10 bg-white/5 px-5 py-3 text-xl text-slate-200 backdrop-blur-md">
                                Wilgotność: 48%
                            </div>
                        </div>
                    </div>

                    <a href="{{route("auth")}}"
                       class="mt-10 inline-flex items-center gap-4 rounded-full border border-blue-400/40 bg-blue-500/20 px-12 py-5 text-2xl font-semibold text-white shadow-[0_0_45px_rgba(59,130,246,0.30)] backdrop-blur-md transition hover:scale-[1.02] hover:bg-blue-500/30">
                        Zaloguj się
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14M13 5l7 7-7 7"/>
                        </svg>
                    </a>
                </div>

                <x-status-card
                    align="xl:justify-start"
                    border="border-orange-400/20"
                    shadow="shadow-[0_0_40px_rgba(249,115,22,0.13)]"
                    iconBorder="border-orange-400/30"
                    iconBg="bg-orange-500/10"
                    iconColor="text-orange-400"
                    iconShadow="shadow-[0_0_25px_rgba(249,115,22,0.30)]"
                    label="Ogrzewanie"
                    value="WŁĄCZONE"
                    valueColor="text-orange-400"
                    description="Pracuje efektywnie"
                    icon='
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12.74 2.27a1 1 0 00-1.48 0c-1.86 2.09-4.51 5.49-4.51 8.62a5.25 5.25 0 0010.5 0c0-3.13-2.65-6.53-4.51-8.62zM12 20a3.75 3.75 0 01-3.75-3.75c0-1.57 1.01-3.16 2.29-4.69a.5.5 0 01.86.32c0 2.06 1.68 3.74 3.74 3.74.34 0 .52.4.31.67A3.74 3.74 0 0112 20z"/>
                        </svg>
                    '
                />
            </div>

            <div class="mt-20 grid w-full grid-cols-1 gap-6 md:grid-cols-2 xl:grid-cols-4">

                <x-feature-card
                    title="Pełna kontrola"
                    description="Zarządzaj wszystkimi urządzeniami w jednym miejscu."
                    border="border-blue-400/15"
                    shadow="shadow-[0_0_30px_rgba(59,130,246,0.10)]"
                    iconBorder="border-blue-400/20"
                    iconBg="bg-blue-500/10"
                    iconColor="text-blue-400"
                    iconShadow="shadow-[0_0_25px_rgba(59,130,246,0.18)]"
                    icon='<svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="currentColor" viewBox="0 0 24 24"><path d="M12 3l9 8h-3v9h-5v-6H11v6H6v-9H3l9-8z"/></svg>'
                />

                <x-feature-card
                    title="Oszczędność energii"
                    description="Inteligentne algorytmy pomagają obniżyć zużycie energii."
                    border="border-emerald-400/15"
                    shadow="shadow-[0_0_30px_rgba(34,197,94,0.10)]"
                    iconBorder="border-emerald-400/20"
                    iconBg="bg-emerald-500/10"
                    iconColor="text-emerald-400"
                    iconShadow="shadow-[0_0_25px_rgba(34,197,94,0.18)]"
                    icon='<svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="currentColor" viewBox="0 0 24 24"><path d="M5 21c7 0 14-5 14-14-9 0-14 7-14 14zm2.5-4.5c2.5-.1 5.1-1.6 7-4.2-1.1 3.2-4 5.7-7 6.2v-2z"/></svg>'
                />

                <x-feature-card
                    title="Historia i analizy"
                    description="Sprawdź statystyki i analizuj swoje zużycie."
                    border="border-fuchsia-400/15"
                    shadow="shadow-[0_0_30px_rgba(217,70,239,0.10)]"
                    iconBorder="border-fuchsia-400/20"
                    iconBg="bg-fuchsia-500/10"
                    iconColor="text-fuchsia-400"
                    iconShadow="shadow-[0_0_25px_rgba(217,70,239,0.18)]"
                    icon='
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <circle cx="12" cy="12" r="9"></circle>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 7v5l3 2"></path>
                        </svg>
                    '
                />

                <x-feature-card
                    title="Ustawienia zaawansowane"
                    description="Dostosuj system do swoich potrzeb."
                    border="border-orange-400/15"
                    shadow="shadow-[0_0_30px_rgba(249,115,22,0.10)]"
                    iconBorder="border-orange-400/20"
                    iconBg="bg-orange-500/10"
                    iconColor="text-orange-400"
                    iconShadow="shadow-[0_0_25px_rgba(249,115,22,0.18)]"
                    icon='
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M19.14 12.94a7.49 7.49 0 000-1.88l2.03-1.58a.5.5 0 00.12-.64l-1.92-3.32a.5.5 0 00-.61-.22l-2.39.96a7.62 7.62 0 00-1.63-.95l-.36-2.54a.5.5 0 00-.5-.42h-3.84a.5.5 0 00-.5.42l-.36 2.54a7.62 7.62 0 00-1.63.95l-2.39-.96a.5.5 0 00-.61.22L2.68 8.84a.5.5 0 00.12.64l2.03 1.58a7.49 7.49 0 000 1.88L2.8 14.52a.5.5 0 00-.12.64l1.92 3.32a.5.5 0 00.61.22l2.39-.96c.5.39 1.05.71 1.63.95l.36 2.54a.5.5 0 00.5.42h3.84a.5.5 0 00.5-.42l.36-2.54c.58-.24 1.13-.56 1.63-.95l2.39.96a.5.5 0 00.61-.22l1.92-3.32a.5.5 0 00-.12-.64l-2.03-1.58zM12 15.5A3.5 3.5 0 1112 8a3.5 3.5 0 010 7.5z"/>
                        </svg>
                    '
                />

            </div>
    </section>
@endsection