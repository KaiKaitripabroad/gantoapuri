<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class AppLayout extends Component
{
    public function __construct(
        public string $bottomNavActive = '',
        public bool $hideBottomNav = false,
    ) {}

    public function render(): View
    {
        return view('layouts.app', [
            'bottomNavActive' => $this->bottomNavActive,
            'hideBottomNav' => $this->hideBottomNav,
        ]);
    }
}
