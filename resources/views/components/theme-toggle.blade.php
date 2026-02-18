<!-- Theme Toggle Component -->
<div {{ $attributes->class('relative inline-block z-30') }}
     x-data="{
        open: false,
        currentTheme: window.themeManager.get(),
        effectiveTheme: window.themeManager.getEffective(),
        init() {
            const updateTheme = (event) => {
                if (event?.detail?.theme) {
                    this.currentTheme = event.detail.theme;
                } else {
                    this.currentTheme = window.themeManager.get();
                }
                this.effectiveTheme = event?.detail?.effectiveTheme ?? window.themeManager.getEffective();
            };
            window.addEventListener('themechange', updateTheme);
        }
     }"
     @click.outside="open = false">
    <button @click="open = !open" type="button" 
        class="theme-toggle-trigger flex items-center justify-center w-10 h-10 rounded-lg bg-transparent border-none hover:opacity-80 transition-opacity"
        :aria-label="'Current theme: ' + currentTheme">
        <!-- Light Icon -->
        <svg x-show="effectiveTheme === 'light'" class="w-5 h-5 text-black" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
        </svg>
        <!-- Dark Icon -->
        <svg x-show="effectiveTheme === 'dark' && currentTheme !== 'system'" class="w-5 h-5 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
        </svg>
        <!-- System Icon -->
        <svg x-show="currentTheme === 'system'" class="w-5 h-5 text-black dark:text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
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
         class="absolute right-0 mt-2 w-48 rounded-lg bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 shadow-lg z-50 theme-toggle-menu"
         style="display: none;">
        <div class="py-1">
            <button @click="window.themeManager.set('light'); currentTheme = 'light'; effectiveTheme = 'light'; open = false" 
                    class="theme-toggle-option flex items-center w-full px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
                    :class="{ 'bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400': currentTheme === 'light' }">
                <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
                Light
                <svg x-show="currentTheme === 'light'" class="w-4 h-4 ml-auto" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                </svg>
            </button>
            <button @click="window.themeManager.set('dark'); currentTheme = 'dark'; effectiveTheme = 'dark'; open = false" 
                    class="theme-toggle-option flex items-center w-full px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
                    :class="{ 'bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400': currentTheme === 'dark' }">
                <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                </svg>
                Dark
                <svg x-show="currentTheme === 'dark'" class="w-4 h-4 ml-auto" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                </svg>
            </button>
            <button @click="window.themeManager.set('system'); currentTheme = 'system'; effectiveTheme = window.themeManager.getEffective(); open = false" 
                    class="theme-toggle-option flex items-center w-full px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
                    :class="{ 'bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400': currentTheme === 'system' }">
                <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
                System
                <svg x-show="currentTheme === 'system'" class="w-4 h-4 ml-auto" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                </svg>
            </button>
        </div>
    </div>
</div>
