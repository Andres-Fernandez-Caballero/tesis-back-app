<div
    x-data="{
        dark: localStorage.getItem('theme') === 'dark'
              || (!localStorage.getItem('theme')
              && window.matchMedia('(prefers-color-scheme: dark)').matches),

        toggle() {
            this.dark = !this.dark;
            document.documentElement.classList.toggle('dark', this.dark);
            localStorage.setItem('theme', this.dark ? 'dark' : 'light');
        }
    }"
    x-init="document.documentElement.classList.toggle('dark', dark)"
>
    <button
        @click="toggle"
        class="px-3 py-2 rounded bg-gray-200 dark:bg-gray-800 text-gray-900 dark:text-gray-100 transition"
    >
        <span x-show="!dark">🌞 Claro</span>
        <span x-show="dark">🌙 Oscuro</span>
    </button>
</div>
