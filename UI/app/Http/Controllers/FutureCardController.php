<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class FutureCardController extends Controller
{
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
    ) {}
}
