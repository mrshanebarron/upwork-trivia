<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdBox;
use Illuminate\Http\Request;

class AdBoxController extends Controller
{
    public function index()
    {
        $adBoxes = AdBox::latest()->paginate(15);
        return view('admin.ad-boxes.index', compact('adBoxes'));
    }

    public function create()
    {
        return view('admin.ad-boxes.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'url' => 'required|url',
            'html_content' => 'required|string',
            'is_active' => 'boolean',
            'is_national' => 'boolean',
            'location_name' => 'nullable|string|max:255',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'radius_miles' => 'nullable|integer|min:1|max:500',
            'rotation_order' => 'nullable|integer',
        ]);

        AdBox::create([
            'title' => $validated['title'],
            'url' => $validated['url'],
            'html_content' => $validated['html_content'],
            'is_active' => $validated['is_active'] ?? true,
            'is_national' => $validated['is_national'] ?? false,
            'location_name' => $validated['location_name'] ?? null,
            'latitude' => $validated['latitude'] ?? null,
            'longitude' => $validated['longitude'] ?? null,
            'radius_miles' => $validated['radius_miles'] ?? null,
            'rotation_order' => $validated['rotation_order'] ?? 0,
        ]);

        return redirect()->route('admin.ad-boxes.index')
            ->with('success', 'Ad box created successfully');
    }

    public function edit(AdBox $adBox)
    {
        return view('admin.ad-boxes.edit', compact('adBox'));
    }

    public function update(Request $request, AdBox $adBox)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'url' => 'required|url',
            'html_content' => 'required|string',
            'is_active' => 'boolean',
            'is_national' => 'boolean',
            'location_name' => 'nullable|string|max:255',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'radius_miles' => 'nullable|integer|min:1|max:500',
            'rotation_order' => 'nullable|integer',
        ]);

        $adBox->update([
            'title' => $validated['title'],
            'url' => $validated['url'],
            'html_content' => $validated['html_content'],
            'is_active' => $validated['is_active'] ?? true,
            'is_national' => $validated['is_national'] ?? false,
            'location_name' => $validated['location_name'] ?? null,
            'latitude' => $validated['latitude'] ?? null,
            'longitude' => $validated['longitude'] ?? null,
            'radius_miles' => $validated['radius_miles'] ?? null,
            'rotation_order' => $validated['rotation_order'] ?? 0,
        ]);

        return redirect()->route('admin.ad-boxes.index')
            ->with('success', 'Ad box updated successfully');
    }

    public function destroy(AdBox $adBox)
    {
        $adBox->delete();
        return redirect()->route('admin.ad-boxes.index')
            ->with('success', 'Ad box deleted successfully');
    }
}
