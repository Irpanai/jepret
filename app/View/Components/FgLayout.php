<?php

namespace App\View\Components;

use App\PhotographerNotificationFeed;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\Component;
use Illuminate\View\View;

class FgLayout extends Component
{
    /**
     * Get the view / contents that represents the component.
     */
    public function render(): View
    {
        $notifications = Auth::check() && Auth::user()->role === 'fotografer'
            ? PhotographerNotificationFeed::forUser(Auth::user())
            : [];

        return view('layouts.fg', compact('notifications'));
    }
}
