<?php

namespace App\Http\Controllers;

use App\Models\CoffeeType;
use Illuminate\Http\Request;

class CoffeeTypeController extends Controller
{
    public function index()
    {
        $coffeeTypes = CoffeeType::orderBy('category')->orderBy('name')->get();
        return view('coffee-types.index', compact('coffeeTypes'));
    }

    public function create()
    {
        return view('coffee-types.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|in:robusta,arabika',
        ]);

        CoffeeType::create($validated);

        return redirect()->route('coffee-types.index')->with('success', 'Jenis kopi berhasil ditambahkan.');
    }

    public function edit(CoffeeType $coffeeType)
    {
        return view('coffee-types.edit', compact('coffeeType'));
    }

    public function update(Request $request, CoffeeType $coffeeType)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|in:robusta,arabika',
        ]);

        $coffeeType->update($validated);

        return redirect()->route('coffee-types.index')->with('success', 'Jenis kopi berhasil diupdate.');
    }

    public function destroy(CoffeeType $coffeeType)
    {
        $coffeeType->delete();
        return redirect()->route('coffee-types.index')->with('success', 'Jenis kopi berhasil dihapus.');
    }
}
