@props(['testimonials' => null])

@php
    // Default testimonials if none provided
    $defaultTestimonials = [
        [
            'name' => 'Sarah Johnson',
            'role' => 'E-commerce Business Owner',
            'avatar' => 'https://ui-avatars.com/api/?name=Sarah+Johnson&background=6366f1&color=fff',
            'content' => 'Codexse has transformed my online business. The digital products I purchased helped me launch faster than I ever imagined. Excellent quality and support!',
            'rating' => 5,
        ],
        [
            'name' => 'Michael Chen',
            'role' => 'Web Developer',
            'avatar' => 'https://ui-avatars.com/api/?name=Michael+Chen&background=06b6d4&color=fff',
            'content' => 'As a developer, I appreciate the code quality of the themes and plugins here. Well-documented, clean code, and regular updates. Highly recommended!',
            'rating' => 5,
        ],
        [
            'name' => 'Emily Rodriguez',
            'role' => 'Digital Marketing Agency',
            'avatar' => 'https://ui-avatars.com/api/?name=Emily+Rodriguez&background=10b981&color=fff',
            'content' => 'We use Codexse for all our client projects. The variety of templates and the quality of services from freelancers is outstanding. Great platform!',
            'rating' => 5,
        ],
        [
            'name' => 'David Kim',
            'role' => 'Startup Founder',
            'avatar' => 'https://ui-avatars.com/api/?name=David+Kim&background=f59e0b&color=fff',
            'content' => 'Found amazing talent here for my startup. The service marketplace is filled with skilled professionals who deliver quality work on time.',
            'rating' => 5,
        ],
        [
            'name' => 'Lisa Thompson',
            'role' => 'Freelance Designer',
            'avatar' => 'https://ui-avatars.com/api/?name=Lisa+Thompson&background=ec4899&color=fff',
            'content' => 'Selling my design templates on Codexse has been a game-changer. The platform is easy to use, and the customer support is exceptional.',
            'rating' => 5,
        ],
        [
            'name' => 'James Wilson',
            'role' => 'Small Business Owner',
            'avatar' => 'https://ui-avatars.com/api/?name=James+Wilson&background=8b5cf6&color=fff',
            'content' => 'The best marketplace for digital products I have ever used. Quick downloads, lifetime updates, and responsive support. Worth every penny!',
            'rating' => 5,
        ],
    ];

    $testimonials = $testimonials ?? $defaultTestimonials;
@endphp

<section class="section-lg bg-gradient-to-b from-white to-surface-50 dark:from-surface-900 dark:to-surface-950">
    <div class="mx-auto max-w-7xl container-padding">
        <!-- Header -->
        <div class="text-center mb-12" x-scroll-animate>
            <span class="text-overline mb-3 block">Testimonials</span>
            <h2 class="text-display-md text-surface-900 dark:text-white mb-4">What Our Customers Say</h2>
            <p class="text-body-lg text-surface-600 dark:text-surface-400 max-w-2xl mx-auto">
                Join thousands of satisfied customers who have found success with Codexse
            </p>
        </div>

        <!-- Carousel -->
        <div x-data="testimonialCarousel({{ json_encode($testimonials) }})"
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
                                <div class="testimonial-card p-6 h-full flex flex-col">
                                    <!-- Rating -->
                                    <div class="flex gap-1 mb-4">
                                        <template x-for="star in 5">
                                            <svg class="w-5 h-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                            </svg>
                                        </template>
                                    </div>

                                    <!-- Content -->
                                    <p class="text-surface-600 dark:text-surface-300 flex-grow mb-6 leading-relaxed" x-text="testimonial.content"></p>

                                    <!-- Author -->
                                    <div class="flex items-center gap-4 pt-4 border-t border-surface-100 dark:border-surface-700">
                                        <img :src="testimonial.avatar" :alt="testimonial.name" class="w-12 h-12 rounded-full object-cover ring-2 ring-primary-100 dark:ring-primary-900">
                                        <div>
                                            <p class="font-semibold text-surface-900 dark:text-white" x-text="testimonial.name"></p>
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
