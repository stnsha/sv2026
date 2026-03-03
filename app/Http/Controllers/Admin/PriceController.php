<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Price;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PriceController extends Controller
{
    public function index(): View
    {
        $prices = Price::orderBy('id')->get();

        return view('admin.prices.index', compact('prices'));
    }

    public function update(Request $request): RedirectResponse
    {
        $prices = Price::all();

        foreach ($prices as $price) {
            $price->update([
                'is_active' => $request->boolean("prices.{$price->id}.is_active"),
            ]);
        }

        return redirect()->route('admin.prices.index')->with('success', 'Price settings updated.');
    }
}
