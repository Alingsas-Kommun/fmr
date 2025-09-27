<div x-data="themeSwitcher()" x-init="init()">
    <button @click="cycleTheme()" class="flex items-center justify-center h-12 transition-colors duration-200" :aria-label="getButtonAriaLabel()" :title="getButtonTitle()">
        <x-heroicon-o-sun x-cloak x-show="currentTheme === 'light'" class="w-7 h-7 text-yellow-500" />
        <x-heroicon-o-moon x-cloak x-show="currentTheme === 'dark'" class="w-6 h-6 text-blue-700" />
        <x-heroicon-o-sun x-cloak x-show="currentTheme === 'auto'" class="w-7 h-7 text-gray-700" />
    </button>
</div>