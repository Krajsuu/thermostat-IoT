<div class="flex justify-center {{ $align }}">
    <div class="w-full max-w-[380px] rounded-[28px] border {{ $border }} bg-white/5 p-8 backdrop-blur-xl {{ $shadow }}">
        <div class="flex items-center justify-between">
            <div class="flex h-16 w-16 items-center justify-center rounded-full border {{ $iconBorder }} {{ $iconBg }} {{ $iconColor }} {{ $iconShadow }}">
                {!! $icon !!}
            </div>
        </div>

        <div class="mt-8">
            <p class="text-xl text-slate-300">{{ $label }}</p>
            <h3 class="mt-2 text-4xl font-bold tracking-wide {{ $valueColor }}">{{ $value }}</h3>
            <p class="mt-4 text-xl text-slate-400">{{ $description }}</p>
        </div>
    </div>
</div>