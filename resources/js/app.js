import Alpine from 'alpinejs';
import Chart from 'chart.js/auto';
import Sortable from 'sortablejs';

window.Alpine = Alpine;
window.Chart = Chart;
window.Sortable = Sortable;

// Global Formatters
window.formatRupiah = function(number) {
    if (number === null || number === undefined || number === '') return 'Rp 0';
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
        maximumFractionDigits: 0
    }).format(number);
};

// Formatter untuk input nominal (format titik ribuan: 100.000 / 1.000.000)
window.formatRupiahInput = function(value) {
    if (value === null || value === undefined || value === '') return '';
    let clean = value.toString().replace(/\D/g, '');
    if (!clean) return '';
    return new Intl.NumberFormat('id-ID').format(clean);
};

// Global unformatter (menghilangkan titik/koma menjadi angka murni)
window.cleanNominal = function(value) {
    if (value === null || value === undefined || value === '') return '';
    return value.toString().replace(/\D/g, '');
};

// Alpine Directive: x-money
// Cara pakai di input: <input type="text" x-money inputmode="numeric" placeholder="100.000">
Alpine.directive('money', (el) => {
    let isFormatting = false;

    const format = () => {
        if (isFormatting) return;
        isFormatting = true;
        try {
            let cursor = el.selectionStart;
            let currentVal = el.value !== undefined && el.value !== null ? String(el.value) : '';
            let oldLength = currentVal.length;
            let clean = currentVal.replace(/\D/g, '');

            if (!clean) {
                if (el.value !== '') {
                    el.value = '';
                    el.dispatchEvent(new Event('input', { bubbles: true }));
                }
                return;
            }

            let formatted = new Intl.NumberFormat('id-ID').format(clean);
            if (el.value !== formatted) {
                el.value = formatted;
                if (cursor !== null && typeof el.setSelectionRange === 'function') {
                    let newLength = formatted.length;
                    cursor = cursor + (newLength - oldLength);
                    if (cursor < 0) cursor = 0;
                    try {
                        el.setSelectionRange(cursor, cursor);
                    } catch (e) {}
                }
                // Sinkronkan ke Alpine x-model atau event listener lain
                el.dispatchEvent(new Event('input', { bubbles: true }));
            }
        } finally {
            isFormatting = false;
        }
    };

    // Tombol Backspace pintar: jika kursor berada tepat setelah titik (separator),
    // hapus angka sebelum titik agar pengguna tidak merasa kursor macet.
    el.addEventListener('keydown', (e) => {
        if (e.key === 'Backspace' && el.selectionStart === el.selectionEnd && el.selectionStart > 0) {
            let pos = el.selectionStart;
            let val = el.value || '';
            if (val[pos - 1] === '.') {
                e.preventDefault();
                if (pos >= 2) {
                    el.value = val.slice(0, pos - 2) + val.slice(pos - 1);
                    el.setSelectionRange(pos - 2, pos - 2);
                    format();
                }
            }
        }
    });

    el.addEventListener('input', format);
    el.addEventListener('change', format);
    el.addEventListener('focus', format);
    el.addEventListener('blur', format);
    el.addEventListener('paste', () => setTimeout(format, 10));

    // Inisialisasi saat load dan saat modal tampil
    setTimeout(format, 15);
});

Alpine.start();
