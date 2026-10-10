<div x-data="{ open: false, query: '', results: [], loading: false }" 
     @open-global-search.window="open = true"
     @keydown.window.escape="open = false"
     @keydown.window.meta-k.prevent="open = true"
     @keydown.window.ctrl-k.prevent="open = true"
     class="relative z-50"
     x-show="open"
     x-transition:enter="transition ease-out duration-200"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-150"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0">
    
    <!-- Backdrop -->
    <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" @click="open = false"></div>
    
    <!-- Modal -->
    <div class="fixed inset-0 overflow-y-auto p-4 sm:p-6">
        <div class="mx-auto max-w-2xl">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                <!-- Search Input -->
                <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                    <div class="relative">
                        <x-heroicon-o-magnifying-glass class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" />
                        <input 
                            type="text" 
                            x-model="query"
                            @input="$wire.search(query)"
                            placeholder="Search for facilities, equipment, users, reservations..."
                            class="w-full pl-10 pr-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                            autofocus
                        />
                        <kbd class="absolute right-3 top-1/2 -translate-y-1/2 px-2 py-0.5 text-xs font-semibold text-gray-400 bg-gray-200 dark:bg-gray-600 rounded">ESC</kbd>
                    </div>
                </div>
                
                <!-- Results -->
                <div class="max-h-96 overflow-y-auto">
                    <template x-if="loading">
                        <div class="p-8 text-center">
                            <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-primary-500"></div>
                            <p class="mt-2 text-gray-500 dark:text-gray-400">Searching...</p>
                        </div>
                    </template>
                    
                    <template x-if="!loading && query === ''">
                        <div class="p-8 text-center text-gray-500 dark:text-gray-400">
                            <x-heroicon-o-magnifying-glass class="w-12 h-12 mx-auto mb-4 opacity-50" />
                            <p>Type to search across the system</p>
                            <p class="text-sm mt-2">Try: facilities, equipment, users, reservations</p>
                        </div>
                    </template>
                    
                    <template x-if="!loading && query !== '' && results.length === 0">
                        <div class="p-8 text-center text-gray-500 dark:text-gray-400">
                            <p>No results found for "<span x-text="query"></span>"</p>
                        </div>
                    </template>
                    
                    <template x-if="!loading && results.length > 0">
                        <div class="divide-y divide-gray-200 dark:divide-gray-700">
                            <template x-for="result in results" :key="result.id">
                                <a :href="result.url" class="flex items-center p-4 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                    <div class="flex-shrink-0 w-10 h-10 rounded-xl bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center">
                                        <x-heroicon-o-building-office class="w-5 h-5 text-primary-600 dark:text-primary-400" x-show="result.type === 'facility'" />
                                        <x-heroicon-o-cube class="w-5 h-5 text-primary-600 dark:text-primary-400" x-show="result.type === 'equipment'" />
                                        <x-heroicon-o-user class="w-5 h-5 text-primary-600 dark:text-primary-400" x-show="result.type === 'user'" />
                                        <x-heroicon-o-calendar class="w-5 h-5 text-primary-600 dark:text-primary-400" x-show="result.type === 'reservation'" />
                                    </div>
                                    <div class="ml-4 flex-1">
                                        <p class="text-sm font-medium text-gray-900 dark:text-white" x-text="result.title"></p>
                                        <p class="text-sm text-gray-500 dark:text-gray-400" x-text="result.subtitle"></p>
                                    </div>
                                    <div class="ml-4">
                                        <span class="inline-flex items-center px-2.5 py-1 text-xs font-medium rounded-full" 
                                              :class="{
                                                  'bg-success-100 text-success-800 dark:bg-success-900/30 dark:text-success-400': result.type === 'facility',
                                                  'bg-primary-100 text-primary-800 dark:bg-primary-900/30 dark:text-primary-400': result.type === 'equipment',
                                                  'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-400': result.type === 'user',
                                                  'bg-warning-100 text-warning-800 dark:bg-warning-900/30 dark:text-warning-400': result.type === 'reservation'
                                              }" x-text="result.type"></span>
                                    </div>
                                </a>
                            </template>
                        </div>
                    </template>
                </div>
                
                <!-- Footer -->
                <div class="p-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50">
                    <div class="flex items-center justify-between text-sm text-gray-500 dark:text-gray-400">
                        <div class="flex items-center space-x-4">
                            <span><kbd class="px-1.5 py-0.5 bg-gray-200 dark:bg-gray-600 rounded text-xs">↑↓</kbd> Navigate</span>
                            <span><kbd class="px-1.5 py-0.5 bg-gray-200 dark:bg-gray-600 rounded text-xs">↵</kbd> Select</span>
                        </div>
                        <span x-show="results.length > 0" x-text="results.length + ' results'"></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@livewire('global-search')
