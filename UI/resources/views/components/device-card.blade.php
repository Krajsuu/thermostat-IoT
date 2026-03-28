@props([
    'name',
    'status',
    'temperature',
    'humidity',
    'mode',
    'heating',
])

@php
    $isOnline = strtolower($status) === 'online';
    $statusColor = $isOnline ? 'bg-[#46d11f]' : 'bg-[#e32626]';
@endphp

<div class="w-[350px] rounded-[22px] border border-white/8 bg-[linear-gradient(180deg,rgba(71,82,105,0.92)_0%,rgba(43,52,73,0.94)_100%)] px-6 py-5 shadow-[0_0_45px_rgba(40,110,255,0.28)]">
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <div class="h-4 w-4 rounded-full {{ $statusColor }}"></div>
            <span class="text-[22px] font-semibold text-white">{{ $name }}</span>
        </div>
        <span class="text-[20px] font-semibold text-white">{{ $status }}</span>
    </div>

    <div class="mt-5 h-px bg-white/12"></div>

    <div class="pt-10 text-center">
        <div class="text-[60px] font-bold tracking-wide text-white">{{ $temperature }}</div>
        <div class="mt-5 text-[24px] text-white">Wilgotność : {{ $humidity }}</div>
    </div>

    <div class="mt-10 h-px bg-white/12"></div>

    <div class="mt-4 flex items-center justify-between text-[18px] font-semibold">
        <div class="text-white">
            Tryb : <span class="text-[#00d1ff]">{{ $mode }}</span>
        </div>
        <div class="text-white">
            Grzanie : <span class="text-[#ffc400]">{{ $heating }}</span>
        </div>
    </div>
</div>