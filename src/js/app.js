/**
 * Boost Stride Content Loader
 * Dynamically injects JSON content into the DOM.
 */

class ContentLoader {
    constructor(jsonPath) {
        this.jsonPath = jsonPath;
        this.data = null;
    }

    async init() {
        try {
            const response = await fetch(this.jsonPath);
            const result = await response.json();

            // Handle both API response {status, data} and raw JSON
            if (result.status === "success" && result.data) {
                this.data = result.data;
            } else {
                this.data = result;
            }

            this.render();
        } catch (error) {
            console.error("Framework Error: Could not load content source", error);
        }
    }

    render() {
        if (!this.data) return;

        // 1. Static Text Elements
        this.populateStaticText();

        // 2. Collection Elements
        this.renderServices();
        this.renderTestimonials();

        // 3. Re-initialize Plugins
        this.reinitializePlugins();
    }

    reinitializePlugins() {
        // Re-init WOW animations
        if (typeof WOW !== 'undefined') {
            new WOW().init();
        }

        // Re-init Owl Carousel for testimonials
        if (window.jQuery && jQuery().owlCarousel) {
            const $testimonialCarousel = jQuery(".testimonial-carousel");
            if ($testimonialCarousel.length) {
                try {
                    $testimonialCarousel.owlCarousel('destroy');
                } catch (e) { }

                // Re-init with template settings
                $testimonialCarousel.owlCarousel({
                    autoplay: true,
                    smartSpeed: 1000,
                    center: true,
                    dots: false,
                    loop: true,
                    nav: true,
                    navText: [
                        '<i class="bi bi-arrow-left"></i>',
                        '<i class="bi bi-arrow-right"></i>'
                    ],
                    responsive: {
                        0: { items: 1 },
                        768: { items: 2 }
                    }
                });
            }
        }
    }

    populateStaticText() {
        // Map JSON keys to HTML IDs
        // Note: The API structure might differ slightly from the old static JSON
        const mapping = {
            "hero-subtitle": this.data.hero?.subtitle,
            "hero-title": this.data.hero?.title,
            "hero-desc": this.data.hero?.description,
            "topbar-address": (this.data.contact?.address),
            "topbar-phone": (this.data.contact?.phone),
            "footer-address": (this.data.contact?.address),
            "footer-phone": (this.data.contact?.phone),
            "footer-email": (this.data.contact?.email),
            "footer-copyright": (this.data.seo?.title || "Boost Stride")
        };

        Object.entries(mapping).forEach(([id, value]) => {
            const el = document.getElementById(id);
            if (el && value) el.textContent = value;
        });
    }

    renderServices() {
        const container = document.getElementById("services-container");
        if (!container || !this.data.services || this.data.services.length === 0) return;

        container.innerHTML = this.data.services.map(service => `
            <div class="col-md-6 col-lg-4 wow fadeInUp" data-wow-delay="0.1s">
                <div class="service-item">
                    <div class="overflow-hidden">
                        <img class="img-fluid" src="${service.image || 'img/service-1.jpg'}" alt="${service.title}">
                    </div>
                    <div class="p-4 text-center border border-5 border-light border-top-0">
                        <h4 class="mb-3">${service.title}</h4>
                        <p>${service.description}</p>
                        <a class="fw-medium" href="#">Read More<i class="fa fa-arrow-right ms-2"></i></a>
                    </div>
                </div>
            </div>
        `).join('');
    }

    renderTestimonials() {
        const container = document.getElementById("testimonials-container");
        if (!container || !this.data.testimonials || this.data.testimonials.length === 0) return;

        container.innerHTML = this.data.testimonials.map(t => `
            <div class="testimonial-item text-center">
                <img class="img-fluid bg-light p-2 mx-auto mb-3" src="${t.image || 'img/testimonial-1.jpg'}" style="width: 90px; height: 90px;">
                <div class="testimonial-text text-center p-4">
                    <p>${t.text}</p>
                    <h5 class="mb-1">${t.name}</h5>
                    <span class="fst-italic">${t.profession}</span>
                </div>
            </div>
        `).join('');
    }
}

// Global Export - Switch to live API for dynamic updates
window.BoostStride = new ContentLoader('api/get_content.php');
document.addEventListener('DOMContentLoaded', () => window.BoostStride.init());
