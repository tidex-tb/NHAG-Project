            </div>
            </div>
            </div>

            <!-- Footer -->
            <footer class="footer">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-4 text-md-left text-center mb-3 mb-md-0">
                            <strong>NHAG Savings Management Platform</strong><br>
                            <small>&copy; 2026 Namibia Housing Action Group</small>
                        </div>
                        <div class="col-md-4 text-center">
                            <small>
                                <a href="#" style="color: rgba(255,255,255,0.8);">Help</a> |
                                <a href="#" style="color: rgba(255,255,255,0.8);">Documentation</a> |
                                <a href="#" style="color: rgba(255,255,255,0.8);">Support</a>
                            </small>
                        </div>
                        <div class="col-md-4 text-md-right text-center">
                            <small>
                                Email: <a href="mailto:support@nhag.org.na" style="color: rgba(255,255,255,0.8);">support@nhag.org.na</a><br>
                                Phone: <a href="tel:+264611234567" style="color: rgba(255,255,255,0.8);">+264 61 123456</a>
                            </small>
                        </div>
                    </div>
                </div>
            </footer>

            <script>
                window.baseUrl = '<?php echo base_url(); ?>';
                window.csrfTokenName = '<?php echo $this->security->get_csrf_token_name(); ?>';
                window.csrfHash = '<?php echo $this->security->get_csrf_hash(); ?>';
            </script>

            <!-- Custom JS -->
            <script src="<?php echo base_url('assets/js/main.js'); ?>"></script>

            <script>
                // Initialize tooltips and popovers
                $(function() {
                    $('[data-toggle="tooltip"]').tooltip();
                    $('[data-toggle="popover"]').popover();
                });
            </script>
            </body>

            </html>
