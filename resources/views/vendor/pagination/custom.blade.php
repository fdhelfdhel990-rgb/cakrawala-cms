@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination Navigation" class="flex items-center justify-between gap-4">
        <div class="flex justify-between flex-1 sm:hidden">
            @if ($paginator->onFirstPage())
                <span class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-semibold text-slate-300 bg-white border border-slate-200 rounded-[8px] cursor-default">
                    <span class="material-symbols-rounded text-sm">chevron_left</span>
                    <span>Sebelumnya</span>
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-semibold text-[#1A43BF] bg-white border border-slate-200 rounded-[8px] hover:bg-slate-50 transition">
                    <span class="material-symbols-rounded text-sm">chevron_left</span>
                    <span>Sebelumnya</span>
                </a>
            @endif

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-semibold text-[#1A43BF] bg-white border border-slate-200 rounded-[8px] hover:bg-slate-50 transition">
                    <span>Berikutnya</span>
                    <span class="material-symbols-rounded text-sm">chevron_right</span>
                </a>
            @else
                <span class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-semibold text-slate-300 bg-white border border-slate-200 rounded-[8px] cursor-default">
                    <span>Berikutnya</span>
                    <span class="material-symbols-rounded text-sm">chevron_right</span>
                </span>
            @endif
        </div>

        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between gap-4">
            <div>
                <p class="text-xs text-[#6F7480] font-medium">
                    Menampilkan <span class="font-bold text-[#292D39]">{{ $paginator->firstItem() ?? 0 }}</span> - <span class="font-bold text-[#292D39]">{{ $paginator->lastItem() ?? 0 }}</span> dari <span class="font-bold text-[#292D39]">{{ $paginator->total() }}</span> data
                </p>
            </div>

            <div class="inline-flex items-center gap-1.5">
                {{-- Previous Page Link --}}
                @if ($paginator->onFirstPage())
                    <span class="w-8 h-8 flex items-center justify-center text-xs font-bold text-slate-300 bg-white border border-slate-200 rounded-[8px] cursor-default">
                        <span class="material-symbols-rounded text-sm">chevron_left</span>
                    </span>
                @else
                    <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="w-8 h-8 flex items-center justify-center text-xs font-bold text-[#292D39] bg-white border border-slate-200 rounded-[8px] hover:border-[#1A43BF] hover:text-[#1A43BF] transition shadow-xs">
                        <span class="material-symbols-rounded text-sm">chevron_left</span>
                    </a>
                @endif

                {{-- Pagination Elements --}}
                @foreach ($elements as $element)
                    {{-- "Three Dots" Separator --}}
                    @if (is_string($element))
                        <span class="w-8 h-8 flex items-center justify-center text-xs font-bold text-[#6F7480] bg-white border border-slate-200 rounded-[8px] cursor-default">{{ $element }}</span>
                    @endif

                    {{-- Array Of Links --}}
                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            @if ($page == $paginator->currentPage())
                                <span class="w-8 h-8 flex items-center justify-center text-xs font-bold text-white bg-[#1A43BF] border border-[#1A43BF] rounded-[8px] shadow-xs cursor-default">
                                    {{ $page }}
                                </span>
                            @else
                                <a href="{{ $url }}" class="w-8 h-8 flex items-center justify-center text-xs font-semibold text-[#292D39] bg-white border border-slate-200 rounded-[8px] hover:border-[#1A43BF] hover:text-[#1A43BF] transition shadow-xs">
                                    {{ $page }}
                                </a>
                            @endif
                        @endforeach
                    @endif
                @endforeach

                {{-- Next Page Link --}}
                @if ($paginator->hasMorePages())
                    <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="w-8 h-8 flex items-center justify-center text-xs font-bold text-[#292D39] bg-white border border-slate-200 rounded-[8px] hover:border-[#1A43BF] hover:text-[#1A43BF] transition shadow-xs">
                        <span class="material-symbols-rounded text-sm">chevron_right</span>
                    </a>
                @else
                    <span class="w-8 h-8 flex items-center justify-center text-xs font-bold text-slate-300 bg-white border border-slate-200 rounded-[8px] cursor-default">
                        <span class="material-symbols-rounded text-sm">chevron_right</span>
                    </span>
                @endif
            </div>
        </div>
    </nav>
@endif
