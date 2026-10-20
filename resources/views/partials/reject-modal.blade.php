<div id="reject-modal" class="hidden fixed inset-0 z-[99999] items-center justify-center bg-black/40 p-4">
    <div class="w-full max-w-md rounded-2xl bg-white dark:bg-gray-800 p-6 shadow-lg">
        <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-1">Tolak pengajuan</h3>
        <p id="reject-modal-target" class="text-sm text-gray-500 dark:text-gray-400 mb-4"></p>

        <form id="reject-form" method="POST" action="">
            @csrf
            @method('PATCH')

            <label for="alasan_penolakan" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                Alasan penolakan <span class="text-red-500">*</span>
            </label>
            <textarea
                name="alasan_penolakan"
                id="alasan_penolakan"
                rows="3"
                maxlength="500"
                required
                placeholder="Contoh: barang sedang dipakai untuk kegiatan lain"
                class="w-full rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white px-3 py-2 text-sm mb-4"
            ></textarea>

            <div class="flex justify-end gap-2">
                <button type="button" id="reject-cancel"
                    class="px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 text-sm text-gray-700 dark:text-gray-300">
                    Batal
                </button>
                <button type="submit"
                    class="px-4 py-2 rounded-lg bg-red-600 text-white text-sm hover:bg-red-700">
                    Tolak pengajuan
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const modal = document.getElementById('reject-modal');
    const form = document.getElementById('reject-form');
    const textarea = document.getElementById('alasan_penolakan');
    const target = document.getElementById('reject-modal-target');

    function openModal(action, label) {
        form.action = action;
        target.textContent = label || '';
        textarea.value = '';
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        textarea.focus();
    }

    function closeModal() {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    document.querySelectorAll('.btn-tolak').forEach(btn => {
        btn.addEventListener('click', () => openModal(btn.dataset.action, btn.dataset.label));
    });

    document.getElementById('reject-cancel').addEventListener('click', closeModal);
    modal.addEventListener('click', e => { if (e.target === modal) closeModal(); });
    document.addEventListener('keydown', e => { if (e.key === 'Escape') closeModal(); });
});
</script>
@endpush