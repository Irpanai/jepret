<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-100 leading-tight">
            {{ __('Create New Event') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-gray-800 shadow-xl sm:rounded-2xl border border-gray-700 overflow-hidden p-6 sm:p-8">
                
                <form action="{{ route('fotografer.events.store') }}" method="POST" class="space-y-6">
                    @csrf
                    <div>
                        <label for="nama_event" class="block text-sm font-medium text-gray-300">Event Name</label>
                        <input type="text" name="nama_event" id="nama_event" placeholder="e.g. Jakarta Marathon 2026" class="mt-1 block w-full bg-gray-900 border border-gray-600 rounded-lg text-white px-4 py-2 focus:ring-accent focus:border-accent" required>
                    </div>

                    <div>
                        <label for="lokasi" class="block text-sm font-medium text-gray-300">Location</label>
                        <input type="text" name="lokasi" id="lokasi" placeholder="e.g. GBK Senayan" class="mt-1 block w-full bg-gray-900 border border-gray-600 rounded-lg text-white px-4 py-2 focus:ring-accent focus:border-accent" required>
                    </div>

                    <div>
                        <label for="tanggal_event" class="block text-sm font-medium text-gray-300">Date</label>
                        <input type="date" name="tanggal_event" id="tanggal_event" value="{{ date('Y-m-d') }}" class="mt-1 block w-full bg-gray-900 border border-gray-600 rounded-lg text-white px-4 py-2 focus:ring-accent focus:border-accent [color-scheme:dark]" required>
                    </div>

                    <div class="pt-4 flex justify-end gap-3 border-t border-gray-700 mt-6">
                        <a href="{{ route('fotografer.events.index') }}" class="px-5 py-2.5 rounded-lg text-gray-300 hover:text-white hover:bg-gray-700 font-medium transition">Cancel</a>
                        <button type="submit" class="bg-accent hover:bg-accent-hover text-white px-6 py-2.5 rounded-lg font-semibold transition shadow-lg shadow-accent/20">Create Event</button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>
