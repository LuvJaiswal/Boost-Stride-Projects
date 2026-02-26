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
            this.data = await response.json();
            this.render();
        } catch (error) {
            console.error("Framework Error: Could not load content.json", error);
        }
    }

    render() {
        if (!this.data) return;

        // 1. Static Text Elements (Simple ID mapping)
        this.populateStaticText();

        // 2. Collection Elements (Lists/Grids)
        this.renderServices();
        this.renderTestimonials();
    }

    populateStaticText() {
        // Map JSON keys to HTML IDs
        const mapping = {
            "hero-subtitle": this.data.hero.subtitle,
            "hero-title": this.data.hero.title,
            "hero-desc": this.data.hero.description,
            "footer-address": this.data.footer.address,
            "footer-phone": this.data.footer.phone,
            "footer-email": this.data.footer.email,
            "footer-copyright": this.data.footer.copyright
        };

        Object.entries(mapping).forEach(([id, value]) => {
            const el = document.getElementById(id);
            if (el) el.textContent = value;
        });
    }

    renderServices() {
        const container = document.getElementById("services-container");
        if (!container || !this.data.services) return;

        container.innerHTML = this.data.services.map(service => `
            <div class="col-md-6 col-lg-4 wow fadeInUp" data-wow-delay="0.1s">
                <div class="c-service-item service-item">
                    <div class="overflow-hidden">
                        <img class="img-fluid" src="${service.image}" alt="${service.title}">
                    </div>
                    <div class="p-4 text-center border border-5 border-light border-top-0">
                        <h4 class="mb-3">${service.title}</h4>
                        <p>${service.description}</p>
                        <a class="fw-medium" href="${service.link}">Read More<i class="fa fa-arrow-right ms-2"></i></a>
                    </div>
                </div>
            </div>
        `).join('');
    }

    renderTestimonials() {
        // Testimonials often use specialized sliders (like Owl Carousel)
        // We inject the items before the slider initializes.
        const container = document.getElementById("testimonials-container");
        if (!container || !this.data.testimonials) return;

        container.innerHTML = this.data.testimonials.map(t => `
            <div class="testimonial-item text-center">
                <img class="img-fluid bg-light p-2 mx-auto mb-3" src="${t.image}" style="width: 90px; height: 90px;">
                <div class="testimonial-text text-center p-4">
                    <p>${t.text}</p>
                    <h5 class="mb-1">${t.name}</h5>
                    <span class="fst-italic">${t.profession}</span>
                </div>
            </div>
        `).join('');
    }
}

// Global Export
window.BoostStride = new ContentLoader('src/data/content.json');
document.addEventListener('DOMContentLoaded', () => window.BoostStride.init());
