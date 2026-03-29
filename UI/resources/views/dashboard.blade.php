@extends('layouts.app')

@section('content')
    <section class="relative border-b border-white/10 py-8 text-center overflow-hidden">
        <div class="pointer-events-none absolute inset-0 flex justify-center">
            <div class="h-[100px] w-[300px] rounded-full bg-blue-500/20 blur-[100px]"></div>
        </div>

        <h1 class="text-3xl sm:text-5xl font-bold text-white">
            Twoje urządzenia
        </h1>

        <p class="mt-3 text-base sm:text-2xl text-white/60">
            Zarządzaj ustawieniami temperatury
        </p>
    </section>

    <section class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-16 px-16 py-12 justify-items-center">
        @foreach($devices as $device)

            @if($device['is_online'])
                <a href="{{ route('control.panel', ['room' => $device['slug']]) }}"
                class="block flex justify-center transition hover:scale-[1.02]">
            @else
                <div
                    onclick="window.dispatchEvent(new CustomEvent('notify', {
                        detail: { message: '{{ $device['name'] }} jest offline', type: 'error' }
                    }))"
                    class="flex justify-center opacity-60 cursor-not-allowed"
                >
            @endif

                <x-device-card
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
@endsection

@section('scripts')
<script>
    function updateDeviceStatus() {
        fetch('/fetch-status')
            .then(response => response.json())
            .then(data => {
                if (data.temperature) {
                    // Aktualizujemy temperaturę w Salonie (ID: temp-Salon)
                    const tempElement = document.getElementById('temp-Salon');
                    if (tempElement) {
                        tempElement.innerText = data.temperature.toFixed(1) + "°C";
                    }
                    
                    // Jeśli Twój kontroler zwraca też wilgotność (hum)
                    const humElement = document.getElementById('hum-Salon');
                    if (humElement && data.humidity) {
                        humElement.innerText = data.humidity + "%";
                    }
                }
            })
            .catch(error => console.error('Błąd pobierania danych:', error));
    }

    // Uruchom od razu i powtarzaj co 5 sekund
    updateDeviceStatus();
    setInterval(updateDeviceStatus, 5000);
</script>
@endsection

