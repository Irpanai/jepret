export default (initialWatermarkUrl = null) => ({
    files: [],
    activeIndex: 0,
    watermarkUrl: initialWatermarkUrl,
    uploadedWatermarkUrl: null,
    dragging: false,
    applyNotice: false,
    applyNoticeTimeout: null,

    get activeFile() {
        return this.files[this.activeIndex] ?? null;
    },

    selectFiles(event) {
        this.revokeFileUrls();
        this.files = Array.from(event.target.files).map((file, index) => ({
            file,
            id: `${file.name}-${file.size}-${file.lastModified}-${index}`,
            url: URL.createObjectURL(file),
            isVideo: file.type.startsWith('video/'),
            title: file.name.replace(/\.[^/.]+$/, ''),
            settings: { x: 50, y: 85, scale: 30, opacity: 100 },
        }));
        this.activeIndex = 0;
    },

    selectWatermark(event) {
        if (this.uploadedWatermarkUrl) {
            URL.revokeObjectURL(this.uploadedWatermarkUrl);
        }

        const [file] = event.target.files;
        this.uploadedWatermarkUrl = file ? URL.createObjectURL(file) : null;
        this.watermarkUrl = this.uploadedWatermarkUrl || initialWatermarkUrl;
    },

    removeFile(index) {
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

    revokeFileUrls() {
        this.files.forEach((file) => URL.revokeObjectURL(file.url));
    },
});
