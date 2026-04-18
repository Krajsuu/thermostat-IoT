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

            <div class="mt-10 rounded-[24px] bg-[#061225]/65 px-3 py-4 sm:px-4">
                <div x-show="historyTab === '24h'" x-cloak>
                    <div x-html="chartSvg(historyPoints24h, 160, 'm24')"></div>
                </div>

                <div x-show="historyTab === '7d'" x-cloak>
                    <div x-html="chartSvg(historyPoints7d, 160, 'm7')"></div>
                </div>

                <div x-show="historyTab === '30d'" x-cloak>
                    <div x-html="chartSvg(historyPoints30d, 160, 'm30')"></div>
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
                        <p class="mt-2 text-4xl font-semibold text-white" x-text="statMin(historyPoints24h)"></p>
                    </div>

                    <div class="border-x border-white/10">
                        <p class="text-2xl font-semibold text-white/55">Max</p>
                        <p class="mt-2 text-4xl font-semibold text-white" x-text="statMax(historyPoints24h)"></p>
                    </div>

                    <div>
                        <p class="text-2xl font-semibold text-white/55">AVG</p>
                        <p class="mt-2 text-4xl font-semibold text-white" x-text="statAvg(historyPoints24h)"></p>
                    </div>
                </div>

                <div
                    x-show="historyTab === '7d'"
                    x-cloak
                    class="grid grid-cols-3 items-center text-center"
                >
                    <div>
                        <p class="text-2xl font-semibold text-white/55">Min</p>
                        <p class="mt-2 text-4xl font-semibold text-white" x-text="statMin(historyPoints7d)"></p>
                    </div>

                    <div class="border-x border-white/10">
                        <p class="text-2xl font-semibold text-white/55">Max</p>
                        <p class="mt-2 text-4xl font-semibold text-white" x-text="statMax(historyPoints7d)"></p>
                    </div>

                    <div>
                        <p class="text-2xl font-semibold text-white/55">AVG</p>
                        <p class="mt-2 text-4xl font-semibold text-white" x-text="statAvg(historyPoints7d)"></p>
                    </div>
                </div>

                <div
                    x-show="historyTab === '30d'"
                    x-cloak
                    class="grid grid-cols-3 items-center text-center"
                >
                    <div>
                        <p class="text-2xl font-semibold text-white/55">Min</p>
                        <p class="mt-2 text-4xl font-semibold text-white" x-text="statMin(historyPoints30d)"></p>
                    </div>

                    <div class="border-x border-white/10">
                        <p class="text-2xl font-semibold text-white/55">Max</p>
                        <p class="mt-2 text-4xl font-semibold text-white" x-text="statMax(historyPoints30d)"></p>
                    </div>

                    <div>
                        <p class="text-2xl font-semibold text-white/55">AVG</p>
                        <p class="mt-2 text-4xl font-semibold text-white" x-text="statAvg(historyPoints30d)"></p>
                    </div>
                </div>
            </div>

            <p class="mt-6 text-center text-2xl font-medium text-white/65">
                Ostatnia aktualizacja: <span x-text="historyLastUpdated || '—'"></span>
            </p>
        </div>
    </div>
</div>
