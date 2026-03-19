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

    getImagePath(path, fallback = 'img/team-1.jpg') {
        if (!path) return fallback;
        // Absolute URL — use as-is
        if (path.startsWith('http://') || path.startsWith('https://')) return path;
        // Uploaded file stored as 'uploads/filename.jpg' — lives inside /admin/
        if (path.startsWith('uploads/')) return 'admin/' + path;
        // Template default images like 'img/service-1.jpg' — at website root
        return path;
    }

    render() {
        if (!this.data) return;

        // 1. Static Text Elements
        this.populateStaticText();

        // 2. Collection Elements
        this.renderServices();
        this.renderTestimonials();
        this.renderAbout();
        this.renderFeatures();
        this.renderTeam();

        // 3. Re-initialize Plugins
        this.reinitializePlugins();
    }

    reinitializePlugins() {
        // Re-init WOW animations
        if (typeof WOW !== 'undefined') {
            new WOW().init();
        }

        // Re-init Facts counter
        if (window.jQuery && jQuery().counterUp) {
            jQuery('[data-toggle="counter-up"]').counterUp({
                delay: 10,
                time: 2000
            });
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
        const mapping = {
            "hero-subtitle": this.data.hero?.subtitle,
            "hero-title": this.data.hero?.title,
            "hero-desc": this.data.hero?.description,
            "topbar-address": (this.data.contact?.address),
            "topbar-phone": (this.data.contact?.phone),
            "footer-address": (this.data.contact?.address),
            "footer-phone": (this.data.contact?.phone),
            "footer-email": (this.data.contact?.email),
            "footer-title-address": this.data.footer?.titles?.address,
            "footer-title-services": this.data.footer?.titles?.services,
            "footer-title-links": this.data.footer?.titles?.links,
            "footer-title-newsletter": this.data.footer?.titles?.newsletter,
            "footer-newsletter-text": this.data.footer?.newsletter_text,
            "footer-newsletter-button": this.data.footer?.newsletter_button,
            "footer-copyright": (this.data.footer?.copyright_text || this.data.seo?.title || "Boost Stride"),
            "nav-brand-text": this.data.menu?.brand_name
        };

        Object.entries(mapping).forEach(([id, value]) => {
            if (!value) return;
            // Handle possibility of multiple elements (e.g. multiple hero slides)
            const elements = document.querySelectorAll(`#${id}, [data-id="${id}"]`);
            elements.forEach(el => {
                if (id.includes('desc') || id.includes('text')) {
                    el.innerHTML = value.replace(/\n/g, '<br>');
                } else {
                    el.textContent = value;
                }
            });
        });

        // 1.2 Attributes & Links
        const newsletterInput = document.getElementById("footer-newsletter-placeholder");
        if (newsletterInput && this.data.footer?.newsletter_placeholder) {
            newsletterInput.setAttribute('placeholder', this.data.footer.newsletter_placeholder);
        }

        const ctaBtn = document.getElementById("nav-cta-button");
        if (ctaBtn && this.data.menu?.cta_text) {
            ctaBtn.innerHTML = `${this.data.menu.cta_text}<i class="fa fa-arrow-right ms-3"></i>`;
            if (this.data.menu.cta_url) ctaBtn.setAttribute('href', this.data.menu.cta_url);
        }

        const linkMapping = {
            "footer-facebook": this.data.footer?.social?.facebook,
            "footer-twitter": this.data.footer?.social?.twitter,
            "footer-youtube": this.data.footer?.social?.youtube,
            "footer-linkedin": this.data.footer?.social?.linkedin,
            "footer-instagram": this.data.footer?.social?.instagram
        };

        Object.entries(linkMapping).forEach(([id, value]) => {
            const el = document.getElementById(id);
            if (el && value) el.setAttribute('href', value);
        });

        // 1.3 Quick Links Renderer
        const qlContainer = document.getElementById("footer-quick-links-container");
        if (qlContainer && this.data.footer?.quick_links) {
            qlContainer.innerHTML = this.data.footer.quick_links
                .filter(link => link.label && link.label.trim() !== "")
                .map(link => `<a class="btn btn-link" href="${link.url}">${link.label}</a>`)
                .join('');
        }

        // 1.4 Main Navbar Renderer
        this.renderNavbar();
    }

    renderNavbar() {
        const container = document.getElementById("main-nav-container");
        if (!container || !this.data.menu?.main_menu) return;

        const currentPath = window.location.pathname.split('/').pop() || 'index.php';

        container.innerHTML = this.data.menu.main_menu.map(item => {
            if (item.type === 'dropdown' && item.children && item.children.length > 0) {
                // Check if any child is active to highlight parent
                const isAnyChildActive = item.children.some(child => currentPath === child.url);
                const activeClass = isAnyChildActive ? 'active' : '';

                const subItemsHtml = item.children.map(child => {
                    const childActive = currentPath === child.url ? 'active' : '';
                    return `<a href="${child.url}" class="dropdown-item ${childActive}">${child.label}</a>`;
                }).join('');

                return `
                    <div class="nav-item dropdown">
                        <a href="#" class="nav-link dropdown-toggle ${activeClass}" data-bs-toggle="dropdown">${item.label}</a>
                        <div class="dropdown-menu fade-up m-0">
                            ${subItemsHtml}
                        </div>
                    </div>
                `;
            } else {
                const isActive = currentPath === item.url ? 'active' : '';
                return `<a href="${item.url}" class="nav-item nav-link ${isActive}">${item.label}</a>`;
            }
        }).join('');
    }

    renderServices() {
        const container = document.getElementById("services-container");
        const footerContainer = document.getElementById("footer-services-container");

        if (!container && !footerContainer) return;

        if (!this.data.services || this.data.services.length === 0) {
            if (container) container.innerHTML = '<div class="col-12 text-center py-5"><h5 class="text-muted">No services found. Visit Admin to add some.</h5></div>';
            return;
        }

        const servicesHtml = this.data.services.map(service => `
            <div class="col-md-6 col-lg-4 wow fadeInUp" data-wow-delay="0.1s">
                <div class="service-item">
                    <div class="overflow-hidden">
                        <img class="img-fluid" src="${this.getImagePath(service.image, 'img/service-1.jpg')}" alt="${service.title}">
                    </div>
                    <div class="p-4 text-center border border-5 border-light border-top-0">
                        <h4 class="mb-3">${service.title}</h4>
                        <p>${service.description}</p>
                        <a class="fw-medium" href="service-details.php?id=${service.id}">Read More<i class="fa fa-arrow-right ms-2"></i></a>
                    </div>
                </div>
            </div>
        `).join('');

        if (container) container.innerHTML = servicesHtml;

        if (footerContainer) {
            footerContainer.innerHTML = this.data.services.slice(0, 5).map(service => `
                <a class="btn btn-link" href="service-details.php?id=${service.id}">${service.title}</a>
            `).join('');
        }
    }

    renderTestimonials() {
        const container = document.getElementById("testimonials-container");
        if (!container || !this.data.testimonials || this.data.testimonials.length === 0) return;

        container.innerHTML = this.data.testimonials.map(t => `
            <div class="testimonial-item text-center">
                <img class="img-fluid bg-light p-2 mx-auto mb-3" src="${this.getImagePath(t.image, 'img/testimonial-1.jpg')}" style="width: 90px; height: 90px;">
                <div class="testimonial-text text-center p-4">
                    <p>${t.text}</p>
                    <h5 class="mb-1">${t.name}</h5>
                    <span class="fst-italic">${t.profession}</span>
                </div>
            </div>
        `).join('');
    }

    renderAbout() {
        const titleElements = document.querySelectorAll("#about-title");
        const descElements = document.querySelectorAll("#about-desc");
        const clientsElements = document.querySelectorAll("#about-clients, #about-clients-count");
        const repairsElements = document.querySelectorAll("#about-repairs, #about-repairs-count");
        const imageElements = document.querySelectorAll("#about-img, .about img");

        titleElements.forEach(el => {
            if (this.data.about?.title) el.textContent = this.data.about.title;
        });
        descElements.forEach(el => {
            if (this.data.about?.description) {
                // Fix internal image paths for narratives
                let html = this.data.about.description.replace(/\n/g, '<br>');
                html = html.replace(/src="uploads\//g, 'src="admin/uploads/');
                el.innerHTML = html;
            }
        });
        clientsElements.forEach(el => {
            if (this.data.about?.clients) el.textContent = this.data.about.clients;
        });
        repairsElements.forEach(el => {
            if (this.data.about?.repairs) el.textContent = this.data.about.repairs;
        });
        imageElements.forEach(el => {
            if (this.data.about?.image) {
                el.setAttribute('src', this.getImagePath(this.data.about.image, 'img/about.jpg'));
            }
        });
    }

    renderFeatures() {
        const container = document.getElementById("features-container");
        if (!container || !this.data.features || this.data.features.length === 0) return;

        container.innerHTML = this.data.features.map((f, i) => `
            <div class="col-md-6 col-lg-3 wow fadeIn" data-wow-delay="${0.1 + (i * 0.2)}s">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <div class="d-flex align-items-center justify-content-center bg-light" style="width: 60px; height: 60px;">
                        <i class="fa ${f.icon} fa-2x text-primary"></i>
                    </div>
                    <h1 class="display-1 text-light mb-0">0${i + 1}</h1>
                </div>
                <h5>${f.title}</h5>
            </div>
        `).join('');
    }

    renderTeam() {
        const container = document.getElementById("team-container");
        if (!container || !this.data.team || this.data.team.length === 0) {
            if (container) container.innerHTML = '<div class="col-12 text-center py-5 text-muted">No team members to display.</div>';
            return;
        }

        console.log("Boost Stride: Rendering Team", this.data.team);

        container.innerHTML = this.data.team.map((m, i) => {
            const imagePath = this.getImagePath(m.image, 'img/team-1.jpg');
            // Handle long designations by truncating or adjusting font size if needed
            const designation = m.designation || m.role || 'Expert';
            
            return `
                <div class="col-lg-3 col-md-6 wow fadeInUp" data-wow-delay="${0.1 + (i % 4 * 0.2)}s">
                    <div class="team-item h-100 shadow-sm border-0">
                        <div class="overflow-hidden position-relative">
                            <img class="img-fluid w-100" src="${imagePath}" alt="${m.name}" style="height:350px; object-fit:cover;">
                            <div class="team-social">
                                ${m.facebook_url ? `<a class="btn btn-square" href="${m.facebook_url}" target="_blank"><i class="fab fa-facebook-f"></i></a>` : ''}
                                ${m.twitter_url ? `<a class="btn btn-square" href="${m.twitter_url}" target="_blank"><i class="fab fa-twitter"></i></a>` : ''}
                                ${m.instagram_url ? `<a class="btn btn-square" href="${m.instagram_url}" target="_blank"><i class="fab fa-instagram"></i></a>` : ''}
                            </div>
                        </div>
                        <div class="text-center border border-5 border-light border-top-0 p-4 bg-white">
                            <h5 class="mb-1 fw-bold text-dark">${m.name}</h5>
                            <small class="text-primary text-uppercase fw-bold letter-spacing-1" style="font-size: 0.75rem;">${designation}</small>
                        </div>
                    </div>
                </div>
            `;
        }).join('');
    }
}

// Global Export - Switch to live API for dynamic updates
window.BoostStride = new ContentLoader('api/get_content.php');
document.addEventListener('DOMContentLoaded', () => window.BoostStride.init());
