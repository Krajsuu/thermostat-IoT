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

        <div class="relative z-10 mx-auto w-full max-w-[1440px] px-6 py-10 lg:px-14">

            <section class="relative overflow-hidden  pb-10 pt-6 text-center">
                <h1 class="mx-auto max-w-[900px] text-4xl font-semibold leading-tight text-white sm:text-5xl lg:text-6xl">
                    Twoje urządzenia
                </h1>

                <p class="mx-auto mt-4 max-w-[720px] text-base leading-7 text-slate-300 sm:text-xl">
                    Zarządzaj ustawieniami temperatury w nowoczesnym, przejrzystym panelu.
                </p>
            </section>

            <section class="grid grid-cols-1 gap-8 px-2 py-12 md:grid-cols-2 xl:grid-cols-3">
                @foreach($devices as $device)

                    @if($device['is_online'])
                        <a href="{{ route('control.panel', ['room' => $device['slug']]) }}"
                           class="group block transition duration-300 hover:scale-[1.02]">
                    @else
                        <div
                            onclick="window.dispatchEvent(new CustomEvent('notify', {
                                detail: { message: '{{ $device['name'] }} jest offline', type: 'error' }
                            }))"
                            class="cursor-not-allowed opacity-70"
                        >
                    @endif
                        <x-device-card
                            :slug="$device['slug']"
                            :name="$device['name']"
                            :status="$device['is_online'] ? 'Online' : 'Offline'"
                            :temperature="$device['temperature']"
                            :humidity="$device['humidity']"
                            :mode="$device['mode']"
                            :heating="$device['heating']"
                        />
                    @if($device['is_online'])
                        </a>
                    @else
                        </div>
                    @endif

                @endforeach
            </section>
        </div>
    </section>
@endsection

@section('scripts')
<script>
    const devices = @json($devices);

    function formatDashboardMode(state) {
        const n = Number(state);
        if (n === 3) {
            return 'Auto';
        }
        if ([1, 2].includes(n)) {
            return 'Manual';
        }
        if (n === 4) {
            return 'Sensor Only';
        }
        return 'brak';
    }

    function updateDeviceStatus() {
        devices.forEach(device => {
            if (!device.is_online) return;

            fetch(`/fetch-status/${encodeURIComponent(device.device_uid)}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    const slug = device.slug;

                    const tempElement = document.getElementById(`temp-${slug}`);
                    if (tempElement && data.temperature !== undefined) {
                        tempElement.innerText = `${Number(data.temperature).toFixed(1)}°C`;
                    }

                    const humElement = document.getElementById(`hum-${slug}`);
                    if (humElement && data.humidity !== undefined) {
                        humElement.innerText = `${data.humidity}%`;
                    }

                    const heatElement = document.getElementById(`heat-${slug}`);
                    if (heatElement && data.heater !== undefined) {
                        heatElement.innerText = data.heater;
                    }

                    const modeElement = document.getElementById(`mode-${slug}`);
                    if (modeElement) {
                        modeElement.innerText = formatDashboardMode(data.state);
                    }
                })
                .catch(error => {
                    console.error(`Błąd pobierania danych dla ${device.device_uid}:`, error);
                });
            });
    }

    updateDeviceStatus();
    setInterval(updateDeviceStatus, 5000);
</script>
@endsection