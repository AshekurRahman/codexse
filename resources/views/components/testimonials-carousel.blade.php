@props(['testimonials' => null])

@php
    use App\Models\Testimonial;
    use App\Models\HomepageSection;

    // Fetch testimonials from database
    $testimonials = $testimonials ?? Testimonial::getForHomepage();

    // Get section header from database
    $section = HomepageSection::getSection('testimonials');

    // Convert to array format for Alpine.js compatibility
    $testimonialData = $testimonials->map(fn($t) => [
        'name' => $t->name,
        'role' => $t->role ?? $t->company ?? '',
        'avatar' => $t->avatar_url,
        'content' => $t->content,
        'rating' => $t->rating,
    ])->toArray();
@endphp

@if(count($testimonialData) > 0)
<section class="section-lg bg-gradient-to-b from-white to-surface-50 dark:from-surface-900 dark:to-surface-950">
    <div class="mx-auto max-w-7xl container-padding">
        <!-- Header -->
        <div class="text-center mb-12" x-scroll-animate>
            <span class="text-overline mb-3 block">{{ $section?->badge_text ?? 'Testimonials' }}</span>
            <h2 class="text-display-md text-surface-900 dark:text-white mb-4">{{ $section?->title ?? 'What Our Customers Say' }}</h2>
            <p class="text-body-lg text-surface-600 dark:text-surface-400 max-w-2xl mx-auto">
                {{ $section?->subtitle ?? 'Join thousands of satisfied customers who have found success with Codexse' }}
            </p>
        </div>

        <!-- Carousel -->
        <div x-data="testimonialCarousel({{ json_encode($testimonialData) }})"
             x-init="init()"
             @mouseenter="stopAutoplay()"
             @mouseleave="startAutoplay()"
             class="relative">

            <!-- Carousel Container -->
            <div class="overflow-hidden">
                <div class="flex transition-transform duration-500 ease-out"
                     :style="{ transform: `translateX(-${currentSlide * 100}%)` }">
                    <!-- Slide Groups -->
                    <template x-for="(_, slideIndex) in totalSlides" :key="slideIndex">
                        <div class="w-full flex-shrink-0 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 px-1">
                            <template x-for="(testimonial, index) in testimonials.slice(slideIndex * slidesPerView, (slideIndex + 1) * slidesPerView)" :key="index">
                                <div class="testimonial-card p-8 h-full flex flex-col group">
                                    <!-- Quote Icon -->
                                    <div class="testimonial-card-quote opacity-80 group-hover:opacity-100 transition-opacity">
                                        <svg class="w-7 h-7 text-white" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h3.983v10h-9.983z"/>
                                        </svg>
                                    </div>

                                    <!-- Rating Badge -->
                                    <div class="testimonial-card-rating mb-5 self-start">
                                        <template x-for="star in testimonial.rating">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                            </svg>
                                        </template>
                                        <span class="ml-1" x-text="testimonial.rating + '.0'"></span>
                                    </div>

                                    <!-- Content -->
                                    <p class="text-surface-700 dark:text-surface-200 flex-grow mb-8 leading-relaxed text-base" x-text="testimonial.content"></p>

                                    <!-- Author -->
                                    <div class="flex items-center gap-4 pt-6 mt-auto border-t border-surface-200/50 dark:border-surface-600/50">
                                        <img :src="testimonial.avatar" :alt="testimonial.name" class="testimonial-card-avatar">
                                        <div>
                                            <p class="font-semibold text-surface-900 dark:text-white text-base" x-text="testimonial.name"></p>
                                            <p class="text-sm text-surface-500 dark:text-surface-400" x-text="testimonial.role"></p>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Navigation Arrows -->
            <button @click="prevSlide()"
                    class="absolute left-0 top-1/2 -translate-y-1/2 -translate-x-4 w-12 h-12 rounded-full bg-white dark:bg-surface-800 shadow-lg border border-surface-200 dark:border-surface-700 flex items-center justify-center text-surface-600 dark:text-surface-300 hover:bg-surface-50 dark:hover:bg-surface-700 transition-all focus:outline-none focus:ring-2 focus:ring-primary-500 hidden md:flex">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </button>
            <button @click="nextSlide()"
                    class="absolute right-0 top-1/2 -translate-y-1/2 translate-x-4 w-12 h-12 rounded-full bg-white dark:bg-surface-800 shadow-lg border border-surface-200 dark:border-surface-700 flex items-center justify-center text-surface-600 dark:text-surface-300 hover:bg-surface-50 dark:hover:bg-surface-700 transition-all focus:outline-none focus:ring-2 focus:ring-primary-500 hidden md:flex">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </button>

            <!-- Dots Navigation -->
            <div class="flex justify-center gap-2 mt-8">
                <template x-for="(_, index) in totalSlides" :key="index">
                    <button @click="goToSlide(index)"
                            :class="currentSlide === index ? 'bg-primary-600 w-8' : 'bg-surface-300 dark:bg-surface-600 w-2'"
                            class="h-2 rounded-full transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
                    </button>
                </template>
            </div>
        </div>
    </div>
</section>
@endif
