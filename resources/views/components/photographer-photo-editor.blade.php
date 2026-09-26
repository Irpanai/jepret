<div class="space-y-5">
    <div>
        <div class="flex items-center justify-between gap-4">
            <div>
                <h2>Upload Content</h2>
                <p class="helper mt-1">Pilih foto, lalu geser watermark langsung pada preview.</p>
            </div>
            <span class="rounded-full bg-gray-100 px-3 py-1 text-xs font-semibold text-gray-600" x-show="files.length" x-text="`${files.length} file`"></span>
        </div>
        <input class="mt-4 w-full rounded-xl border border-dashed border-gray-400 p-8 text-sm" type="file" name="photos[]" accept="image/jpeg,image/png,image/webp,video/quicktime" multiple required x-ref="photoInput" @change="selectFiles($event)">
    </div>

    <div class="rounded-xl border border-dashed border-gray-300 bg-gray-50 p-8 text-center" x-show="!files.length">
        <p class="text-sm font-semibold text-gray-700">Preview editor akan muncul di sini</p>
        <p class="helper mt-1">Foto tetap diproses dalam resolusi aslinya.</p>
    </div>

    <template x-if="activeFile">
    <div class="grid gap-5 xl:grid-cols-[minmax(0,1fr)_15rem]" x-cloak>
        <div class="space-y-4">
            <div class="relative mx-auto w-full touch-none select-none overflow-hidden rounded-xl bg-gray-950 leading-none shadow-sm" x-ref="stage" @pointerdown.prevent="startDrag($event)" @pointermove.prevent="moveWatermark($event)" @pointerup="stopDrag()" @pointercancel="stopDrag()">
                <template x-if="activeFile && !activeFile.isVideo">
                    <img class="block h-auto w-full" :src="activeFile.url" alt="Preview foto" draggable="false">
                </template>
                <template x-if="activeFile && activeFile.isVideo">
                    <video class="block h-auto w-full" :src="activeFile.url" muted loop></video>
                </template>
                <img x-show="watermarkUrl" class="pointer-events-none absolute h-auto max-w-none -translate-x-1/2 -translate-y-1/2" :class="dragging ? 'drop-shadow-lg' : ''" :src="watermarkUrl" alt="Watermark" :style="`left:${activeFile?.settings.x ?? 50}%;top:${activeFile?.settings.y ?? 85}%;width:${activeFile?.settings.scale ?? 30}%;opacity:${(activeFile?.settings.opacity ?? 100) / 100}`" draggable="false">
                <div class="pointer-events-none absolute left-3 top-3 rounded-lg bg-black/65 px-3 py-2 text-xs font-semibold leading-none text-white">Klik atau geser untuk mengatur posisi</div>
            </div>

            <div class="grid gap-4 rounded-xl border bg-white p-4 sm:grid-cols-2">
                <label class="text-sm font-semibold text-gray-700 sm:col-span-2">
                    Nama foto
                    <input class="mt-2 w-full rounded-lg border-gray-300 text-sm" type="text" maxlength="255" x-model="activeFile.title" placeholder="Gunakan nama file asli">
                    <span class="mt-1 block text-xs font-normal text-gray-500">Kosongkan untuk menggunakan nama file asli.</span>
                </label>
                <label class="text-sm font-semibold text-gray-700">
                    Ukuran <span class="float-right text-gray-500" x-text="`${activeFile?.settings.scale ?? 30}%`"></span>
                    <input class="mt-2 w-full accent-black" type="range" min="5" max="80" step="1" x-model.number="activeFile.settings.scale">
                </label>
                <label class="text-sm font-semibold text-gray-700">
                    Opacity <span class="float-right text-gray-500" x-text="`${activeFile?.settings.opacity ?? 100}%`"></span>
                    <input class="mt-2 w-full accent-black" type="range" min="5" max="100" step="1" x-model.number="activeFile.settings.opacity">
                </label>
                <div class="flex flex-wrap gap-2 sm:col-span-2">
                    <button class="rounded-lg border px-3 py-2 text-xs font-semibold hover:bg-gray-50" type="button" @click="resetActive()">Reset</button>
                    <button class="rounded-lg border border-black bg-black px-3 py-2 text-xs font-semibold text-white" type="button" @click="applyToAll()">Terapkan ke semua</button>
                </div>
                <p class="rounded-lg bg-green-50 px-3 py-2 text-xs font-semibold text-green-700 sm:col-span-2" x-show="applyNotice" x-transition.opacity role="status" aria-live="polite">
                    Watermark diterapkan ke semua foto.
                </p>
            </div>
        </div>

        <div class="grid min-w-0 max-h-[42rem] content-start gap-2 overflow-x-hidden overflow-y-auto pr-1">
            <template x-for="(item, index) in files" :key="item.id">
                <div class="relative min-w-0 overflow-hidden rounded-xl border transition" :class="activeIndex === index ? 'border-black bg-black text-white' : 'border-gray-200 bg-white hover:border-gray-400'">
                    <button class="flex min-w-0 w-full items-center gap-3 p-2 pr-11 text-left" type="button" @click="activeIndex = index">
                        <div class="h-14 w-16 shrink-0 overflow-hidden rounded-lg bg-gray-200">
                            <img class="h-full w-full object-cover" x-show="!item.isVideo" :src="item.url" alt="">
                            <video class="h-full w-full object-cover" x-show="item.isVideo" :src="item.url"></video>
                        </div>
                        <span class="min-w-0 flex-1">
                            <span class="block break-all text-xs font-semibold leading-4" x-text="item.title || item.file.name.replace(/\.[^/.]+$/, '')"></span>
                            <span class="mt-1 block text-[11px] opacity-70" x-text="`${(item.file.size / 1048576).toFixed(2)} MB`"></span>
                        </span>
                    </button>
                    <button class="absolute right-2 top-2 flex h-7 w-7 items-center justify-center rounded-full bg-white text-lg leading-none text-gray-700 shadow hover:bg-red-50 hover:text-red-700" type="button" :aria-label="`Batalkan upload ${item.file.name}`" title="Batalkan upload" @click.stop="removeFile(index)">×</button>
                </div>
            </template>
        </div>
    </div>
    </template>

    <template x-for="(item, index) in files" :key="`settings-${item.id}`">
        <div>
            <input type="hidden" :name="`watermark_settings[${index}][x]`" :value="item.settings.x">
            <input type="hidden" :name="`watermark_settings[${index}][y]`" :value="item.settings.y">
            <input type="hidden" :name="`watermark_settings[${index}][scale]`" :value="item.settings.scale">
            <input type="hidden" :name="`watermark_settings[${index}][opacity]`" :value="item.settings.opacity">
            <input type="hidden" :name="`photo_titles[${index}]`" :value="item.title">
        </div>
    </template>
</div>
