<?php $this->load->view('layouts/header', array('show_sidebar' => false, 'page_title' => 'Register')); ?>

<div class="container-fluid" style="background: linear-gradient(135deg, #2c3e50 0%, #3498db 100%); min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 32px 16px;">
    <div class="row w-100">
        <div class="col-md-8 mx-auto col-lg-5">
            <div class="text-center mb-4">
                <div style="font-size: 3rem; color: white;">
                    <i class="fas fa-piggy-bank"></i>
                </div>
                <h2 style="color: white; margin-top: 10px;">NHAG Savings</h2>
                <p style="color: rgba(255,255,255,0.8);">Create your member account</p>
            </div>

            <div class="card" style="border-radius: 12px; box-shadow: 0 10px 40px rgba(0,0,0,0.2);">
                <div class="card-body p-5">
                    <h4 class="card-title text-center mb-4">Register</h4>

                    <?php if ($this->session->flashdata('error')): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle"></i> <?php echo $this->session->flashdata('error'); ?>
                            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                        </div>
                    <?php endif; ?>

                    <?php echo validation_errors('<div class="alert alert-danger">', '</div>'); ?>

                    <?php echo form_open('auth/register'); ?>
                        <div class="form-group mb-3">
                            <label for="name"><i class="fas fa-user"></i> Full Name</label>
                            <input type="text" class="form-control" id="name" name="name" value="<?php echo set_value('name'); ?>" placeholder="Enter your full name" required>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-6 mb-3">
                                <label for="username"><i class="fas fa-at"></i> Username</label>
                                <input type="text" class="form-control" id="username" name="username" value="<?php echo set_value('username'); ?>" placeholder="Choose a username" required>
                            </div>
                            <div class="form-group col-md-6 mb-3">
                                <label for="email"><i class="fas fa-envelope"></i> Email</label>
                                <input type="email" class="form-control" id="email" name="email" value="<?php echo set_value('email'); ?>" placeholder="email@example.com" required>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-6 mb-3">
                                <label for="national_id"><i class="fas fa-id-card"></i> National ID</label>
                                <input type="text" class="form-control" id="national_id" name="national_id" value="<?php echo set_value('national_id'); ?>" placeholder="Enter national ID" required>
                            </div>
                            <div class="form-group col-md-6 mb-3">
                                <label for="phone_number"><i class="fas fa-phone"></i> Phone Number</label>
                                <input type="text" class="form-control" id="phone_number" name="phone_number" value="<?php echo set_value('phone_number'); ?>" placeholder="Enter phone number" required>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-6 mb-3">
                                <label for="password"><i class="fas fa-lock"></i> Password</label>
                                <input type="password" class="form-control" id="password" name="password" placeholder="Minimum 6 characters" required>
                            </div>
                            <div class="form-group col-md-6 mb-3">
                                <label for="confirm_password"><i class="fas fa-lock"></i> Confirm Password</label>
                                <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="Confirm password" required>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary btn-block" style="background-color: #3498db; border: none; padding: 10px; font-weight: 600;">
                            <i class="fas fa-user-plus"></i> Create Account
                        </button>
                    <?php echo form_close(); ?>

                    <hr class="my-4">
                    <p class="text-center mb-0">
                        Already have an account?
                        <a href="<?php echo base_url('auth/login'); ?>" style="color: #3498db; font-weight: 600;">Login Here</a>
                    </p>
                </div>
            </div>

            <div class="text-center mt-4" style="color: white;">
                <small><i class="fas fa-question-circle"></i> Need help? Contact support@nhag.org.na</small>
            </div>
        </div>
    </div>
</div>

<?php $this->load->view('layouts/footer'); ?>
