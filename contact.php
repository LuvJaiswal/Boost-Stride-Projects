<?php include 'includes/header.php'; ?>
<?php include 'includes/navbar.php'; ?>

    <!-- Page Header Start -->
    <div class="container-fluid page-header py-5 mb-5">
        <div class="container py-5">
            <h1 class="display-3 text-white mb-3 animated slideInDown">Contact Us</h1>
            <nav aria-label="breadcrumb animated slideInDown">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a class="text-white" href="index.php">Home</a></li>
                    <li class="breadcrumb-item"><a class="text-white" href="#">Pages</a></li>
                    <li class="breadcrumb-item text-white active" aria-current="page">Contact</li>
                </ol>
            </nav>
        </div>
    </div>
    <!-- Page Header End -->


    <!-- Contact Start -->
    <div class="container-fluid bg-light overflow-hidden px-lg-0" style="margin: 6rem 0;">
        <div class="container contact px-lg-0">
            <div class="row g-0 mx-lg-0">
                <div class="col-lg-6 contact-text py-5 wow fadeIn" data-wow-delay="0.5s">
                    <div class="p-lg-5 ps-lg-0">
                        <div class="section-title text-start">
                            <h1 class="display-5 mb-4">Get In Touch</h1>
                        </div>
                        <p class="mb-4">Have questions about your vehicle? Our expert team is here to help. Send us a message or visit our shop during business hours.</p>
                        
                        <!-- Response Message Placeholder -->
                        <div id="contactResponse" class="mb-4" style="display: none;"></div>

                        <form id="contactForm">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="text" name="name" class="form-control" id="name" placeholder="Your Name" required>
                                        <label for="name">Your Name</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="email" name="email" class="form-control" id="email" placeholder="Your Email" required>
                                        <label for="email">Your Email</label>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-floating">
                                        <input type="text" name="subject" class="form-control" id="subject" placeholder="Subject" required>
                                        <label for="subject">Subject</label>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-floating">
                                        <textarea name="message" class="form-control" placeholder="Leave a message here" id="message"
                                            style="height: 150px" required></textarea>
                                        <label for="message">Message</label>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <button id="submitBtn" class="btn btn-primary w-100 py-3 fw-bold shadow-sm" type="submit">
                                        <span class="btn-text">Send Message</span>
                                        <span class="spinner-border spinner-border-sm ms-2" style="display: none;"></span>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="col-lg-6 pe-lg-0" style="min-height: 400px;">
                    <div class="position-relative h-100">
                        <iframe class="position-absolute w-100 h-100" style="object-fit: cover; border: 0;"
                            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3001156.4288297426!2d-78.01371936852176!3d42.72876761954724!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x4ccc4bf0f123a5a9%3A0xddcfc6c1de189567!2sNew%20York%2C%20USA!5e0!3m2!1sen!2sbd!4v1603794290143!5m2!1sen!2sbd"
                            allowfullscreen="" aria-hidden="false" tabindex="0"></iframe>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Contact End -->

    <script>
    document.getElementById('contactForm')?.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const form = this;
        const btn = document.getElementById('submitBtn');
        const btnText = btn.querySelector('.btn-text');
        const spinner = btn.querySelector('.spinner-border');
        const responseDiv = document.getElementById('contactResponse');
        
        // UI Loading State
        btn.disabled = true;
        btnText.textContent = 'Processing...';
        spinner.style.display = 'inline-block';
        
        const formData = new FormData(form);
        const data = Object.fromEntries(formData.entries());
        
        try {
            const response = await fetch('api/contact_handler.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            });
            
            const result = await response.json();
            
            responseDiv.style.display = 'block';
            if (result.status === 'success') {
                responseDiv.className = 'alert alert-success border-0 shadow-sm rounded-4 py-3 animated fadeIn';
                responseDiv.innerHTML = `<i class="fas fa-check-circle me-2"></i><b>Thank you!</b> ${result.message}`;
                form.reset();
            } else {
                responseDiv.className = 'alert alert-danger border-0 shadow-sm rounded-4 py-3 animated fadeIn';
                responseDiv.innerHTML = `<i class="fas fa-exclamation-circle me-2"></i><b>Error:</b> ${result.message}`;
            }
        } catch (error) {
            responseDiv.style.display = 'block';
            responseDiv.className = 'alert alert-danger border-0 shadow-sm rounded-4 py-3 animated fadeIn';
            responseDiv.innerHTML = `<i class="fas fa-wifi me-2"></i><b>Network Error:</b> Could not connect to lead server.`;
        } finally {
            btn.disabled = false;
            btnText.textContent = 'Send Message';
            spinner.style.display = 'none';
        }
    });
    </script>

<?php include 'includes/footer.php'; ?>
