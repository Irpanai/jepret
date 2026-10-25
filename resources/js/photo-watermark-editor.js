export default (configuration = {}) => ({
    files: [],
    activeIndex: 0,
    watermarkUrl: configuration.watermarkUrl ?? null,
    uploadedWatermarkUrl: null,
    usedBytes: Number(configuration.usedBytes ?? 0),
    quotaBytes: Number(configuration.quotaBytes ?? 0),
    dragging: false,
    uploading: false,
    uploadError: null,
    applyNotice: false,
    applyNoticeTimeout: null,

    get activeFile() {
        return this.files[this.activeIndex] ?? null;
    },

    get selectedBytes() {
        return this.files.reduce((total, item) => total + item.file.size, 0);
    },

    get pendingBytes() {
        return this.files.filter((item) => ['queued', 'error'].includes(item.status)).reduce((total, item) => total + item.file.size, 0);
    },

    get remainingBytes() {
        return Math.max(0, this.quotaBytes - this.usedBytes);
    },

    get projectedBytes() {
        return this.usedBytes + this.pendingBytes;
    },

    get exceedsQuota() {
        return this.pendingBytes > this.remainingBytes;
    },

    get hasPendingFiles() {
        return this.files.some((item) => ['queued', 'error'].includes(item.status));
    },

    get uploadProgress() {
        if (this.selectedBytes === 0) {
            return 0;
        }

        const uploadedBytes = this.files.reduce((total, item) => {
            if (item.status === 'done') {
                return total + item.file.size;
            }

            if (item.status === 'uploading') {
                return total + (item.file.size * item.progress / 100);
            }

            return total;
        }, 0);

        return Math.max(this.uploading ? 1 : 0, Math.round(uploadedBytes / this.selectedBytes * 100));
    },

    selectFiles(event) {
        this.revokeFileUrls();
        this.files = Array.from(event.target.files).map((file, index) => ({
            file,
            id: `${file.name}-${file.size}-${file.lastModified}-${index}`,
            url: URL.createObjectURL(file),
            title: file.name.replace(/\.[^/.]+$/, ''),
            settings: { x: 50, y: 85, scale: 30, opacity: 100 },
            token: crypto.randomUUID(),
            status: 'queued',
            progress: 0,
            error: null,
            request: null,
        }));
        this.activeIndex = 0;
        this.uploadError = null;
    },

    selectWatermark(event) {
        if (this.uploadedWatermarkUrl) {
            URL.revokeObjectURL(this.uploadedWatermarkUrl);
        }

        const [file] = event.target.files;
        this.uploadedWatermarkUrl = file ? URL.createObjectURL(file) : null;
        this.watermarkUrl = this.uploadedWatermarkUrl || configuration.watermarkUrl || null;
    },

    removeFile(index) {
        if (this.files[index]?.status === 'done') {
            return;
        }

        this.files[index]?.request?.abort();
        URL.revokeObjectURL(this.files[index].url);
        this.files.splice(index, 1);

        if (index < this.activeIndex) {
            this.activeIndex--;
        }

        const transfer = new DataTransfer();
        this.files.forEach((item) => transfer.items.add(item.file));
        this.$refs.photoInput.files = transfer.files;
        this.activeIndex = Math.min(this.activeIndex, Math.max(0, this.files.length - 1));
    },

    cancelFile(index) {
        this.removeFile(index);
    },

    async uploadAll(event) {
        if (this.uploading || this.exceedsQuota || this.files.length === 0) {
            return;
        }

        this.uploading = true;
        this.uploadError = null;

        for (const item of [...this.files]) {
            if (!this.files.includes(item) || !['queued', 'error'].includes(item.status)) {
                continue;
            }

            await this.uploadItem(event.currentTarget, item);

            if (item.status === 'error') {
                break;
            }
        }

        this.uploading = false;
    },

    async uploadItem(form, item) {
        item.status = 'uploading';
        item.progress = 0;
        item.error = null;

        const data = new FormData(form);
        Array.from(data.keys()).forEach((key) => {
            if (key === 'photos[]' || key.startsWith('watermark_settings[') || key.startsWith('photo_titles[')) {
                data.delete(key);
            }
        });
        data.append('photos[]', item.file);
        data.append('upload_token', item.token);
        Object.entries(item.settings).forEach(([key, value]) => data.append(`watermark_settings[0][${key}]`, value));
        data.append('photo_titles[0]', item.title);

        try {
            const payload = await this.send(form.action, data, item);
            item.status = 'done';
            item.progress = 100;
            this.usedBytes = Number(payload.used_bytes);
        } catch (error) {
            if (item.status !== 'cancelled') {
                item.status = 'error';
                item.error = error.message;
                this.uploadError = error.message;
            }
        } finally {
            item.request = null;
        }
    },

    send(url, data, item) {
        return new Promise((resolve, reject) => {
            const request = new XMLHttpRequest();
            item.request = request;
            request.open('POST', url);
            request.setRequestHeader('Accept', 'application/json');
            request.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
            request.upload.addEventListener('progress', (event) => {
                if (event.lengthComputable) {
                    item.progress = Math.round((event.loaded / event.total) * 100);
                }
            });
            request.addEventListener('load', () => {
                let payload = {};
                try {
                    payload = JSON.parse(request.responseText);
                } catch {
                    reject(new Error('Respons upload tidak dapat dibaca.'));
                    return;
                }

                if (request.status >= 200 && request.status < 300) {
                    resolve(payload);
                    return;
                }

                const message = Object.values(payload.errors ?? {}).flat()[0] ?? payload.message ?? 'Upload gagal. Coba lagi.';
                reject(new Error(message));
            });
            request.addEventListener('error', () => reject(new Error('Koneksi terputus saat upload.')));
            request.addEventListener('abort', () => {
                item.status = 'cancelled';
                reject(new Error('Upload dibatalkan.'));
            });
            request.send(data);
        });
    },

    startDrag(event) {
        if (!this.activeFile || !this.watermarkUrl) {
            return;
        }

        this.dragging = true;
        event.currentTarget.setPointerCapture(event.pointerId);
        this.moveWatermark(event);
    },

    moveWatermark(event) {
        if (!this.dragging || !this.activeFile) {
            return;
        }

        const bounds = this.$refs.stage.getBoundingClientRect();
        this.activeFile.settings.x = this.clamp(((event.clientX - bounds.left) / bounds.width) * 100, 0, 100);
        this.activeFile.settings.y = this.clamp(((event.clientY - bounds.top) / bounds.height) * 100, 0, 100);
    },

    stopDrag() {
        this.dragging = false;
    },

    applyToAll() {
        if (!this.activeFile) {
            return;
        }

        const settings = { ...this.activeFile.settings };
        this.files.forEach((file) => {
            file.settings = { ...settings };
        });

        this.applyNotice = true;
        clearTimeout(this.applyNoticeTimeout);
        this.applyNoticeTimeout = setTimeout(() => {
            this.applyNotice = false;
        }, 2500);
    },

    resetActive() {
        if (this.activeFile) {
            this.activeFile.settings = { x: 50, y: 85, scale: 30, opacity: 100 };
        }
    },

    clamp(value, minimum, maximum) {
        return Math.round(Math.min(maximum, Math.max(minimum, value)) * 100) / 100;
    },

    formatBytes(bytes) {
        if (bytes < 1048576) {
            return `${(bytes / 1024).toFixed(1)} KB`;
        }

        if (bytes < 1073741824) {
            return `${(bytes / 1048576).toFixed(2)} MB`;
        }

        return `${(bytes / 1073741824).toFixed(2)} GB`;
    },

    revokeFileUrls() {
        this.files.forEach((file) => URL.revokeObjectURL(file.url));
    },

    destroy() {
        this.files.forEach((item) => item.request?.abort());
        this.revokeFileUrls();
        if (this.uploadedWatermarkUrl) {
            URL.revokeObjectURL(this.uploadedWatermarkUrl);
        }
        clearTimeout(this.applyNoticeTimeout);
    },
});
