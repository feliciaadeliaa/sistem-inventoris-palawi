import Alpine from 'alpinejs';
import calendarInit from './calendar-init';

window.Alpine = Alpine;

Alpine.start();

// Jalankan calendar init kalau elemen #calendar ada di halaman
document.addEventListener('DOMContentLoaded', () => {
    calendarInit();
});