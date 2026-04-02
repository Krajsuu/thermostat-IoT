<nav class="flex h-[78px] items-center justify-between px-8 border-b border-white/10 
    bg-gradient-to-b from-[#0F172A] to-[#131B38] backdrop-blur-sm">

        <div class="flex items-center">
            <img src="{{ asset('images/logo.png') }}" 
                class="h-[38px] w-auto object-contain drop-shadow-[0_0_6px_rgba(255,140,47,0.6)]"
                alt="Thermio logo">
        </div>

        <div class="flex items-center gap-6">

           <a href="{{ route('home') }}"
            class="inline-flex items-center justify-center rounded-full border border-blue-400/30 bg-blue-500/10 px-6 py-3 text-sm font-medium text-blue-100 shadow-[0_0_30px_rgba(59,130,246,0.18)] backdrop-blur-md transition hover:border-blue-300/50 hover:bg-blue-500/20">
                Strona Główna
            </a>

            <a href="{{ route('auth') }}"
            class="inline-flex items-center justify-center rounded-full border border-blue-400/40 bg-blue-500/15 px-6 py-3 text-sm font-medium text-white shadow-[0_0_35px_rgba(59,130,246,0.25)] backdrop-blur-md transition hover:scale-[1.02] hover:bg-blue-500/25">
                Zaloguj się / Zarejestruj
            </a>

        </div>
</nav>