<div
    wire:loading.flex
    wire:target="tableFilters,tableSortColumn,tableSearch,gotoPage,nextPage,previousPage"
    class="filament-advanced-tables-skeleton absolute inset-0 z-10 flex flex-col bg-white/80 dark:bg-gray-900/80 backdrop-blur-[2px] rounded-lg overflow-hidden animate-pulse"
>
    {{-- Toolbar skeleton --}}
    <div class="flex items-center gap-3 px-4 py-3 border-b border-gray-100 dark:border-white/10">
        <div class="h-4 w-32 rounded bg-gray-200 dark:bg-gray-700"></div>
        <div class="h-4 w-24 rounded bg-gray-200 dark:bg-gray-700"></div>
        <div class="ml-auto h-4 w-20 rounded bg-gray-200 dark:bg-gray-700"></div>
    </div>

    {{-- Header skeleton --}}
    <div class="flex items-center gap-4 px-4 py-2.5 border-b border-gray-100 dark:border-white/10 bg-gray-50 dark:bg-gray-800">
        @for ($i = 0; $i < 5; $i++)
            <div class="h-3 rounded bg-gray-200 dark:bg-gray-700" style="width: {{ rand(60, 130) }}px"></div>
        @endfor
    </div>

    {{-- Row skeletons --}}
    @for ($row = 0; $row < 8; $row++)
        <div class="flex items-center gap-4 px-4 py-3 border-b border-gray-100 dark:border-white/10">
            <div class="w-4 h-4 rounded bg-gray-200 dark:bg-gray-700 flex-shrink-0"></div>
            @for ($col = 0; $col < 5; $col++)
                <div class="h-3 rounded bg-gray-200 dark:bg-gray-700" style="width: {{ rand(50, 180) }}px"></div>
            @endfor
            <div class="ml-auto flex gap-2">
                <div class="w-6 h-6 rounded bg-gray-200 dark:bg-gray-700"></div>
                <div class="w-6 h-6 rounded bg-gray-200 dark:bg-gray-700"></div>
            </div>
        </div>
    @endfor
</div>
