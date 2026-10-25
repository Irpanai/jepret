<time
    class="hidden text-right text-xs font-semibold leading-5 text-gray-500 md:block"
    x-data="{
        value: '',
        timer: null,
        update() {
            const parts = new Intl.DateTimeFormat('id-ID', {
                timeZone: 'Asia/Makassar',
                weekday: 'long',
                day: 'numeric',
                month: 'long',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit',
                hour12: false,
            }).formatToParts(new Date());
            const part = (type) => parts.find((item) => item.type === type)?.value ?? '';
            this.value = `${part('weekday')}, ${part('day')} ${part('month')} ${part('year')} | ${part('hour')}:${part('minute')}:${part('second')} WITA`;
        },
        init() {
            this.update();
            this.timer = setInterval(() => this.update(), 1000);
        },
        destroy() {
            clearInterval(this.timer);
        },
    }"
    x-text="value"
    aria-label="Waktu Indonesia Tengah saat ini"
></time>
