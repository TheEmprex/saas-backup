<div x-data="darkModeToggle()" x-init="init()" class="flex items-center px-1 py-2 text-xs rounded-md cursor-pointer select-none hover:bg-zinc-100 dark:hover:bg-zinc-800">
    <button
        @click="toggle()"
        type="button"
        role="switch"
        :aria-checked="isDark"
        :class="isDark ? 'bg-zinc-700' : 'bg-slate-300'"
        class="relative inline-flex flex-shrink-0 py-1 ml-1 transition rounded-full w-7 focus:ring-0"
    >
        <span
            :class="isDark ? 'translate-x-[13px]' : 'translate-x-1'"
            class="w-3 h-3 transition bg-white rounded-full shadow-md focus:outline-none"
            aria-hidden="true"
        ></span>
    </button>

    <label class="flex-shrink-0 ml-1.5 font-medium cursor-pointer" :class="isDark ? 'text-zinc-200' : 'text-zinc-600'">
        <span x-show="!isDark">Dark Mode</span>
        <span x-show="isDark">Light Mode</span>
    </label>
</div>

<script>
function darkModeToggle() {
    return {
        isDark: false,
        
        init() {
            // Check current state
            this.isDark = localStorage.getItem('theme') === 'dark' || document.documentElement.classList.contains('dark');
            
            // Apply current state
            this.applyTheme();
        },
        
        toggle() {
            this.isDark = !this.isDark;
            this.applyTheme();
            
            // Save to localStorage
            localStorage.setItem('theme', this.isDark ? 'dark' : 'light');
        },
        
        applyTheme() {
            if (this.isDark) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        }
    }
}
</script>
