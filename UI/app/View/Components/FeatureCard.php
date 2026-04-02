<?php

namespace App\View\Components;

use Closure;
use Illuminate\View\Component;
use Illuminate\Contracts\View\View;

class FeatureCard extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(
        public string $title,
        public string $description,
        public string $border,
        public string $shadow,
        public string $iconBorder,
        public string $iconBg,
        public string $iconColor,
        public string $iconShadow,
        public string $icon
    ) {
        
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.feature-card');
    }
}
