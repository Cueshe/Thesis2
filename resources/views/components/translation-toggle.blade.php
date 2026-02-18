<!-- Translation Toggle Component -->
<div class="relative" x-data="{
        open: false,
        isDark: document.documentElement.classList.contains('dark'),
        changeLanguage(lang) {
            if (window.changeLanguage) {
                window.changeLanguage(lang);
            }
        },
        init() {
            const updateTheme = (event) => {
                if (event?.detail?.effectiveTheme) {
                    this.isDark = event.detail.effectiveTheme === 'dark';
                } else if (window.themeManager?.getEffective) {
                    this.isDark = window.themeManager.getEffective() === 'dark';
                } else {
                    this.isDark = document.documentElement.classList.contains('dark');
                }
            };

            window.addEventListener('themechange', updateTheme);

            this.$nextTick(() => {
                if (window.themeManager?.getEffective) {
                    this.isDark = window.themeManager.getEffective() === 'dark';
                } else {
                    this.isDark = document.documentElement.classList.contains('dark');
                }
            });
        }
    }" style="z-index: 10;">
    <button @click="open = !open" @click.outside="open = false" 
            class="flex items-center gap-1.5 sm:gap-2 px-2 sm:px-4 py-1.5 sm:py-2 border rounded-lg font-medium text-xs sm:text-sm transition cursor-pointer touch-manipulation"
            :class="isDark ? 'bg-white text-black border-gray-200 hover:bg-gray-100' : 'bg-black text-white border-gray-800 hover:bg-gray-900'"
            style="min-width: 44px; min-height: 44px; -webkit-tap-highlight-color: transparent;">
        <svg class="w-4 h-4 sm:w-5 sm:h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"/>
        </svg>
        <span class="translation-current-lang hidden sm:inline">English</span>
        <svg class="w-3 h-3 sm:w-4 sm:h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
        </svg>
    </button>
    <!-- Dropdown Menu -->
    <div x-show="open" 
         x-transition:enter="transition ease-out duration-100"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-75"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="absolute right-0 mt-2 w-40 sm:w-48 rounded-lg shadow-lg z-[100] border"
         :class="isDark ? 'bg-white border-gray-200' : 'bg-gray-900 border-gray-800'"
         style="display: none;">
        <div class="py-1">
            <button @click="changeLanguage('en'); open = false" 
                    class="w-full text-left px-3 sm:px-4 py-2 text-xs sm:text-sm transition flex items-center gap-2 touch-manipulation"
                    :class="isDark ? 'text-gray-900 hover:bg-gray-100' : 'text-white hover:bg-gray-700'"
                    style="min-height: 44px;">
                <span class="font-medium">English</span>
            </button>
            <button @click="changeLanguage('fil'); open = false" 
                    class="w-full text-left px-3 sm:px-4 py-2 text-xs sm:text-sm transition flex items-center gap-2 touch-manipulation"
                    :class="isDark ? 'text-gray-900 hover:bg-gray-100' : 'text-white hover:bg-gray-700'"
                    style="min-height: 44px;">
                <span class="font-medium">Filipino</span>
            </button>
            <button @click="changeLanguage('bis'); open = false" 
                    class="w-full text-left px-3 sm:px-4 py-2 text-xs sm:text-sm transition flex items-center gap-2 touch-manipulation"
                    :class="isDark ? 'text-gray-900 hover:bg-gray-100' : 'text-white hover:bg-gray-700'"
                    style="min-height: 44px;">
                <span class="font-medium">Bisaya</span>
            </button>
        </div>
    </div>
</div>

