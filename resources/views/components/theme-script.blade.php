<script>
    // Theme Management System
    (function() {
        const THEME_KEY = 'q2l-theme';
        const mediaQuery = window.matchMedia('(prefers-color-scheme: dark)');
        
        function getSystemTheme() {
            return mediaQuery.matches ? 'dark' : 'light';
        }
        
        function getStoredTheme() {
            return localStorage.getItem(THEME_KEY);
        }
        
        function setStoredTheme(theme) {
            localStorage.setItem(THEME_KEY, theme);
        }
        
        function notifyThemeChange(theme, effectiveTheme) {
            window.dispatchEvent(new CustomEvent('themechange', { detail: { theme, effectiveTheme } }));
        }
        
        function applyTheme(theme) {
            const effectiveTheme = theme === 'system' ? getSystemTheme() : theme;

            if (effectiveTheme === 'dark') {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }

            document.documentElement.setAttribute('data-theme', effectiveTheme);
            return effectiveTheme;
        }
        
        // Initialize theme immediately to prevent flash
        const storedTheme = getStoredTheme() || 'system';
        const initialEffectiveTheme = applyTheme(storedTheme);
        notifyThemeChange(storedTheme, initialEffectiveTheme);
        
        // Listen for system theme changes
        function handleSystemThemeChange() {
            const currentTheme = getStoredTheme() || 'system';
            if (currentTheme === 'system') {
                const effectiveTheme = applyTheme('system');
                notifyThemeChange('system', effectiveTheme);
            }
        }

        if (typeof mediaQuery.addEventListener === 'function') {
            mediaQuery.addEventListener('change', handleSystemThemeChange);
        } else if (typeof mediaQuery.addListener === 'function') {
            mediaQuery.addListener(handleSystemThemeChange);
        }
        
        // Make theme functions globally available
        window.themeManager = {
            get: () => getStoredTheme() || 'system',
            set: (theme) => {
                setStoredTheme(theme);
                const effectiveTheme = applyTheme(theme);
                notifyThemeChange(theme, effectiveTheme);
            },
            getEffective: () => {
                const theme = getStoredTheme() || 'system';
                return theme === 'system' ? getSystemTheme() : theme;
            }
        };
    })();
</script>
