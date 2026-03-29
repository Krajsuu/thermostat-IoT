@props([
    'historyPoints24h' => [],
    'historyPoints7d' => [],
    'historyPoints30d' => [],
])

@php
    $temps24 = array_column($historyPoints24h, 'temp');
    $temps7 = array_column($historyPoints7d, 'temp');
    $temps30 = array_column($historyPoints30d, 'temp');

    $min24 = count($temps24) ? number_format(min($temps24), 1) : '0.0';
    $max24 = count($temps24) ? number_format(max($temps24), 1) : '0.0';
    $avg24 = count($temps24) ? number_format(array_sum($temps24) / count($temps24), 1) : '0.0';

    $min7 = count($temps7) ? number_format(min($temps7), 1) : '0.0';
    $max7 = count($temps7) ? number_format(max($temps7), 1) : '0.0';
    $avg7 = count($temps7) ? number_format(array_sum($temps7) / count($temps7), 1) : '0.0';

    $min30 = count($temps30) ? number_format(min($temps30), 1) : '0.0';
    $max30 = count($temps30) ? number_format(max($temps30), 1) : '0.0';
    $avg30 = count($temps30) ? number_format(array_sum($temps30) / count($temps30), 1) : '0.0';
@endphp

<div
    x-show="historyOpen"
    x-transition
    x-cloak
    class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm px-4"
>
    <div
        @click.outside="historyOpen = false"
        class="relative w-full max-w-[980px] overflow-hidden rounded-[32px] border border-blue-400/10 bg-[#030B1D]/95 px-8 pb-7 pt-5 shadow-[0_0_80px_rgba(37,99,235,0.20)]"
    >
        <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_top,rgba(59,130,246,0.14),transparent_35%)]"></div>

        <div class="relative z-10">
            <div class="mx-auto mb-5 h-[7px] w-[70px] rounded-full bg-blue-400 shadow-[0_0_18px_rgba(59,130,246,0.7)]"></div>

            <button
                type="button"
                @click="historyOpen = false"
                class="absolute right-1 top-0 flex h-10 w-10 items-center justify-center rounded-full border border-white/10 bg-white/5 text-white/35 transition hover:bg-white/10 hover:text-white"
            >
                ✕
            </button>

            <h2 class="text-center text-3xl font-semibold text-white sm:text-4xl">
                Historia
            </h2>

            <div class="mt-8 flex justify-center">
                <div class="inline-flex rounded-full border border-blue-400/20 bg-blue-500/10 p-1 shadow-[0_0_20px_rgba(59,130,246,0.18)]">
                    <button
                        type="button"
                        @click="historyTab='24h'"
                        :class="tabClass('24h')"
                    >
                        24h
                    </button>

                    <button
                        type="button"
                        @click="historyTab='7d'"
                        :class="tabClass('7d')"
                    >
                        7 dni
                    </button>

                    <button
                        type="button"
                        @click="historyTab='30d'"
                        :class="tabClass('30d')"
                    >
                        Miesiąc
                    </button>
                </div>
            </div>

            <div class="mt-6 rounded-[24px] bg-[#061225]/65 px-4 py-4">
                <div x-show="historyTab === '24h'" x-cloak>
                    <x-history :points="$historyPoints24h" :height="160" />
                </div>

                <div x-show="historyTab === '7d'" x-cloak>
                    <x-history :points="$historyPoints7d" :height="160" />
                </div>

                <div x-show="historyTab === '30d'" x-cloak>
                    <x-history :points="$historyPoints30d" :height="160" />
                </div>
            </div>

            <div class="mt-10 rounded-[24px] border border-white/10 bg-[#0A1B38]/85 px-6 py-4 shadow-[inset_0_1px_0_rgba(255,255,255,0.04)]">
                <div
                    x-show="historyTab === '24h'"
                    x-cloak
                    class="grid grid-cols-3 items-center text-center"
                >
                    <div>
                        <p class="text-2xl font-semibold text-white/55">Min</p>
                        <p class="mt-2 text-4xl font-semibold text-white">{{ $min24 }}°C</p>
                    </div>

                    <div class="border-x border-white/10">
                        <p class="text-2xl font-semibold text-white/55">Max</p>
                        <p class="mt-2 text-4xl font-semibold text-white">{{ $max24 }}°C</p>
                    </div>

                    <div>
                        <p class="text-2xl font-semibold text-white/55">AVG</p>
                        <p class="mt-2 text-4xl font-semibold text-white">{{ $avg24 }}°C</p>
                    </div>
                </div>

                <div
                    x-show="historyTab === '7d'"
                    x-cloak
                    class="grid grid-cols-3 items-center text-center"
                >
                    <div>
                        <p class="text-2xl font-semibold text-white/55">Min</p>
                        <p class="mt-2 text-4xl font-semibold text-white">{{ $min7 }}°C</p>
                    </div>

                    <div class="border-x border-white/10">
                        <p class="text-2xl font-semibold text-white/55">Max</p>
                        <p class="mt-2 text-4xl font-semibold text-white">{{ $max7 }}°C</p>
                    </div>

                    <div>
                        <p class="text-2xl font-semibold text-white/55">AVG</p>
                        <p class="mt-2 text-4xl font-semibold text-white">{{ $avg7 }}°C</p>
                    </div>
                </div>

                <div
                    x-show="historyTab === '30d'"
                    x-cloak
                    class="grid grid-cols-3 items-center text-center"
                >
                    <div>
                        <p class="text-2xl font-semibold text-white/55">Min</p>
                        <p class="mt-2 text-4xl font-semibold text-white">{{ $min30 }}°C</p>
                    </div>

                    <div class="border-x border-white/10">
                        <p class="text-2xl font-semibold text-white/55">Max</p>
                        <p class="mt-2 text-4xl font-semibold text-white">{{ $max30 }}°C</p>
                    </div>

                    <div>
                        <p class="text-2xl font-semibold text-white/55">AVG</p>
                        <p class="mt-2 text-4xl font-semibold text-white">{{ $avg30 }}°C</p>
                    </div>
                </div>
            </div>

            <p class="mt-6 text-center text-2xl font-medium text-white/65">
                Ostatnia aktualizacja: 11:45
            </p>
        </div>
    </div>
</div>