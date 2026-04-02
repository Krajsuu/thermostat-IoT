<?php

namespace App\View\Components;

use Closure;
use Illuminate\View\Component;
use Illuminate\Contracts\View\View;

class StatusCard extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(
        public string $align = '',
        public string $border = '',
        public string $shadow = '',
        public string $iconBorder = '',
        public string $iconBg = '',
        public string $iconColor = '',
        public string $iconShadow = '',
        public string $label = '',
        public string $value = '',
        public string $valueColor = 'text-white',
        public string $description = '',
        public string $icon = '',
    ) {}

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.status-card');
    }
}
