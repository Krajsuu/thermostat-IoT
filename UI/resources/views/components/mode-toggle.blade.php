@props([
    'label',
    'mode',
])

<button
    type="button"
    @click="setMode('{{ $mode }}')"
    :disabled="activeMode === 'auto' && '{{ $mode }}' !== 'auto'"
    class="flex w-full items-center justify-between rounded-2xl border px-4 py-4 backdrop-blur-md transition duration-200 disabled:cursor-not-allowed"
    :class="[
        activeMode === '{{ $mode }}'
            ? 'border-blue-400/30 bg-white/8 shadow-[0_0_20px_rgba(59,130,246,0.10)]'
            : 'border-white/10 bg-white/5 hover:bg-white/15',
        activeMode === 'auto' && '{{ $mode }}' !== 'auto'
            ? 'opacity-40'
            : 'opacity-100'
    ]"
>
    <div class="flex items-center gap-3">
        <span class="flex h-8 w-8 items-center justify-center">
            {{ $slot }}
        </span>

        <span class="font-medium text-white">
            {{ $label }}
        </span>
    </div>

    <span
        class="relative block h-6 w-11 rounded-full transition"
        :class="activeMode === '{{ $mode }}' ? 'bg-blue-500/30' : 'bg-white/10'"
    >
        <span
            class="absolute top-1 h-4 w-4 rounded-full transition-all duration-300"
            :class="activeMode === '{{ $mode }}'
                ? 'left-6 bg-blue-300 shadow-[0_0_10px_rgba(125,211,252,0.9)]'
                : 'left-1 bg-white/40'"
        ></span>
    </span>
</button>