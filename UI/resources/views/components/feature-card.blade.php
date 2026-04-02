<div class="rounded-[28px] border {{ $border }} bg-white/5 p-8 backdrop-blur-xl {{ $shadow }}">
    <div class="flex h-16 w-16 items-center justify-center rounded-2xl border {{ $iconBorder }} {{ $iconBg }} {{ $iconColor }} {{ $iconShadow }}">
        {!! $icon !!}
    </div>

    <h3 class="mt-8 text-3xl font-semibold text-white">
        {{ $title }}
    </h3>

    <p class="mt-5 text-lg leading-8 text-slate-400">
        {{ $description }}
    </p>

</div>