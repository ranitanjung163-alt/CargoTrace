<!-- Language Translation Switcher (Fitur Penerjemah Bahasa - Semua Negara 130+ Bahasa) -->
<div class="relative notranslate" data-lang-container id="{{ $containerId ?? 'langDropdownContainer' }}">
    <button type="button" data-lang-btn id="{{ $btnId ?? 'langDropdownBtn' }}" title="Translate / Terjemahkan Bahasa"
            class="flex items-center space-x-1.5 px-3 py-2 bg-white hover:bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-700 shadow-soft-xs transition-all hover:border-brand-primary/50 focus:outline-none">
        <span class="text-sm">🌐</span>
        <span data-lang-flag id="{{ $flagId ?? 'currentLangFlag' }}">🇺🇸</span>
        <span data-lang-label id="{{ $labelId ?? 'currentLangLabel' }}" class="{{ $labelClass ?? 'hidden sm:inline' }}">English</span>
        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
    </button>

    <!-- Language Dropdown Menu -->
    <div data-lang-menu id="{{ $menuId ?? 'langDropdownMenu' }}" class="hidden absolute right-0 mt-2.5 w-72 sm:w-80 bg-white border border-slate-200 rounded-2xl shadow-soft-lg py-2.5 z-50 animate-in fade-in zoom-in-95 duration-100 select-none">
        <div class="px-3.5 pb-2 border-b border-slate-100 flex items-center justify-between">
            <span class="text-[10px] font-black uppercase tracking-wider text-slate-400 flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" stroke-linejoin="round" d="M2 12h20M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1 4-10z"/></svg>
                Translate / Terjemahkan
            </span>
            <span class="text-[9px] font-mono text-blue-600 font-bold bg-blue-50 px-2 py-0.5 rounded-full border border-blue-100">Semua Negara (130+ Bahasa)</span>
        </div>

        <!-- Search Filter Input -->
        <div class="p-2 border-b border-slate-100">
            <div class="relative">
                <input type="text" data-lang-search id="{{ $searchId ?? 'langSearchInput' }}" placeholder="Cari negara / bahasa (e.g. Korea, Japan, Arab)..."
                       class="w-full pl-8 pr-3 py-1.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-800 placeholder-slate-400 focus:outline-none focus:border-brand-primary focus:bg-white focus:ring-2 focus:ring-brand-primary/10 transition-all">
                <svg class="w-3.5 h-3.5 text-slate-400 absolute left-2.5 top-2.5 pointer-events-none" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>
        </div>

        <!-- Dynamic Language List (All 130+ Countries) -->
        <div class="max-h-72 overflow-y-auto divide-y divide-slate-50 py-1" data-lang-list id="{{ $listId ?? 'langListOptions' }}">
            <!-- Rendered dynamically by JS -->
        </div>
    </div>
</div>
