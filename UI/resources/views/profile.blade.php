@extends('layouts.app')

@section('content')
<section class="relative min-h-screen overflow-hidden text-white">
    <div class="pointer-events-none absolute inset-0">
        <div class="absolute left-[-120px] top-[120px] h-[260px] w-[260px] rounded-full bg-blue-500/10 blur-[120px]"></div>
        <div class="absolute right-[-100px] top-[180px] h-[300px] w-[300px] rounded-full bg-blue-400/10 blur-[140px]"></div>
        <div class="absolute left-1/2 top-[220px] h-[420px] w-[420px] -translate-x-1/2 rounded-full bg-blue-500/10 blur-[170px]"></div>
        <div class="absolute bottom-[60px] right-[10%] h-[240px] w-[240px] rounded-full bg-orange-500/10 blur-[130px]"></div>
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_center,rgba(37,99,235,0.08),transparent_55%)]"></div>
    </div>

    <div class="relative z-10 px-6 pb-12 pt-10 lg:px-12">
        <div class="mx-auto max-w-[1250px]">
            <div class="relative rounded-[32px] border border-white/10 bg-white/5 p-6 shadow-[0_0_40px_rgba(255,255,255,0.05)] backdrop-blur-xl lg:p-8">

                <h1 class="mb-8 text-center text-4xl font-semibold text-white sm:text-5xl">
                    Mój profil
                </h1>

                <button onclick="window.location.href='{{ route('dashboard') }}'"
                    class="absolute -left-30 top-1/2 -translate-y-1/2 flex h-16 w-16 items-center justify-center rounded-full border border-white/10 bg-white/5 backdrop-blur-md text-white/70 transition hover:bg-white/10 hover:text-white hover:shadow-[0_0_15px_rgba(59,130,246,0.4)]">
                    <svg width="45" height="45" viewBox="0 0 25 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12.5 21C7.8 21 4 17.2 4 12.5C4 7.8 7.8 4 12.5 4C17.2 4 21 7.8 21 12.5C21 17.2 17.2 21 12.5 21ZM12.5 5C8.35 5 5 8.35 5 12.5C5 16.65 8.35 20 12.5 20C16.65 20 20 16.65 20 12.5C20 8.35 16.65 5 12.5 5Z" fill="#E5E7EB"/>
                        <path d="M12.65 17.35L7.80005 12.5L12.65 7.64999L13.35 8.34999L9.20005 12.5L13.35 16.65L12.65 17.35Z" fill="#E5E7EB"/>
                        <path d="M8.5 12H17V13H8.5V12Z" fill="#E5E7EB"/>
                    </svg>
                </button>

                <div class="space-y-8">

                    <div class="rounded-[28px] border border-white/10 bg-white/5 p-6">
                        <div class="mb-5 flex items-center gap-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-full bg-white/10">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6.75a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.5 20.118a7.5 7.5 0 0115 0A17.933 17.933 0 0112 21.75a17.933 17.933 0 01-7.5-1.632z"/>
                                </svg>
                            </div>
                            <h2 class="text-2xl font-semibold text-white">Dane użytkownika</h2>
                        </div>

                        <form method="POST" action="">
                            @csrf

                            <div class="space-y-4">
                                <input
                                    type="text"
                                    name="name"
                                    value="{{ auth()->user()->name }}"
                                    class="w-full rounded-2xl border border-white/10 bg-white/5 px-5 py-4 text-lg text-white placeholder:text-slate-400 focus:border-white/30 focus:outline-none"
                                    placeholder="Imię i nazwisko"
                                >

                                <input
                                    type="email"
                                    name="email"
                                    value="{{ auth()->user()->email }}"
                                    class="w-full rounded-2xl border border-white/10 bg-white/5 px-5 py-4 text-lg text-white placeholder:text-slate-400 focus:border-white/30 focus:outline-none"
                                    placeholder="Adres e-mail"
                                >

                                <button
                                    type="submit"
                                    class="w-full rounded-2xl border border-white/10 bg-white/10 py-4 text-xl font-semibold text-white transition hover:bg-white/20"
                                >
                                    Zapisz zmiany
                                </button>
                            </div>
                        </form>
                    </div>

                    <div class="rounded-[28px] border border-white/10 bg-white/5 p-6">
                        <div class="mb-5 flex items-center gap-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-full bg-white/10">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 7.5h16.5M6 3.75h12A2.25 2.25 0 0120.25 6v12A2.25 2.25 0 0118 20.25H6A2.25 2.25 0 013.75 18V6A2.25 2.25 0 016 3.75z"/>
                                </svg>
                            </div>
                            <h2 class="text-2xl font-semibold text-white">Moje urządzenia</h2>
                        </div>

                        <div class="overflow-hidden rounded-[24px] border border-white/10 bg-white/5">
                            <div class="hidden grid-cols-[1.2fr_1.4fr_0.8fr_0.7fr] gap-4 border-b border-white/10 px-6 py-4 text-sm font-medium uppercase tracking-wide text-slate-400 md:grid">
                                <div>Pomieszczenie</div>
                                <div>Urządzenie</div>
                                <div>Status</div>
                                <div>Akcja</div>
                            </div>

                            @forelse($devices as $device)
                                @php
                                    $room = $device->room_name;
                                    $deviceName = $device->name;
                                    $isOnline = $device->is_active;
                                    $status = $isOnline ? 'Online' : 'Offline';
                                @endphp

                                <div class="border-b border-white/10 last:border-b-0">
                                    <div class="hidden items-center gap-4 px-6 py-5 md:grid md:grid-cols-[1.2fr_1.4fr_0.8fr_0.7fr]">
                                        <div class="text-2xl font-semibold text-white">{{ $room }}</div>
                                        <div class="text-xl text-slate-300">{{ $deviceName }}</div>

                                        <div class="flex items-center gap-3">
                                            <span class="h-3 w-3 rounded-full {{ $isOnline ? 'bg-lime-400' : 'bg-red-500' }}"></span>
                                            <span class="text-xl {{ $isOnline ? 'text-slate-200' : 'text-slate-300' }}">
                                                {{ $status }}
                                            </span>
                                        </div>

                                        <div>
                                            <button
                                                type="button"
                                                class="rounded-xl border border-white/10 bg-white/5 px-6 py-2 text-lg text-white transition hover:bg-white/10"
                                            >
                                                Edytuj
                                            </button>
                                        </div>
                                    </div>

                                    <div class="space-y-3 px-5 py-5 md:hidden">
                                        <div class="flex items-center justify-between">
                                            <div class="text-xl font-semibold text-white">{{ $room }}</div>
                                            <div class="flex items-center gap-2">
                                                <span class="h-3 w-3 rounded-full {{ $isOnline ? 'bg-lime-400' : 'bg-red-500' }}"></span>
                                                <span class="text-base text-slate-300">{{ $status }}</span>
                                            </div>
                                        </div>

                                        <div class="text-base text-slate-300">{{ $deviceName }}</div>

                                        <button
                                            type="button"
                                            class="w-full rounded-xl border border-white/10 bg-white/5 px-4 py-2 text-base text-white transition hover:bg-white/10"
                                        >
                                            Edytuj
                                        </button>
                                    </div>
                                </div>
                            @empty
                                <div class="px-6 py-5 text-slate-400">
                                    Brak dodanych urządzeń.
                                </div>
                            @endforelse
                        </div>

                        <h3 class="mt-6 text-xl font-semibold text-white">Dodaj nowe urządzenie</h3>

                        <div class="mt-6 rounded-[24px] border border-white/10 bg-white/5 p-5">
                            @if ($errors->any())
                                <div class="mb-4 rounded-xl border border-red-400/20 bg-red-500/10 p-4 text-sm text-red-200">
                                    <ul class="space-y-1">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <form method="POST" action="{{ route('device.store') }}" class="mt-6">
                                @csrf

                                <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                                    <input
                                        type="text"
                                        name="name"
                                        value="{{ old('name') }}"
                                        placeholder="Nazwa urządzenia"
                                        class="w-full rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-white placeholder:text-slate-400 focus:border-white/30 focus:outline-none"
                                    >

                                    <input
                                        type="text"
                                        name="device_uid"
                                        id="device_uid"
                                        value="{{ old('device_uid') }}"
                                        placeholder="UID urządzenia"
                                        readonly
                                        class="w-full rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-white placeholder:text-slate-400 focus:border-white/30 focus:outline-none"
                                    >

                                    <input
                                        type="text"
                                        name="room_name"
                                        value="{{ old('room_name') }}"
                                        placeholder="Nazwa pomieszczenia"
                                        class="w-full rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-white placeholder:text-slate-400 focus:border-white/30 focus:outline-none"
                                    >
                                </div>

                                <div class="mt-5 flex flex-col gap-4 md:flex-row">
                                    <button
                                        type="button"
                                        onclick="connectBluetooth()"
                                        class="rounded-2xl border border-white/10 bg-white/10 px-6 py-3 text-white transition hover:bg-white/20"
                                    >
                                        Połącz przez Bluetooth
                                    </button>

                                    <button
                                        id="save-device-button"
                                        type="submit"
                                        disabled
                                        class="rounded-2xl border border-white/10 bg-white/10 px-6 py-3 text-white opacity-50 transition hover:bg-white/20 disabled:cursor-not-allowed"
                                    >
                                        Zapisz urządzenie
                                    </button>
                                </div>
                            </form>

                            <div id="bluetooth-status" class="mt-5 text-sm text-slate-400"></div>
                        </div>
                    </div>

                    <div class="mt-6 text-center p-5">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button
                                type="submit"
                                class="rounded-xl border border-red-400/20 bg-red-500/10 px-6 py-3 text-sm font-medium text-red-200 transition hover:bg-red-500/20 hover:shadow-[0_0_20px_rgba(248,113,113,0.3)]"
                            >
                                Wyloguj się
                            </button>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </div>
</section>

<div id="wifiModal" class="fixed inset-0 z-[999999] hidden items-center justify-center bg-black/95 px-4">
    <div class="relative w-full max-w-md rounded-[28px] border border-white/10 bg-[#020617] p-6 text-white shadow-[0_0_80px_rgba(0,0,0,0.9)]">
        <h2 class="text-2xl font-semibold">Konfiguracja Wi-Fi</h2>

        <p class="mt-2 text-sm text-slate-400">
            Wpisz dane sieci, z którą ma połączyć się urządzenie.
        </p>

        <div class="mt-5 space-y-4">
            <input
                id="wifi_ssid"
                type="text"
                placeholder="Nazwa sieci Wi-Fi"
                class="w-full rounded-2xl border border-white/10 bg-[#0f172a] px-4 py-3 text-white placeholder:text-slate-400 focus:border-blue-400/50 focus:outline-none"
            >

            <input
                id="wifi_password"
                type="password"
                placeholder="Hasło Wi-Fi"
                class="w-full rounded-2xl border border-white/10 bg-[#0f172a] px-4 py-3 text-white placeholder:text-slate-400 focus:border-blue-400/50 focus:outline-none"
            >

            <button
                type="button"
                onclick="sendWifiToDevice()"
                class="w-full rounded-2xl border border-blue-400/30 bg-blue-600 px-6 py-3 font-medium text-white transition hover:bg-blue-500"
            >
                Wyślij dane do urządzenia
            </button>

            <button
                type="button"
                onclick="closeWifiModal()"
                class="w-full rounded-2xl border border-white/10 bg-[#111827] px-6 py-3 text-slate-300 transition hover:bg-[#1f2937]"
            >
                Anuluj
            </button>
        </div>

        <div id="wifi-send-status" class="mt-4 text-sm text-slate-400"></div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    const SERVICE_UUID = "12345678-1234-1234-1234-1234567890ab";
    const CHARACTERISTIC_UUID = "abcd1234-5678-1234-5678-abcdef123456";
    const WIFI_CONFIG_CHARACTERISTIC_UUID = "dcba4321-8765-4321-8765-fedcba654321";
    const WIFI_STATUS_CHARACTERISTIC_UUID = "eeee1111-2222-3333-4444-555566667777";

    let bluetoothDevice = null;
    let bluetoothServer = null;
    let bluetoothService = null;

    document.addEventListener('DOMContentLoaded', () => {
        const modal = document.getElementById('wifiModal');

        if (modal && modal.parentElement !== document.body) {
            document.body.appendChild(modal);
        }
    });

    async function connectBluetooth() {
        const statusBox = document.getElementById('bluetooth-status');
        const uidInput = document.getElementById('device_uid');

        if (!navigator.bluetooth) {
            statusBox.innerText = 'Ta przeglądarka nie obsługuje Web Bluetooth.';
            return;
        }

        try {
            statusBox.innerText = 'Szukanie urządzenia...';

            bluetoothDevice = await navigator.bluetooth.requestDevice({
                filters: [{ services: [SERVICE_UUID] }]
            });

            statusBox.innerText = 'Łączenie z urządzeniem...';

            bluetoothServer = await bluetoothDevice.gatt.connect();
            bluetoothService = await bluetoothServer.getPrimaryService(SERVICE_UUID);

            const characteristic = await bluetoothService.getCharacteristic(CHARACTERISTIC_UUID);
            const value = await characteristic.readValue();

            const decoder = new TextDecoder('utf-8');
            const deviceMac = decoder.decode(value).trim();

            if (!deviceMac) {
                throw new Error('Nie udało się odczytać UID urządzenia.');
            }

            uidInput.value = deviceMac;

            statusBox.innerText = `Połączono z urządzeniem: ${bluetoothDevice.name || 'Thermio'} | UID: ${deviceMac}`;

            openWifiModal();

        } catch (error) {
            console.error('Błąd BLE:', error);
            statusBox.innerText = `Nie udało się pobrać identyfikatora urządzenia. ${error.message ?? ''}`;
        }
    }

    function openWifiModal() {
        const modal = document.getElementById('wifiModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.classList.add('overflow-hidden');
    }

    function closeWifiModal() {
        const modal = document.getElementById('wifiModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.body.classList.remove('overflow-hidden');
    }

    async function sendWifiToDevice() {
        const statusBox = document.getElementById('wifi-send-status');
        const ssid = document.getElementById('wifi_ssid').value.trim();
        const password = document.getElementById('wifi_password').value;

        if (!ssid) {
            statusBox.innerText = 'Podaj nazwę sieci Wi-Fi.';
            return;
        }

        try {
            statusBox.innerText = 'Wysyłanie danych do urządzenia...';

            if (!bluetoothDevice) {
                throw new Error('Najpierw połącz się z urządzeniem przez Bluetooth.');
            }

            if (!bluetoothDevice.gatt.connected) {
                bluetoothServer = await bluetoothDevice.gatt.connect();
                bluetoothService = await bluetoothServer.getPrimaryService(SERVICE_UUID);
            }

            const wifiCharacteristic = await bluetoothService.getCharacteristic(WIFI_CONFIG_CHARACTERISTIC_UUID);

            const payload = JSON.stringify({
                ssid: ssid,
                password: password
            });

            const encoder = new TextEncoder();
            await wifiCharacteristic.writeValue(encoder.encode(payload));

            statusBox.innerText = 'Dane Wi-Fi wysłane. Sprawdzanie połączenia...';

            const statusCharacteristic = await bluetoothService.getCharacteristic(WIFI_STATUS_CHARACTERISTIC_UUID);

            let wifiStatus = '';

            for (let i = 0; i < 20; i++) {
                await new Promise(resolve => setTimeout(resolve, 1000));

                const statusValue = await statusCharacteristic.readValue();

                wifiStatus = new TextDecoder('utf-8')
                    .decode(statusValue)
                    .replace(/\0/g, '')
                    .replace(/\r?\n/g, '')
                    .trim();

                if (wifiStatus.includes('WIFI_OK') || wifiStatus.includes('AWS_OK')) {
                    statusBox.innerText = 'Połączono z Wi-Fi. Możesz zapisać urządzenie.';

                    const saveButton = document.getElementById('save-device-button');

                    if (saveButton) {
                        saveButton.disabled = false;
                        saveButton.removeAttribute('disabled');
                        saveButton.classList.remove('opacity-50');
                        saveButton.classList.add('opacity-100');
                    }
                    closeWifiModal();
                    return;
                }

                if (wifiStatus === 'WIFI_ERROR') {
                    statusBox.innerText = 'Nie udało się połączyć z Wi-Fi. Sprawdź nazwę sieci lub hasło.';
                    return;
                }

                if (wifiStatus === 'AWS_ERROR') {
                    statusBox.innerText = 'Wi-Fi działa, ale nie udało się połączyć z AWS IoT.';
                    return;
                }
            }

            statusBox.innerText = 'Nie udało się potwierdzić połączenia z Wi-Fi.';
            setTimeout(() => {
                closeWifiModal();

                if (bluetoothDevice?.gatt?.connected) {
                    bluetoothDevice.gatt.disconnect();
                }
            }, 1500);

        } catch (error) {
            console.error('Błąd wysyłania WiFi:', error);
            statusBox.innerText = `Nie udało się wysłać danych Wi-Fi. ${error.message ?? ''}`;
        }
    }
</script>
@endsection