            </main>
        </div>
    </div>
    <!-- JQuery and Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Summernote JS -->
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.summernote').summernote({
                placeholder: 'Start building your professional content here... Tip: Click the image icon to upload site assets!',
                tabsize: 2,
                height: 400,
                dialogsInBody: true,
                dialogsFade: true,
                toolbar: [
                    ['blocks', ['wp_blocks']],
                    ['style', ['style']],
                    ['font', ['bold', 'italic', 'underline', 'strikethrough', 'color', 'clear']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['table', ['table']],
                    ['insert', ['link', 'picture', 'video', 'hr']],
                    ['view', ['fullscreen', 'codeview', 'help']]
                ],
                buttons: {
                    wp_blocks: function (context) {
                        var ui = $.summernote.ui;
                        var button = ui.buttonGroup([
                            ui.button({
                                className: 'dropdown-toggle',
                                contents: '<i class="fas fa-cubes me-1"></i> Blocks',
                                tooltip: 'Insert WordPress-style Blocks',
                                data: { toggle: 'dropdown' }
                            }),
                            ui.dropdown({
                                className: 'dropdown-style wp-blocks-dropdown',
                                items: [
                                    'col-2|2-Column Layout',
                                    'col-3|3-Column Grid',
                                    'cta|Call-to-Action Section',
                                    'info-box|Feature Highlight',
                                    'quote|Premium Blockquote',
                                    'divider|Modern Divider'
                                ],
                                template: function (item) {
                                    var parts = item.split('|');
                                    var icons = {
                                        'col-2': 'fa-columns text-primary',
                                        'col-3': 'fa-th-large text-info',
                                        'cta': 'fa-bullhorn text-danger',
                                        'info-box': 'fa-lightbulb text-success',
                                        'quote': 'fa-quote-left text-warning',
                                        'divider': 'fa-minus text-muted'
                                    };
                                    return '<i class="fas ' + icons[parts[0]] + ' me-2"></i>' + parts[1];
                                },
                                click: function (event) {
                                    var $target = $(event.target);
                                    var $item = $target.is('a') ? $target : $target.closest('a');
                                    var val = $item.data('value').split('|')[0];
                                    var html = '';
                                    
                                    if (val === 'col-2') {
                                        html = '<div class="row my-5"><div class="col-md-6 mb-4 mb-md-0"><h4 class="fw-bold">Left Heading</h4><p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis.</p></div><div class="col-md-6"><h4 class="fw-bold">Right Heading</h4><p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis.</p></div></div><p><br></p>';
                                    } else if (val === 'col-3') {
                                        html = '<div class="row my-5"><div class="col-md-4 mb-4 mb-md-0"><h5 class="fw-bold">Column 1</h5><p>Sample text for first column.</p></div><div class="col-md-4 mb-4 mb-md-0"><h5 class="fw-bold">Column 2</h5><p>Sample text for second column.</p></div><div class="col-md-4"><h5 class="fw-bold">Column 3</h5><p>Sample text for third column.</p></div></div><p><br></p>';
                                    } else if (val === 'cta') {
                                        html = '<div class="p-5 my-5 text-center bg-primary text-white rounded-5 shadow-lg"><h2>Ready to Start Your Project?</h2><p class="lead mb-4">Contact our expert team today for a premium consulting session.</p><a href="contact.php" class="btn btn-light btn-lg rounded-pill px-5">Get In Touch</a></div><p><br></p>';
                                    } else if (val === 'info-box') {
                                        html = '<div class="card border-0 shadow-sm p-4 my-5 bg-light rounded-4 overflow-hidden position-relative" style="border-left: 5px solid #4f46e5 !important;"><div class="d-flex gap-4"><div><div class="stat-icon bg-white shadow-sm text-primary"><i class="fas fa-rocket"></i></div></div><div><h4 class="fw-bold">Feature Title</h4><p class="text-muted mb-0">Describe your amazing service or feature with enough detail to inspire your customers.</p></div></div></div><p><br></p>';
                                    } else if (val === 'quote') {
                                        html = '<blockquote class="wp-block-quote p-5 my-5 border-0 bg-white shadow-sm rounded-4 position-relative" style="border-left: 8px solid #f59e0b !important;"><i class="fas fa-quote-left fa-3x position-absolute top-0 start-0 opacity-10 mt-3 ms-3"></i><p class="h3 fst-italic mb-3">"This is where your client\'s powerful testimonial or an inspiring quote goes. It looks amazing and builds trust."</p><cite class="fw-bold text-uppercase tracking-wider">— Success Client</cite></blockquote><p><br></p>';
                                    } else if (val === 'divider') {
                                        html = '<div class="py-5 d-flex align-items-center gap-3"><div class="flex-grow-1 border-bottom"></div><i class="fas fa-star text-muted opacity-25"></i><div class="flex-grow-1 border-bottom"></div></div><p><br></p>';
                                    }
                                    
                                    context.invoke('editor.pasteHTML', html);
                                    event.preventDefault();
                                }
                            })
                        ]);
                        return button.render();
                    }
                },
                callbacks: {
                    onImageUpload: function(files) {
                        for (let i = 0; i < files.length; i++) {
                            uploadImage(files[i], this);
                        }
                    }
                }
            });

            // Prevent Summernote buttons from accidentally submitting the main form
            $(document).on('click', '.note-btn, .note-editor button', function(e) {
                if ($(this).attr('type') !== 'submit' && !$(this).hasClass('dropdown-toggle')) {
                    e.preventDefault();
                }
            });

            function uploadImage(file, editor) {
                let data = new FormData();
                data.append("image", file);
                $.ajax({
                    data: data,
                    type: "POST",
                    url: "api/upload_handler.php",
                    cache: false,
                    contentType: false,
                    processData: false,
                    success: function(url) {
                        // The URL returned is 'uploads/filename.jpg'
                        // This works in the admin editor because it's at /admin/
                        $(editor).summernote('insertImage', url); 
                    },
                    error: function(data) {
                        console.error("Upload Error:", data);
                        alert("Image upload failed. Please check the file size and type.");
                    }
                });
            }
        });
    </script>
</body>
</html>
