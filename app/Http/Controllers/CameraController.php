<?php

namespace App\Http\Controllers;

use App\Models\Camera;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class CameraController extends Controller
{
    public function index(Request $request): View
    {
        $cameras = Camera::where('fotografer_id', $request->user()->id)
            ->orderByDesc('created_at')
            ->get();

        return view('fotografer.cameras.index', compact('cameras'));
    }

    public function create(): View
    {
        return view('fotografer.cameras.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'brand_model' => 'required|string|max:255',
            'lens' => 'nullable|string|max:255',
            'photo' => 'nullable|image|mimes:jpg,jpeg,png|max:4096',
            'notes' => 'nullable|string|max:1000',
        ]);

        if ($request->hasFile('photo')) {
            $data['photo_path'] = $request->file('photo')->store('cameras', 'public');
        }

        unset($data['photo']);
        $data['fotografer_id'] = $request->user()->id;

        Camera::create($data);

        return redirect()->route('fotografer.cameras.index')->with('success', 'Camera saved.');
    }

    public function edit(Request $request, Camera $camera): View
    {
        abort_if($camera->fotografer_id !== $request->user()->id, 403);

        return view('fotografer.cameras.edit', compact('camera'));
    }

    public function update(Request $request, Camera $camera): RedirectResponse
    {
        abort_if($camera->fotografer_id !== $request->user()->id, 403);

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'brand_model' => 'required|string|max:255',
            'lens' => 'nullable|string|max:255',
            'photo' => 'nullable|image|mimes:jpg,jpeg,png|max:4096',
            'notes' => 'nullable|string|max:1000',
        ]);

        if ($request->hasFile('photo')) {
            if ($camera->photo_path) {
                Storage::disk('public')->delete($camera->photo_path);
            }

            $data['photo_path'] = $request->file('photo')->store('cameras', 'public');
        }

        unset($data['photo']);
        $camera->update($data);

        return redirect()->route('fotografer.cameras.index')->with('success', 'Camera updated.');
    }

    public function destroy(Request $request, Camera $camera): RedirectResponse
    {
        abort_if($camera->fotografer_id !== $request->user()->id, 403);

        if ($camera->photos()->exists()) {
            return back()->withErrors(['camera' => 'Camera masih dipakai oleh foto. Ganti metadata foto terlebih dahulu sebelum menghapus.']);
        }

        if ($camera->photo_path) {
            Storage::disk('public')->delete($camera->photo_path);
        }

        $camera->delete();

        return redirect()->route('fotografer.cameras.index')->with('success', 'Camera deleted.');
    }
}
