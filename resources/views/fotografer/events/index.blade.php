<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center w-full">
            <h2 class="font-semibold text-xl text-gray-100 leading-tight">
                {{ __('Event Management') }}
            </h2>
            <a href="{{ route('fotografer.events.create') }}" class="bg-accent hover:bg-accent-hover text-white px-4 py-2 rounded-lg font-semibold transition">
                + Create Event
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-gray-800 shadow-xl sm:rounded-2xl border border-gray-700 overflow-hidden">
                <div class="p-6 border-b border-gray-700">
                    <h3 class="text-xl font-bold text-white">All Events</h3>
                </div>
                
                @if($events->isEmpty())
                    <div class="p-12 text-center">
                        <svg class="w-16 h-16 text-gray-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        <p class="text-gray-400 text-lg">No events found.</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm text-gray-400">
                            <thead class="bg-gray-900/50 text-xs uppercase text-gray-500 border-b border-gray-700">
                                <tr>
                                    <th scope="col" class="px-6 py-3">Event Name</th>
                                    <th scope="col" class="px-6 py-3">Date</th>
                                    <th scope="col" class="px-6 py-3">Location</th>
                                    <th scope="col" class="px-6 py-3 text-center">Total Photos</th>
                                    <th scope="col" class="px-6 py-3 text-right">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($events as $event)
                                    <tr class="border-b border-gray-700/50 hover:bg-gray-700/30 transition">
                                        <th scope="row" class="px-6 py-4 font-medium text-white whitespace-nowrap">
                                            {{ $event->nama_event }}
                                        </th>
                                        <td class="px-6 py-4">
                                            {{ \Carbon\Carbon::parse($event->tanggal_event)->format('d M Y') }}
                                        </td>
                                        <td class="px-6 py-4">
                                            {{ $event->lokasi }}
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            <span class="inline-flex items-center justify-center px-3 py-1 text-xs font-bold leading-none text-accent bg-accent/10 rounded-full">
                                                {{ $event->photos_count }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-right space-x-3">
                                            <a href="{{ route('fotografer.events.show', $event) }}" class="font-medium text-accent hover:text-accent-hover">Manage Photos</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
