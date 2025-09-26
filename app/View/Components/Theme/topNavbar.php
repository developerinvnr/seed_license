<?php

namespace App\View\Components\Theme;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class topNavbar extends Component
{
    public function __construct()
    {
        
    }

    public function render(): View|Closure|string
    {
        return view('components.theme.topNavbar');
    }
}
