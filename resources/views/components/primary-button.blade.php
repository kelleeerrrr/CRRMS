<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center justify-center px-4 py-2 border border-transparent rounded-lg font-medium text-sm text-white focus:outline-none focus:ring-2 focus:ring-offset-2 transition-colors disabled:opacity-50 disabled:cursor-not-allowed shadow-sm hover:shadow-md']) }} style="background: var(--brand-maroon);" onmouseover="this.style.background='var(--brand-maroon-hover)'" onmouseout="this.style.background='var(--brand-maroon)'">
    {{ $slot }}
</button>
