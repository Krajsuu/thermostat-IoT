@extends('layouts.app')

@section('content')
<section class="relative min-h-screen overflow-hidden text-white">
    <div class="pointer-events-none absolute inset-0">
        <div class="absolute left-[-120px] top-[120px] h-[260px] w-[260px] rounded-full bg-blue-500/10 blur-[120px]"></div>
        <div class="absolute right-[-100px] top-[180px] h-[300px] w-[300px] rounded-full bg-blue-400/10 blur-[140px]"></div>
        <div class="absolute left-1/2 top-[220px] h-[420px] w-[420px] -translate-x-1/2 rounded-full bg-blue-500/10 blur-[170px]"></div>
        <div class="absolute bottom-[60px] right-[10%] h-[240px] w-[240px] rounded-full bg-orange-500/10 blur-[130px]"></div>

        <div class="absolute inset-0 bg-[radial-gradient(circle_at_center,rgba(37,99,235,0.08),transparent_55%)]"></div>
        <div class="absolute inset-0 opacity-[0.08] [background:linear-gradient(to_right,transparent,rgba(255,255,255,0.04),transparent)] bg-[length:600px_100%]"></div>
    </div>

    <div class="relative z-10 px-6 pb-12 pt-10 lg:px-12">
        <div class="mx-auto max-w-[1250px]">
            <div class="rounded-[32px] border border-white/10 bg-white/5 p-6 shadow-[0_0_40px_rgba(255,255,255,0.05)] backdrop-blur-xl lg:p-8">
                
                <h1 class="mb-8 text-center text-4xl font-semibold text-white sm:text-5xl">
                    Mój profil
                </h1>

                <button onclick="window.location.href='{{ route('dashboard') }}'" class="absolute -left-30 top-1/2 -translate-y-1/2 flex h-16 w-16 items-center justify-center rounded-full border border-white/10 bg-white/5 backdrop-blur-md text-white/70 transition hover:bg-white/10 hover:text-white hover:shadow-[0_0_15px_rgba(59,130,246,0.4)]">
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
                        <div class="mb-5 flex items-center justify-between gap-4">
                            <div class="flex items-center gap-3">
                                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-white/10">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 7.5h16.5M6 3.75h12A2.25 2.25 0 0120.25 6v12A2.25 2.25 0 0118 20.25H6A2.25 2.25 0 013.75 18V6A2.25 2.25 0 016 3.75z"/>
                                    </svg>
                                </div>
                                <h2 class="text-2xl font-semibold text-white">Moje urządzenia</h2>
                            </div>
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
                            @endforeach
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
                                        type="submit"
                                        class="rounded-2xl border border-white/10 bg-white/10 px-6 py-3 text-white transition hover:bg-white/20"
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
@endsection

@section('scripts')
<script>
async function connectBluetooth() {
    const statusBox = document.getElementById('bluetooth-status');
    const uidInput = document.getElementById('device_uid');

    if (!navigator.bluetooth) {
        statusBox.innerText = 'Ta przeglądarka nie obsługuje Bluetooth.';
        return;
    }

    try {
        const device = await navigator.bluetooth.requestDevice({
            acceptAllDevices: true
        });

        console.log("Bluetooth device:", device);
        console.log("Bluetooth device id:", device.id);
        console.log("Bluetooth device name:", device.name);

        if (uidInput) {
            uidInput.value = device.id || '';
        }

        statusBox.innerText = `Połączono z urządzeniem: ${device.name || 'Nieznane urządzenie'} | ID: ${device.id || 'brak'}`;
    } catch (error) {
        statusBox.innerText = 'Nie udało się połączyć z urządzeniem.';
        console.error(error);
    }
}
</script>
@endsection