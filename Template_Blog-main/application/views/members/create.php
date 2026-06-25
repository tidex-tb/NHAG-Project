<?php $this->load->view('layouts/header', array('show_sidebar' => true, 'page_title' => 'Create Member', 'current_page' => 'members', 'is_admin' => true)); ?>

<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-user-plus"></i> Register New Member</h2>
    <a href="<?php echo base_url('members'); ?>" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left"></i> Back to Members
    </a>
</div>

<!-- Alert Messages -->
<?php if ($this->session->flashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle"></i> <?php echo $this->session->flashdata('error'); ?>
        <button type="button" class="close" data-dismiss="alert">
            <span>&times;</span>
        </button>
    </div>
<?php endif; ?>

<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="card">
            <div class="card-body p-5">
                <form method="POST" action="<?php echo base_url('members/create'); ?>" id="memberForm">
                    <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">

                    <!-- Personal Information -->
                    <h5 class="mb-3 pb-2 border-bottom"><i class="fas fa-user"></i> Personal Information</h5>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="name">Full Name *</label>
                            <input type="text" class="form-control <?php echo form_error('name') ? 'is-invalid' : ''; ?>"
                                id="name" name="name" placeholder="John Doe"
                                value="<?php echo set_value('name'); ?>" required>
                            <?php if (form_error('name')): ?>
                                <div class="invalid-feedback d-block"><?php echo form_error('name'); ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="form-group col-md-6">
                            <label for="id_number">ID Number *</label>
                            <input type="text" class="form-control <?php echo form_error('id_number') ? 'is-invalid' : ''; ?>"
                                id="id_number" name="id_number" placeholder="ID-123456"
                                value="<?php echo set_value('id_number'); ?>" required>
                            <?php if (form_error('id_number')): ?>
                                <div class="invalid-feedback d-block"><?php echo form_error('id_number'); ?></div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="email">Email Address *</label>
                            <input type="email" class="form-control <?php echo form_error('email') ? 'is-invalid' : ''; ?>"
                                id="email" name="email" placeholder="john@example.com"
                                value="<?php echo set_value('email'); ?>" required>
                            <?php if (form_error('email')): ?>
                                <div class="invalid-feedback d-block"><?php echo form_error('email'); ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="form-group col-md-6">
                            <label for="phone_number">Phone Number</label>
                            <input type="tel" class="form-control <?php echo form_error('phone_number') ? 'is-invalid' : ''; ?>"
                                id="phone_number" name="phone_number" placeholder="+264 61 123456"
                                value="<?php echo set_value('phone_number'); ?>">
                            <?php if (form_error('phone_number')): ?>
                                <div class="invalid-feedback d-block"><?php echo form_error('phone_number'); ?></div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="date_of_birth">Date of Birth</label>
                            <input type="date" class="form-control <?php echo form_error('date_of_birth') ? 'is-invalid' : ''; ?>"
                                id="date_of_birth" name="date_of_birth"
                                value="<?php echo set_value('date_of_birth'); ?>">
                            <?php if (form_error('date_of_birth')): ?>
                                <div class="invalid-feedback d-block"><?php echo form_error('date_of_birth'); ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="form-group col-md-6">
                            <label for="gender">Gender</label>
                            <select class="form-control" id="gender" name="gender">
                                <option value="">-- Select --</option>
                                <option value="M" <?php echo set_value('gender') === 'M' ? 'selected' : ''; ?>>Male</option>
                                <option value="F" <?php echo set_value('gender') === 'F' ? 'selected' : ''; ?>>Female</option>
                                <option value="O" <?php echo set_value('gender') === 'O' ? 'selected' : ''; ?>>Other</option>
                            </select>
                        </div>
                    </div>

                    <hr class="my-4">

                    <!-- Employment Information -->
                    <h5 class="mb-3 pb-2 border-bottom"><i class="fas fa-briefcase"></i> Employment Information</h5>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="employment_status">Employment Status</label>
                            <select class="form-control" id="employment_status" name="employment_status">
                                <option value="">-- Select Status --</option>
                                <option value="employed" <?php echo set_value('employment_status') === 'employed' ? 'selected' : ''; ?>>Employed</option>
                                <option value="self_employed" <?php echo set_value('employment_status') === 'self_employed' ? 'selected' : ''; ?>>Self Employed</option>
                                <option value="unemployed" <?php echo set_value('employment_status') === 'unemployed' ? 'selected' : ''; ?>>Unemployed</option>
                                <option value="retired" <?php echo set_value('employment_status') === 'retired' ? 'selected' : ''; ?>>Retired</option>
                            </select>
                        </div>

                        <div class="form-group col-md-6">
                            <label for="employer">Employer/Organization</label>
                            <input type="text" class="form-control" id="employer" name="employer"
                                placeholder="Company Name" value="<?php echo set_value('employer'); ?>">
                        </div>
                    </div>

                    <hr class="my-4">

                    <!-- Account Information -->
                    <h5 class="mb-3 pb-2 border-bottom"><i class="fas fa-lock"></i> Account Information</h5>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="username">Username *</label>
                            <input type="text" class="form-control <?php echo form_error('username') ? 'is-invalid' : ''; ?>"
                                id="username" name="username" placeholder="johndoe"
                                value="<?php echo set_value('username'); ?>" required>
                            <small class="form-text text-muted">At least 4 characters, no spaces</small>
                            <?php if (form_error('username')): ?>
                                <div class="invalid-feedback d-block"><?php echo form_error('username'); ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="form-group col-md-6">
                            <label for="password">Password *</label>
                            <input type="password" class="form-control <?php echo form_error('password') ? 'is-invalid' : ''; ?>"
                                id="password" name="password" placeholder="Enter a strong password" required>
                            <small class="form-text text-muted">At least 8 characters</small>
                            <?php if (form_error('password')): ?>
                                <div class="invalid-feedback d-block"><?php echo form_error('password'); ?></div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <hr class="my-4">

                    <!-- Form Actions -->
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-check"></i> Create Member Account
                        </button>
                        <a href="<?php echo base_url('members'); ?>" class="btn btn-outline-secondary btn-lg">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php $this->load->view('layouts/footer'); ?>
