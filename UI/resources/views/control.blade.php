@extends('layouts.app')

@section('content')

<section class="relative min-h-screen overflow-hidden bg-[#050816] text-white">
    <div class="pointer-events-none absolute inset-0">
        <div class="absolute left-[-120px] top-[120px] h-[260px] w-[260px] rounded-full bg-blue-500/10 blur-[120px]"></div>
        <div class="absolute right-[-100px] top-[180px] h-[300px] w-[300px] rounded-full bg-blue-400/10 blur-[140px]"></div>
        <div class="absolute left-1/2 top-[220px] h-[420px] w-[420px] -translate-x-1/2 rounded-full bg-blue-500/10 blur-[170px]"></div>
        <div class="absolute bottom-[60px] right-[10%] h-[240px] w-[240px] rounded-full bg-orange-500/10 blur-[130px]"></div>

        <div class="absolute inset-0 bg-[radial-gradient(circle_at_center,rgba(37,99,235,0.08),transparent_55%)]"></div>
        <div class="absolute inset-0 opacity-[0.08] [background:linear-gradient(to_right,transparent,rgba(255,255,255,0.04),transparent)] bg-[length:600px_100%]"></div>
    </div>

    <div class="relative z-10">
        <div class="mx-auto flex min-h-[calc(100vh-73px)] max-w-[1440px] items-start justify-center px-4 pt-10 pb-10 sm:px-6 lg:px-10">
            <div class="relative w-full max-w-[520px]">
                <div class="absolute left-1/2 top-[20%] h-[420px] w-[420px] -translate-x-1/2 rounded-full bg-blue-500/20 blur-[120px]"></div>

                @php
                    $sliderInit = 21.0;
                    if (!empty($room['is_online'])) {
                        $t = isset($temperature) ? (float) $temperature : 21.0;
                        if ($t <= 0) {
                            $t = 21.0;
                        }
                        $sliderInit = max(10.0, min(30.0, $t));
                    }
                @endphp
                <div
                    id="control-panel-root"
                    x-data="{
                        temperature: {{ number_format($sliderInit, 1, '.', '') }},
                        activeMode: '{{ $room['state_name'] ?? 'auto' }}',
                        deviceUid: '{{ $room['device_uid'] }}',
                        historyOpen: false,
                        historyTab: '24h',
                        commandCooldownUntil: 0,

                        thumbLeftPct() {
                            const t = Math.min(30, Math.max(10, Number(this.temperature) || 21));
                            return ((t - 10) / 20) * 100;
                        },

                        async sendCommand(mode, targetTemp = null) {
                            const modeMap = { 'heating': 1, 'cooling': 2, 'auto': 3, 'monitor': 4 };
                            const state = modeMap[mode];
                            if (state === undefined) {
                                return;
                            }
                            const rawTarget = targetTemp ?? this.temperature;
                            const target = Math.min(30, Math.max(10, Number(rawTarget) || 21));
                            const payload = {
                                device_uid: this.deviceUid,
                                state,
                                target
                            };

                            try {
                                const response = await fetch('{{ route('device.command') }}', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                        'Accept': 'application/json'
                                    },
                                    body: JSON.stringify(payload)
                                });
                                const body = await response.json().catch(() => ({}));
                                if (!response.ok) {
                                    console.error('Sterowanie:', response.status, body);
                                    return;
                                }
                                this.commandCooldownUntil = Date.now() + 3000;
                                console.log('MQTT Status:', body.status ?? body);
                            } catch (error) {
                                console.error('Błąd sterowania:', error);
                            }
                        },

                        setMode(mode) {
                            if (this.activeMode === mode) {
                                this.activeMode = 'monitor';
                            } else {
                                this.activeMode = mode;
                            }
                            this.sendCommand(this.activeMode);
                        },

                        isAuto() {
                            return this.activeMode === 'auto';
                        },
                        tabClass(tab) {
                            return this.historyTab === tab
                                ? 'rounded-full bg-blue-500/15 px-6 py-2 text-2xl font-medium text-blue-300 shadow-[0_0_20px_rgba(59,130,246,0.18)] transition'
                                : 'rounded-full px-6 py-2 text-2xl font-medium text-blue-200/80 transition hover:text-blue-200'
                        }
                    }"
                    class="relative text-center"
                >
                    <div class="relative mb-2 w-fit mx-auto">
                        <button onclick="window.location.href='{{ route('dashboard') }}'" class="absolute -left-12 top-1/2 -translate-y-1/2 flex h-8 w-8 items-center justify-center rounded-full border border-white/10 bg-white/5 backdrop-blur-md text-white/70 transition hover:bg-white/10 hover:text-white hover:shadow-[0_0_15px_rgba(59,130,246,0.4)]">
                            <svg width="25" height="25" viewBox="0 0 25 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M12.5 21C7.8 21 4 17.2 4 12.5C4 7.8 7.8 4 12.5 4C17.2 4 21 7.8 21 12.5C21 17.2 17.2 21 12.5 21ZM12.5 5C8.35 5 5 8.35 5 12.5C5 16.65 8.35 20 12.5 20C16.65 20 20 16.65 20 12.5C20 8.35 16.65 5 12.5 5Z" fill="#E5E7EB"/>
                                <path d="M12.65 17.35L7.80005 12.5L12.65 7.64999L13.35 8.34999L9.20005 12.5L13.35 16.65L12.65 17.35Z" fill="#E5E7EB"/>
                                <path d="M8.5 12H17V13H8.5V12Z" fill="#E5E7EB"/>
                            </svg>
                        </button>

                        <h1 class="text-center text-3xl font-semibold tracking-wide sm:text-4xl">
                            {{ $room['name'] }}
                        </h1>
                    </div>

                    <div class="mt-5">
                        <p class="text-sm text-white/55 sm:text-base">Aktualna temperatura</p>
                        <p id="control-temperature" class="text-5xl font-bold tracking-tight sm:text-6xl">
                            {{ $room['is_online'] ? $temperature . '°C' : '--- °C' }}
                        </p>
                    </div>

                    <div class="mt-7">
                        <p class="text-lg text-white/60">Sterowanie temperaturą</p>
                    </div>

                    <div class="mt-10">
                        <div class="mx-auto w-full max-w-[340px]">
                            <div class="relative">
                                <div
                                    class="absolute top-[-40px] -translate-x-1/2 text-sm font-medium text-white bg-blue-500/20 px-3 py-1 rounded-lg backdrop-blur shadow-[0_0_10px_rgba(59,130,246,0.5)] transition"
                                    :class="isAuto() ? 'opacity-100' : 'opacity-40'"
                                    :style="`left: ${thumbLeftPct()}%`"
                                >
                                    <span x-text="temperature.toFixed(1)"></span>°C
                                </div>

                                <input
                                    type="range"
                                    min="10"
                                    max="30"
                                    step="0.1"
                                    x-model.number="temperature"
                                    x-on:change="sendCommand('auto', temperature)"
                                    :disabled="activeMode !== 'auto'"
                                    class="temperature-slider w-full appearance-none bg-transparent disabled:cursor-not-allowed disabled:opacity-40"
                                >
                            </div>

                            <div class="mt-2 flex justify-between text-xs text-white/55">
                                <span>10°C</span>
                                <span>30°C</span>
                            </div>
                        </div>
                    </div>

                    <p id="control-humidity" class="mt-4 text-xl text-white/70">
                        Wilgotność {{ $room['is_online'] ? $humidity . '%' : '---%' }}
                    </p>

                    <div class="mt-6 space-y-3 text-left">
                        <x-mode-toggle label="Ogrzewanie" mode="heating">
                            <svg width="33" height="33" viewBox="0 0 33 33" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M9.16777 10.5007C9.02082 12.0682 8.91769 14.8423 9.84324 16.023C9.84324 16.023 9.40754 12.9757 13.3134 9.15233C14.8861 7.61319 15.2496 5.51975 14.7004 3.94968C14.3885 3.06022 13.8187 2.32546 13.3237 1.81241C13.035 1.51077 13.2567 1.01319 13.6769 1.03124C16.2189 1.14468 20.3388 1.85108 22.0893 6.24421C22.8576 8.17265 22.9143 10.1655 22.5482 12.1919C22.3162 13.4862 21.4912 16.3633 23.3732 16.7166C24.7164 16.9692 25.3661 15.9019 25.6575 15.1336C25.7786 14.8139 26.1989 14.734 26.4257 14.9892C28.6945 17.5699 28.8878 20.6095 28.4186 23.2263C27.5111 28.2846 22.3884 31.9662 17.2992 31.9662C10.9415 31.9662 5.88066 28.3284 4.5684 21.7439C4.03988 19.0858 4.30801 13.8265 8.40722 10.114C8.71144 9.83554 9.20902 10.083 9.16777 10.5007Z" fill="url(#paint0_radial_heat)"/>
                                <path d="M19.6222 19.9598C17.2786 16.9434 18.3279 13.5016 18.9029 12.1301C18.9802 11.9496 18.774 11.7795 18.6115 11.8903C17.6035 12.5761 15.5384 14.19 14.5768 16.4613C13.2748 19.5319 13.3676 21.0349 14.1385 22.8706C14.6026 23.9766 14.0637 24.2112 13.793 24.2524C13.5301 24.2937 13.2877 24.1184 13.0944 23.9353C12.5382 23.4012 12.1418 22.7227 11.9497 21.9759C11.9084 21.8161 11.6996 21.7723 11.6042 21.9038C10.8823 22.9015 10.5085 24.5025 10.4904 25.6343C10.4337 29.1328 13.3238 31.9688 16.8197 31.9688C21.2258 31.9688 24.4355 27.0961 21.9038 23.0227C21.169 21.8367 20.4781 21.0607 19.6222 19.9598Z" fill="url(#paint1_radial_heat)"/>
                                <defs>
                                    <radialGradient id="paint0_radial_heat" cx="0" cy="0" r="1" gradientTransform="matrix(-18.1982 -0.0789802 -0.129753 29.8596 16.04 32.0463)" gradientUnits="userSpaceOnUse">
                                        <stop offset="0.314" stop-color="#FF9800"/>
                                        <stop offset="0.662" stop-color="#FF6D00"/>
                                        <stop offset="0.972" stop-color="#F44336"/>
                                    </radialGradient>
                                    <radialGradient id="paint1_radial_heat" cx="0" cy="0" r="1" gradientTransform="matrix(-0.192325 19.0401 14.3291 0.144719 17.0621 13.9369)" gradientUnits="userSpaceOnUse">
                                        <stop offset="0.214" stop-color="#FFF176"/>
                                        <stop offset="0.328" stop-color="#FFF27D"/>
                                        <stop offset="0.487" stop-color="#FFF48F"/>
                                        <stop offset="0.672" stop-color="#FFF7AD"/>
                                        <stop offset="0.793" stop-color="#FFF9C4"/>
                                        <stop offset="0.822" stop-color="#FFF8BD" stop-opacity="0.804"/>
                                        <stop offset="0.863" stop-color="#FFF6AB" stop-opacity="0.529"/>
                                        <stop offset="0.91" stop-color="#FFF38D" stop-opacity="0.209"/>
                                        <stop offset="0.941" stop-color="#FFF176" stop-opacity="0"/>
                                    </radialGradient>
                                </defs>
                            </svg>
                        </x-mode-toggle>

                        <x-mode-toggle label="Chłodzenie" mode="cooling">
                            <svg width="33" height="33" viewBox="0 0 33 33" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <g clip-path="url(#clip0_cool)">
                                    <g filter="url(#filter0_cool)">
                                        <path fill-rule="evenodd" clip-rule="evenodd" d="M15.125 6.75677L11.4028 3.03464L13.3471 1.09039L16.5 4.24327L19.6528 1.09039L21.5971 3.03464L17.875 6.75677V11.1183L18.9062 12.1495L22 9.05577V5.50002H24.75V8.25002H27.5V11H23.9442L20.8505 14.0938L21.8817 15.125H25.5557L29.2778 11.4029L31.2221 13.3471L28.0692 16.5L31.2221 19.6529L29.2778 21.5971L25.5557 17.875H21.8817L20.8505 18.9063L23.9442 22H27.5V24.75H24.75V27.5H22V23.9443L18.9062 20.8505L17.875 21.8818V26.2433L21.5971 29.9654L19.6528 31.9096L16.5 28.7568L13.3471 31.9096L11.4028 29.9654L15.125 26.2433V21.8818L14.0937 20.8505L11 23.9443V27.5H8.24996V24.75H5.49996V22H9.05571L12.1495 18.9063L11.1182 17.875H6.75671L3.03458 21.5971L1.09033 19.6529L4.24321 16.5L1.09033 13.3471L3.03458 11.4029L6.75671 15.125H11.1182L12.1495 14.0938L9.05571 11H5.49996V8.25002H8.24996V5.50002H11V9.05577L14.0937 12.1495L15.125 11.1183V6.75677ZM16.5 19.3683L13.6317 16.5L16.5 13.6318L19.3682 16.5L16.5 19.3683Z" fill="#8FBFFA"/>
                                    </g>
                                </g>
                                <defs>
                                    <filter id="filter0_cool" x="-2.90967" y="-2.90961" width="38.1317" height="38.8193" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB">
                                        <feFlood flood-opacity="0" result="BackgroundImageFix"/>
                                        <feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha"/>
                                        <feOffset/>
                                        <feGaussianBlur stdDeviation="2"/>
                                        <feComposite in2="hardAlpha" operator="out"/>
                                        <feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.25 0"/>
                                        <feBlend mode="normal" in2="BackgroundImageFix" result="effect1_dropShadow_11_954"/>
                                        <feBlend mode="normal" in="SourceGraphic" in2="effect1_dropShadow_11_954" result="shape"/>
                                    </filter>
                                    <clipPath id="clip0_cool">
                                        <rect width="33" height="33" fill="white"/>
                                    </clipPath>
                                </defs>
                            </svg>
                        </x-mode-toggle>

                        <x-mode-toggle label="Tryb automatyczny" mode="auto">
                            <svg width="33" height="33" viewBox="0 0 33 33" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M27.225 18.7C27.2938 18.2187 27.3625 17.7375 27.3625 17.1875C27.3625 16.6375 27.2938 16.1562 27.225 15.675L30.3188 13.475C30.5938 13.2687 30.7313 12.8562 30.525 12.5125L27.5 7.42498C27.2938 7.08123 26.95 6.94373 26.6063 7.14998L23.1688 8.73123C22.3438 8.11248 21.5188 7.63123 20.5563 7.21873L20.2125 3.43748C20.1438 3.09373 19.8688 2.81873 19.525 2.81873H13.6125C13.2688 2.81873 12.925 3.09373 12.925 3.43748L12.5813 7.21873C11.6188 7.63123 10.725 8.11248 9.96876 8.73123L6.53126 7.14998C6.18751 7.01248 5.77501 7.14998 5.63751 7.42498L2.68126 12.5125C2.47501 12.8562 2.61251 13.2687 2.88751 13.475L5.98126 15.675C5.91251 16.1562 5.84376 16.6375 5.84376 17.1875C5.84376 17.7375 5.91251 18.2187 5.98126 18.7L2.75001 20.9C2.47501 21.1062 2.33751 21.5187 2.54376 21.8625L5.50001 26.95C5.70626 27.2937 6.05001 27.4312 6.39376 27.225L9.83126 25.6437C10.6563 26.2625 11.4813 26.7437 12.4438 27.1562L12.7875 30.9375C12.8563 31.2812 13.1313 31.5562 13.475 31.5562H19.3875C19.7313 31.5562 20.075 31.2812 20.075 30.9375L20.4188 27.1562C21.3813 26.7437 22.275 26.2625 23.0313 25.6437L26.4688 27.225C26.8125 27.3625 27.225 27.225 27.3625 26.95L30.3188 21.8625C30.525 21.5187 30.3875 21.1062 30.1125 20.9L27.225 18.7ZM16.5 24.0625C12.7188 24.0625 9.62501 20.9687 9.62501 17.1875C9.62501 13.4062 12.7188 10.3125 16.5 10.3125C20.2813 10.3125 23.375 13.4062 23.375 17.1875C23.375 20.9687 20.2813 24.0625 16.5 24.0625Z" fill="#607D8B"/>
                                <path d="M16.5 8.9375C11.9625 8.9375 8.25 12.65 8.25 17.1875C8.25 21.725 11.9625 25.4375 16.5 25.4375C21.0375 25.4375 24.75 21.725 24.75 17.1875C24.75 12.65 21.0375 8.9375 16.5 8.9375ZM16.5 20.625C14.575 20.625 13.0625 19.1125 13.0625 17.1875C13.0625 15.2625 14.575 13.75 16.5 13.75C18.425 13.75 19.9375 15.2625 19.9375 17.1875C19.9375 19.1125 18.425 20.625 16.5 20.625Z" fill="#455A64"/>
                            </svg>
                        </x-mode-toggle>

                        <p class="mt-3 text-center text-xs text-white/45">
                            Kliknij ponownie aktywny tryb, aby wyłączyć wszystkie — urządzenie tylko mierzy i wysyła dane.
                        </p>
                    </div>

                    <div class="mt-8">
                        <div class="relative mx-auto w-full max-w-[360px]">
                            <x-history :points="$historyPoints" />

                            <button
                                type="button"
                                @click="historyOpen = true"
                                class="absolute -right-4 top-0 rounded-full border border-blue-400/40 bg-blue-500/10 px-4 py-1.5 text-xs text-blue-200 shadow-[0_0_20px_rgba(59,130,246,0.18)] transition hover:bg-blue-500/20"
                            >
                                Historia
                            </button>
                        </div>
                    </div>
                    <x-history-modal
                        :historyPoints24h="$historyPoints24h"
                        :historyPoints7d="$historyPoints7d"
                        :historyPoints30d="$historyPoints30d"
                        :historyLastUpdated="$historyLastUpdated ?? null"
                    />
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@section('scripts')
<script>
    function updateControlData() {
        fetch('{{ route('fetch.status', ['device_uid' => $room['device_uid']]) }}', {
            headers: { 'Accept': 'application/json' }
        })
            .then(response => {
                if (!response.ok) {
                    throw new Error('HTTP ' + response.status);
                }
                return response.json();
            })
            .then(data => {
                const root = document.getElementById('control-panel-root');

                const tempElement = document.getElementById('control-temperature');
                const humElement = document.getElementById('control-humidity');

                if (tempElement && data.temperature !== undefined) {
                    tempElement.innerText = Number(data.temperature).toFixed(1) + "°C";
                }

                if (humElement && data.humidity !== undefined) {
                    humElement.innerText = "Wilgotność " + data.humidity + "%";
                }

                if (root && typeof Alpine !== 'undefined' && Alpine.$data) {
                    try {
                        const ax = Alpine.$data(root);
                        if (ax) {
                            const inCooldown = ax.commandCooldownUntil && Date.now() < ax.commandCooldownUntil;
                            if (!inCooldown) {
                                if (data.target !== undefined && data.target !== null) {
                                    const tgt = Number(data.target);
                                    if (!Number.isNaN(tgt)) {
                                        ax.temperature = Math.min(30, Math.max(10, tgt));
                                    }
                                }
                                if (data.state !== undefined && data.state !== null) {
                                    const modes = { 1: 'heating', 2: 'cooling', 3: 'auto', 4: 'monitor' };
                                    const m = modes[Number(data.state)];
                                    if (m) {
                                        ax.activeMode = m;
                                    }
                                }
                            }
                        }
                    } catch (e) {
                        /* Alpine jeszcze nie zainicjalizował komponentu */
                    }
                }
            })
            .catch(error => console.error('Błąd pobierania danych:', error));
    }

    updateControlData();
    setInterval(updateControlData, 5000);
</script>
@endsection