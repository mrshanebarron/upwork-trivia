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
            'url' => 'required|url|max:500',
            'description' => 'nullable|string|max:500',
            'html_content' => 'nullable|string|max:10000',
            'order' => 'nullable|integer|min:0',
            'active' => 'nullable|boolean',
        ]);

        AdBox::create([
            'title' => $validated['title'],
            'url' => $validated['url'],
            'description' => $validated['description'] ?? null,
            'html_content' => $validated['html_content'] ?? null,
            'order' => $validated['order'] ?? 0,
            'is_active' => $request->has('active') && $request->active == '1',
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
            'url' => 'required|url|max:500',
            'description' => 'nullable|string|max:500',
            'html_content' => 'nullable|string|max:10000',
            'order' => 'nullable|integer|min:0',
            'active' => 'nullable|boolean',
        ]);

        $adBox->update([
            'title' => $validated['title'],
            'url' => $validated['url'],
            'description' => $validated['description'] ?? null,
            'html_content' => $validated['html_content'] ?? null,
            'order' => $validated['order'] ?? $adBox->order,
            'is_active' => $request->has('active') && $request->active == '1',
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
