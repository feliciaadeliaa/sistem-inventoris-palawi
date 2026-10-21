<div id="reject-modal" class="hidden fixed inset-0 z-[99999] items-center justify-center bg-black/40 p-4"
     role="dialog" aria-modal="true" aria-labelledby="reject-modal-title">
    <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-theme-xl">
        <h3 id="reject-modal-title" class="panel-title mb-1">Tolak pengajuan</h3>
        <p id="reject-modal-target" class="page-desc mb-4 mt-0"></p>

        <form id="reject-form" method="POST" action="">
            @csrf
            @method('PATCH')

            <label for="alasan_penolakan" class="form-label">
                Alasan penolakan <span class="text-error-600">*</span>
            </label>
            <textarea
                name="alasan_penolakan"
                id="alasan_penolakan"
                rows="3"
                minlength="5"
                maxlength="500"
                required
                placeholder="Contoh: barang sedang dipakai untuk kegiatan lain"
                class="form-control h-auto py-2 mb-4"
            ></textarea>

            <div class="flex justify-end gap-2">
                <button type="button" id="reject-cancel" class="btn btn-sm btn-outline">
                    Batal
                </button>
                <button type="submit" id="reject-submit" class="btn btn-sm btn-danger">
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
    const submitBtn = document.getElementById('reject-submit');

    function openModal(action, label) {
        form.action = action;
        target.textContent = label || '';
        textarea.value = '';
        submitBtn.disabled = false;
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

    // Cegah double submit
    form.addEventListener('submit', () => { submitBtn.disabled = true; });

    document.getElementById('reject-cancel').addEventListener('click', closeModal);
    modal.addEventListener('click', e => { if (e.target === modal) closeModal(); });
    document.addEventListener('keydown', e => { if (e.key === 'Escape') closeModal(); });
});
</script>
@endpush