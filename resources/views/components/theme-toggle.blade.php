<div 
    x-data="{
        dark: localStorage.getItem('theme') === 'dark' || window.matchMedia('(prefers-color-scheme: dark)').matches,
        toggle() {
            this.dark = !this.dark;
            if (this.dark) {
                document.documentElement.classList.add('dark');
                localStorage.setItem('theme', 'dark');
            } else {
                document.documentElement.classList.remove('dark');
                localStorage.setItem('theme', 'light');
            }
        }
    }"
    x-init="dark ? document.documentElement.classList.add('dark') : document.documentElement.classList.remove('dark')"
>
    <button 
        @click="toggle" 
        class="px-3 py-2 rounded bg-gray-200 dark:bg-gray-800 text-gray-900 dark:text-gray-100 transition"
    >
        <span x-show="!dark">🌞 Claro</span>
        <span x-show="dark">🌙 Oscuro</span>
    </button>
</div>
