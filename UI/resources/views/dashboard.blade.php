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
        <x-device-card
            name="Salon"
            status="Online"
            temperature="22.4°C"
            humidity="48%"
            mode="AUTO"
            heating="ON"
        />

        <x-device-card
            name="Pokój"
            status="Online"
            temperature="25°C"
            humidity="42%"
            mode="MANUAL"
            heating="OFF"
        />

        <x-device-card
            name="Biuro"
            status="Offline"
            temperature="--- °C"
            humidity="---%"
            mode="MANUAL"
            heating="OFF"
        />

        
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
