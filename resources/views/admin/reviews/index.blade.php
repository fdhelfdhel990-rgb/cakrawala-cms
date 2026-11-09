@extends('layouts.admin')

@section('title', 'Kelola Ulasan')
@section('breadcrumb_title', 'Kelola Ulasan')

@section('content')
<div class="space-y-6" x-data="{
    replyModalOpen: false,
    replyReviewId: null,
    replyReviewComment: '',
    replyReviewUser: '',
    replyText: '',
    openReply(id, comment, user, existingReply) {
        this.replyReviewId = id;
        this.replyReviewComment = comment;
        this.replyReviewUser = user;
        this.replyText = existingReply || '';
        this.replyModalOpen = true;
    }
}">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <p class="text-[11px] font-bold text-[#0B132B] uppercase tracking-wider mb-1">INTERAKSI PEMBACA</p>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-[#1E293B] tracking-tight">Kelola Ulasan & Rating</h1>
            <p class="text-xs sm:text-sm text-[#64748B] font-normal mt-1">Lihat testimoni dan tanggapi ulasan rating bintang dari para pembaca buku.</p>
        </div>
        
        <span class="text-xs text-[#64748B] font-medium px-4 py-2 bg-white rounded-xl border border-[#E2E8F0] shadow-2xs self-start sm:self-auto">
            Total: <strong class="text-[#1E293B]">{{ $reviews->total() }}</strong> ulasan pembaca
        </span>
    </div>

    <!-- Card Container -->
    <div class="card-system p-6 sm:p-7 space-y-5">
        <!-- Filter Bar Sederhana -->
        <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
            <form method="GET" action="{{ route('admin.reviews.index') }}" class="flex flex-col sm:flex-row items-center gap-3 w-full sm:w-auto">
                <select name="rating" class="w-full sm:w-60 py-2.5 px-4 rounded-xl border border-[#E2E8F0] text-xs text-[#1E293B] focus:outline-none focus:ring-2 focus:ring-[#1A43BF] bg-white font-medium">
                    <option value="">Semua Bintang Rating</option>
                    <option value="5" {{ request('rating') == 5 ? 'selected' : '' }}>5 Bintang (★★★★★)</option>
                    <option value="4" {{ request('rating') == 4 ? 'selected' : '' }}>4 Bintang (★★★★☆)</option>
                    <option value="3" {{ request('rating') == 3 ? 'selected' : '' }}>3 Bintang (★★★☆☆)</option>
                    <option value="2" {{ request('rating') == 2 ? 'selected' : '' }}>2 Bintang (★★☆☆☆)</option>
                    <option value="1" {{ request('rating') == 1 ? 'selected' : '' }}>1 Bintang (★☆☆☆☆)</option>
                </select>

                <button type="submit" class="w-full sm:w-auto inline-flex items-center justify-center gap-1.5 px-5 py-2.5 bg-[#1A43BF] hover:bg-[#15379e] text-white rounded-[8px] text-xs font-bold transition shadow-xs cursor-pointer">
                    <span class="material-symbols-rounded text-base">filter_alt</span>
                    <span>Saring Ulasan</span>
                </button>
            </form>

            <span class="text-xs text-[#64748B]">
                Menampilkan halaman {{ $reviews->currentPage() }} dari {{ $reviews->lastPage() }}
            </span>
        </div>

        <!-- Tabel Ulasan Sederhana: PEMBACA, RATING, BUKU, KOMENTAR & BALASAN, TANGGAL, AKSI -->
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-[#F8FAFC] text-[#64748B] font-bold uppercase tracking-wider text-[11px] rounded-xl">
                        <th class="py-3 px-4 rounded-l-xl">PEMBACA</th>
                        <th class="py-3 px-4">RATING</th>
                        <th class="py-3 px-4">JUDUL BUKU</th>
                        <th class="py-3 px-4">KOMENTAR & BALASAN</th>
                        <th class="py-3 px-4">TANGGAL</th>
                        <th class="py-3 px-4 text-center rounded-r-xl">AKSI</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse($reviews as $review)
                        <tr class="hover:bg-slate-50/70 transition">
                            <!-- Pembaca -->
                            <td class="py-3.5 px-4">
                                <p class="font-bold text-[#1E293B] text-xs">{{ $review->user->name ?? 'Pembaca' }}</p>
                                <p class="text-[11px] text-[#64748B]">{{ $review->user->email ?? '-' }}</p>
                            </td>

                            <!-- Rating -->
                            <td class="py-3.5 px-4">
                                <span class="inline-flex items-center gap-1 font-bold text-amber-500 bg-amber-50 px-2.5 py-1 rounded-md border border-amber-200 text-xs">
                                    ★ {{ number_format($review->rating, 1) }}
                                </span>
                            </td>

                            <!-- Judul Buku -->
                            <td class="py-3.5 px-4 font-bold text-[#1E293B]">
                                {{ $review->book ? $review->book->title : 'Aplikasi Cakrawala' }}
                            </td>

                            <!-- Komentar & Balasan -->
                            <td class="py-3.5 px-4 max-w-sm">
                                <p class="text-[#1E293B] italic leading-relaxed text-xs">"{{ $review->comment ?? 'Tidak ada komentar tertulis.' }}"</p>
                                @if($review->reply)
                                    <div class="mt-2 p-2.5 rounded-lg bg-[#EFF6FF] border border-blue-200 text-[11px] text-[#1A43BF]">
                                        <span class="font-bold block">Balasan Pengelola:</span>
                                        {{ $review->reply }}
                                    </div>
                                @endif
                            </td>

                            <!-- Tanggal -->
                            <td class="py-3.5 px-4 text-[#64748B]">
                                {{ $review->created_at ? $review->created_at->format('d M Y') : '-' }}
                            </td>

                            <!-- Aksi: [ Balas ] [ Hapus ] -->
                            <td class="py-3.5 px-4 text-center">
                                <div class="inline-flex items-center gap-2">
                                    <button type="button" 
                                            @click="openReply({{ $review->id }}, '{{ addslashes($review->comment ?? '') }}', '{{ addslashes($review->user->name ?? 'Pembaca') }}', '{{ addslashes($review->reply ?? '') }}')"
                                            class="inline-flex items-center gap-1 px-3.5 py-1.5 rounded-[8px] bg-[#1A43BF] hover:bg-[#15379e] text-white font-bold text-xs transition shadow-xs cursor-pointer">
                                        <span class="material-symbols-rounded text-xs">reply</span>
                                        <span>{{ $review->reply ? 'Edit Balasan' : 'Balas' }}</span>
                                    </button>

                                    <button type="button" 
                                            @click="confirmDelete('{{ route('admin.reviews.destroy', $review->id) }}', 'Ulasan dari {{ addslashes($review->user->name ?? 'Pembaca') }}')"
                                            class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-[8px] text-rose-600 hover:text-rose-700 hover:bg-rose-50 font-bold text-xs transition cursor-pointer">
                                        <span class="material-symbols-rounded text-xs">delete</span>
                                        <span>Hapus</span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-10 text-[#64748B]">
                                Belum ada ulasan atau rating dari pembaca.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Paginasi -->
        <div class="pt-4 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-3">
            <span class="text-xs text-[#64748B]">
                {{ $reviews->total() }} ulasan
            </span>
            <div>
                {{ $reviews->links('vendor.pagination.custom') }}
            </div>
        </div>
    </div>

    <!-- Modal Balas Ulasan Sederhana -->
    <div x-show="replyModalOpen" 
         x-cloak
         class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4 text-center">
            <div class="fixed inset-0 bg-slate-900/40 backdrop-blur-xs transition-opacity" @click="replyModalOpen = false"></div>
            
            <div class="inline-block bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:max-w-md w-full border border-[#E2E8F0] p-6 relative z-10">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <h3 class="text-base font-bold text-[#1E293B]">Balas Ulasan Pembaca</h3>
                    <button type="button" @click="replyModalOpen = false" class="text-slate-400 hover:text-slate-600 flex items-center justify-center">
                        <span class="material-symbols-rounded text-lg">close</span>
                    </button>
                </div>

                <form :action="'{{ url('admin/reviews') }}/' + replyReviewId + '/reply'" method="POST" class="mt-4 space-y-4">
                    @csrf
                    <div>
                        <span class="text-xs font-bold text-[#64748B]">Ulasan dari <span x-text="replyReviewUser" class="text-[#1E293B]"></span>:</span>
                        <div class="mt-1 p-3 rounded-xl bg-slate-50 border border-slate-200 text-xs text-[#1E293B] italic" x-text="replyReviewComment"></div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-[#1E293B] mb-1">Tulis Balasan Anda <span class="text-rose-500">*</span></label>
                        <textarea name="reply" x-model="replyText" rows="4" required placeholder="Contoh: Terima kasih atas ulasannya! Selamat menikmati koleksi bacaan kami..."
                                  class="w-full px-3.5 py-2.5 rounded-xl border border-[#E2E8F0] text-xs text-[#1E293B] focus:outline-none focus:ring-2 focus:ring-[#1A43BF] font-medium leading-relaxed"></textarea>
                    </div>

                    <div class="pt-4 flex items-center justify-end gap-2 border-t border-slate-100">
                        <button type="button" @click="replyModalOpen = false" class="px-4 py-2 rounded-[8px] border border-slate-300 text-xs font-semibold text-[#1E293B] hover:bg-slate-50 transition cursor-pointer">
                            Batal
                        </button>
                        <button type="submit" class="inline-flex items-center gap-1.5 px-5 py-2 rounded-[8px] bg-[#1A43BF] hover:bg-[#15379e] text-white text-xs font-bold transition shadow-xs cursor-pointer">
                            <span class="material-symbols-rounded text-base">send</span>
                            <span>Kirim Balasan</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
