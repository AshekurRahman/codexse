@props(['product'])

@php
    $galleryItems = $product->gallery_items;
@endphp

<div x-data="{
    activeIndex: 0,
    lightboxOpen: false,
    items: {{ json_encode($galleryItems) }},
    get activeItem() {
        return this.items[this.activeIndex] || null;
    },
    next() {
        this.activeIndex = (this.activeIndex + 1) % this.items.length;
    },
    prev() {
        this.activeIndex = (this.activeIndex - 1 + this.items.length) % this.items.length;
    },
    openLightbox(index) {
        this.activeIndex = index;
        this.lightboxOpen = true;
        document.body.style.overflow = 'hidden';
    },
    closeLightbox() {
        this.lightboxOpen = false;
        document.body.style.overflow = '';
    }
}" @keydown.escape.window="closeLightbox()" @keydown.arrow-right.window="lightboxOpen && next()" @keydown.arrow-left.window="lightboxOpen && prev()" class="space-y-4">

    <!-- Main Display -->
    <div class="relative aspect-video rounded-2xl overflow-hidden bg-white dark:bg-surface-800 border border-surface-200 dark:border-surface-700 cursor-pointer group"
         @click="openLightbox(activeIndex)">
        <template x-if="activeItem && activeItem.type === 'image'">
            <img :src="activeItem.url" :alt="'{{ $product->name }}'" class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105">
        </template>
        <template x-if="activeItem && activeItem.type === 'video'">
            <div class="relative w-full h-full">
                <img :src="activeItem.thumbnail" alt="Video thumbnail" class="w-full h-full object-cover">
                <!-- Video Play Button Overlay -->
                <div class="absolute inset-0 flex items-center justify-center bg-black/30 group-hover:bg-black/40 transition-colors">
                    <div class="w-20 h-20 rounded-full bg-white/90 dark:bg-surface-800/90 flex items-center justify-center shadow-2xl transform group-hover:scale-110 transition-transform">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-primary-600 ml-1" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M8 5v14l11-7z"/>
                        </svg>
                    </div>
                </div>
            </div>
        </template>
        <template x-if="!activeItem">
            <div class="w-full h-full flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-24 w-24 text-surface-300 dark:text-surface-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
            </div>
        </template>

        <!-- Expand Icon -->
        <div class="absolute top-4 right-4 p-2 rounded-lg bg-black/50 text-white opacity-0 group-hover:opacity-100 transition-opacity">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4" />
            </svg>
        </div>

        <!-- Navigation Arrows (if multiple items) -->
        <template x-if="items.length > 1">
            <div>
                <button @click.stop="prev()" class="absolute left-4 top-1/2 -translate-y-1/2 p-2 rounded-full bg-black/50 text-white opacity-0 group-hover:opacity-100 transition-opacity hover:bg-black/70">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </button>
                <button @click.stop="next()" class="absolute right-4 top-1/2 -translate-y-1/2 p-2 rounded-full bg-black/50 text-white opacity-0 group-hover:opacity-100 transition-opacity hover:bg-black/70">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </button>
            </div>
        </template>
    </div>

    <!-- Thumbnails -->
    <template x-if="items.length > 1">
        <div class="grid grid-cols-4 sm:grid-cols-5 md:grid-cols-6 gap-2">
            <template x-for="(item, index) in items" :key="index">
                <button @click="activeIndex = index"
                    class="relative aspect-video rounded-lg overflow-hidden border-2 transition-all"
                    :class="activeIndex === index ? 'border-primary-500 ring-2 ring-primary-500/30' : 'border-surface-200 dark:border-surface-700 hover:border-primary-400'">
                    <img :src="item.thumbnail" alt="" class="w-full h-full object-cover">
                    <!-- Video indicator -->
                    <template x-if="item.type === 'video'">
                        <div class="absolute inset-0 flex items-center justify-center bg-black/30">
                            <div class="w-8 h-8 rounded-full bg-white/90 flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-primary-600 ml-0.5" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M8 5v14l11-7z"/>
                                </svg>
                            </div>
                        </div>
                    </template>
                </button>
            </template>
        </div>
    </template>

    <!-- Lightbox Modal -->
    <div x-show="lightboxOpen" x-cloak
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center bg-black/95 p-4">

        <!-- Close Button -->
        <button @click="closeLightbox()" class="absolute top-4 right-4 p-2 rounded-full bg-white/10 text-white hover:bg-white/20 transition-colors z-10">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>

        <!-- Content -->
        <div class="relative w-full max-w-6xl max-h-[90vh]">
            <!-- Image -->
            <template x-if="activeItem && activeItem.type === 'image'">
                <img :src="activeItem.url" alt="" class="w-full h-full object-contain max-h-[85vh] mx-auto">
            </template>

            <!-- Video -->
            <template x-if="activeItem && activeItem.type === 'video'">
                <div class="relative w-full" style="padding-bottom: 56.25%;">
                    <iframe :src="activeItem.url + '?autoplay=1'"
                            class="absolute inset-0 w-full h-full rounded-lg"
                            frameborder="0"
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                            allowfullscreen>
                    </iframe>
                </div>
            </template>

            <!-- Navigation -->
            <template x-if="items.length > 1">
                <div>
                    <button @click="prev()" class="absolute left-4 top-1/2 -translate-y-1/2 p-3 rounded-full bg-white/10 text-white hover:bg-white/20 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                    </button>
                    <button @click="next()" class="absolute right-4 top-1/2 -translate-y-1/2 p-3 rounded-full bg-white/10 text-white hover:bg-white/20 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>
                </div>
            </template>

            <!-- Counter -->
            <div class="absolute bottom-4 left-1/2 -translate-x-1/2 px-4 py-2 rounded-full bg-black/50 text-white text-sm">
                <span x-text="activeIndex + 1"></span> / <span x-text="items.length"></span>
            </div>
        </div>

        <!-- Thumbnail Strip -->
        <template x-if="items.length > 1">
            <div class="absolute bottom-16 left-1/2 -translate-x-1/2 flex gap-2 p-2 rounded-lg bg-black/50 max-w-full overflow-x-auto">
                <template x-for="(item, index) in items" :key="'lb-' + index">
                    <button @click="activeIndex = index"
                        class="relative w-16 h-10 rounded overflow-hidden border-2 transition-all flex-shrink-0"
                        :class="activeIndex === index ? 'border-white' : 'border-transparent opacity-60 hover:opacity-100'">
                        <img :src="item.thumbnail" alt="" class="w-full h-full object-cover">
                        <template x-if="item.type === 'video'">
                            <div class="absolute inset-0 flex items-center justify-center bg-black/40">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-white" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M8 5v14l11-7z"/>
                                </svg>
                            </div>
                        </template>
                    </button>
                </template>
            </div>
        </template>
    </div>
</div>
