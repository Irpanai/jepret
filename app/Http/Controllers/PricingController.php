<?php

namespace App\Http\Controllers;

use App\Models\Package;
use Illuminate\View\View;

class PricingController extends Controller
{
    public function __invoke(): View
    {
        $pricingPlans = Package::query()->publiclyAvailable()->ordered()->get();

        return view('pricing', compact('pricingPlans'));
    }
}
