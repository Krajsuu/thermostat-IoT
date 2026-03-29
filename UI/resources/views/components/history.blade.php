@props([
    'points' => [],
    'height' => 110,
])

<div class="w-full">
    <svg viewBox="0 0 360 {{ $height }}" class="w-full">

        <defs>
            <filter id="glow-history">
                <feGaussianBlur stdDeviation="3.5" result="coloredBlur" />
                <feMerge>
                    <feMergeNode in="coloredBlur" />
                    <feMergeNode in="SourceGraphic" />
                </feMerge>
            </filter>
        </defs>

        <line 
            x1="12" 
            y1="{{ $height - 18 }}" 
            x2="348" 
            y2="{{ $height - 18 }}" 
            stroke="rgba(255,255,255,0.15)" 
            stroke-dasharray="3 5" 
        />

        @if(count($points) >= 2)
            @php
                $count = count($points);

                $temps = array_column($points, 'temp');
                $minTemp = min($temps);
                $maxTemp = max($temps);
                
                if ($maxTemp == $minTemp) {
                    $maxTemp += 1;
                    $minTemp -= 1;
                }

                $left = 12;
                $right = 348;

                $bottom = $height - 24;
                $top = 20;

                $path = '';
                $circles = [];

                foreach ($points as $i => $point) {
                    $x = $left + (($right - $left) * $i / max($count - 1, 1));

                    $normalized = ($point['temp'] - $minTemp) / ($maxTemp - $minTemp);
                    $y = $bottom - ($normalized * ($bottom - $top));

                    $path .= $i === 0 ? "M{$x} {$y}" : " L{$x} {$y}";

                    $circles[] = [
                        'x' => $x,
                        'y' => $y,
                        'label' => $point['label'],
                        'temp' => $point['temp'],
                    ];
                }
            @endphp

            <path
                d="{{ $path }}"
                fill="none"
                stroke="#7dd3fc"
                stroke-width="2"
                filter="url(#glow-history)"
            />

            @foreach($circles as $c)
                <circle cx="{{ $c['x'] }}" cy="{{ $c['y'] }}" r="3" fill="#dbeafe" />
            @endforeach

            @foreach($circles as $c)
                <text
                    x="{{ $c['x'] }}"
                    y="{{ $height - 2 }}"
                    text-anchor="middle"
                    fill="rgba(255,255,255,0.45)"
                    font-size="10"
                >
                    {{ $c['label'] }}
                </text>
            @endforeach
        @endif

    </svg>
</div>