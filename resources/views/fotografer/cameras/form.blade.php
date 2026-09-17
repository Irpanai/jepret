@if($errors->any())
    <div class="p-4 bg-red-50 border border-red-200 rounded-xl text-sm font-bold text-red-700">{{ $errors->first() }}</div>
@endif

<div>
    <label for="name" class="block text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-2">Nama Internal</label>
    <input id="name" name="name" value="{{ old('name', $camera?->name) }}" required class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm font-medium focus:ring-black focus:border-black" placeholder="Contoh: Kamera utama CFD">
</div>

<div>
    <label for="brand_model" class="block text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-2">Body Kamera</label>
    <input id="brand_model" name="brand_model" value="{{ old('brand_model', $camera?->brand_model) }}" required class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm font-medium focus:ring-black focus:border-black" placeholder="Contoh: Sony A7 IV">
</div>

<div>
    <label for="lens" class="block text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-2">Lensa</label>
    <input id="lens" name="lens" value="{{ old('lens', $camera?->lens) }}" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm font-medium focus:ring-black focus:border-black" placeholder="Contoh: 70-200mm f/2.8">
</div>

<div>
    <label for="photo" class="block text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-2">Foto Kamera</label>
    <input id="photo" name="photo" type="file" accept="image/jpeg,image/png,image/jpg" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm font-medium focus:ring-black focus:border-black">
</div>

<div>
    <label for="notes" class="block text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-2">Catatan</label>
    <textarea id="notes" name="notes" rows="4" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm font-medium focus:ring-black focus:border-black" placeholder="Kondisi setup, focal range favorit, atau catatan operasional">{{ old('notes', $camera?->notes) }}</textarea>
</div>
