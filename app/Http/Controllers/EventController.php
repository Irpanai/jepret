<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function index(Request $request)
    {
        $events = Event::where('fotografer_id', $request->user()->id)->withCount('photos')->latest()->get();

        return view('fotografer.events.index', compact('events'));
    }

    public function create()
    {
        return view('fotografer.events.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_event' => 'required|string|max:255',
            'lokasi' => 'required|string|max:255',
            'tanggal_event' => 'required|date',
        ]);

        $event = Event::create([
            'fotografer_id' => $request->user()->id,
            'nama_event' => $request->nama_event,
            'lokasi' => $request->lokasi,
            'tanggal_event' => $request->tanggal_event,
        ]);

        return redirect()->route('fotografer.events.show', $event)->with('success', 'Event created successfully.');
    }

    public function show(Event $event)
    {
        if ($event->fotografer_id !== auth()->id()) {
            abort(403);
        }

        $photos = $event->photos()->latest()->paginate(24);

        return view('fotografer.events.show', compact('event', 'photos'));
    }

    public function edit(Event $event)
    {
        if ($event->fotografer_id !== auth()->id()) {
            abort(403);
        }

        return view('fotografer.events.edit', compact('event'));
    }

    public function update(Request $request, Event $event)
    {
        if ($event->fotografer_id !== auth()->id()) {
            abort(403);
        }

        $request->validate([
            'nama_event' => 'required|string|max:255',
            'lokasi' => 'required|string|max:255',
            'tanggal_event' => 'required|date',
        ]);

        $event->update($request->only('nama_event', 'lokasi', 'tanggal_event'));

        return redirect()->route('fotografer.events.index')->with('success', 'Event updated.');
    }

    public function destroy(Event $event)
    {
        if ($event->fotografer_id !== auth()->id()) {
            abort(403);
        }
        $event->delete();

        return redirect()->route('fotografer.events.index')->with('success', 'Event deleted.');
    }
}
