<x-guest-layout>
    <div class="mb-4 text-theme-sm text-gray-600">
        Terima kasih sudah mendaftar! Sebelum mulai, mohon verifikasi alamat email Anda dengan
        mengklik link yang baru kami kirim ke email Anda. Jika belum menerima email, kami bisa
        mengirimkannya lagi.
    </div>

    <div class="mb-4 rounded-lg bg-blue-light-50 p-3 text-theme-sm text-blue-light-700">
        Setelah email diverifikasi, akun Anda akan ditinjau dan diaktifkan oleh admin.
        Anda baru bisa masuk ke sistem setelah akun disetujui.
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="mb-4 rounded-lg bg-secondary-50 p-3 text-theme-sm font-medium text-secondary-700">
            Link verifikasi baru sudah dikirim ke alamat email Anda.
        </div>
    @endif

    <div class="mt-6 flex flex-col-reverse items-stretch gap-3 sm:flex-row sm:items-center sm:justify-between">
        <form method="POST" action="{{ route('logout') }}" class="sm:order-1">
            @csrf

            <button type="submit" class="btn btn-ghost w-full sm:w-auto">
                Keluar
            </button>
        </form>

        <form method="POST" action="{{ route('verification.send') }}" class="sm:order-2">
            @csrf

            <button type="submit" class="btn btn-primary w-full sm:w-auto">
                Kirim Ulang Email Verifikasi
            </button>
        </form>
    </div>
</x-guest-layout>