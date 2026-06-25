<?php $this->load->view('layouts/header', array('show_sidebar' => false, 'page_title' => 'Login')); ?>

<div class="container-fluid" style="background: linear-gradient(135deg, #2c3e50 0%, #3498db 100%); min-height: 100vh; display: flex; align-items: center; justify-content: center;">
    <div class="row w-100">
        <div class="col-md-6 mx-auto col-lg-4">
            <!-- Logo Section -->
            <div class="text-center mb-4">
                <div style="font-size: 3rem; color: white;">
                    <i class="fas fa-piggy-bank"></i>
                </div>
                <h2 style="color: white; margin-top: 10px;">NHAG Savings</h2>
                <p style="color: rgba(255,255,255,0.8);">Management Platform</p>
            </div>

            <!-- Login Card -->
            <div class="card" style="border-radius: 12px; box-shadow: 0 10px 40px rgba(0,0,0,0.2);">
                <div class="card-body p-5">
                    <h4 class="card-title text-center mb-4">Member Login</h4>

                    <!-- Error Messages -->
                    <?php if ($this->session->flashdata('error')): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle"></i> <?php echo $this->session->flashdata('error'); ?>
                            <button type="button" class="close" data-dismiss="alert">
                                <span>&times;</span>
                            </button>
                        </div>
                    <?php endif; ?>

                    <!-- Success Messages -->
                    <?php if ($this->session->flashdata('success')): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle"></i> <?php echo $this->session->flashdata('success'); ?>
                            <button type="button" class="close" data-dismiss="alert">
                                <span>&times;</span>
                            </button>
                        </div>
                    <?php endif; ?>

                    <!-- Form -->
                    <form method="POST" action="<?php echo base_url('auth/login'); ?>">
                        <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
                        <div class="form-group mb-3">
                            <label for="username" class="form-label">
                                <i class="fas fa-user"></i> Username or Email
                            </label>
                            <input type="text" class="form-control <?php echo form_error('username') ? 'is-invalid' : ''; ?>"
                                id="username" name="username" placeholder="Enter your username or email"
                                value="<?php echo set_value('username'); ?>" required>
                            <?php if (form_error('username')): ?>
                                <div class="invalid-feedback d-block"><?php echo form_error('username'); ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="form-group mb-3">
                            <label for="password" class="form-label">
                                <i class="fas fa-lock"></i> Password
                            </label>
                            <input type="password" class="form-control <?php echo form_error('password') ? 'is-invalid' : ''; ?>"
                                id="password" name="password" placeholder="Enter your password" required>
                            <?php if (form_error('password')): ?>
                                <div class="invalid-feedback d-block"><?php echo form_error('password'); ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="remember" name="remember" value="1">
                            <label class="form-check-label" for="remember">
                                Remember me
                            </label>
                        </div>

                        <button type="submit" class="btn btn-primary btn-block" style="background-color: #3498db; border: none; padding: 10px; font-weight: 600;">
                            <i class="fas fa-sign-in-alt"></i> Login
                        </button>
                    </form>

                    <hr class="my-4">

                    <!-- Register Link -->
                    <p class="text-center mb-0">
                        Don't have an account?
                        <a href="<?php echo base_url('auth/register'); ?>" style="color: #3498db; font-weight: 600;">
                            Register Here
                        </a>
                    </p>
                </div>
            </div>

            <!-- Help Text -->
            <div class="text-center mt-4" style="color: white;">
                <small>
                    <i class="fas fa-question-circle"></i>
                    Need help? Contact support@nhag.org.na
                </small>
            </div>
        </div>
    </div>
</div>

<?php $this->load->view('layouts/footer'); ?>
