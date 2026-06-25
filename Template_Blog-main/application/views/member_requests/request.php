<?php $this->load->view('layouts/header', array('show_sidebar' => true, 'page_title' => 'Request Member Profile', 'current_page' => 'dashboard', 'is_admin' => false)); ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-id-card"></i> Request Member Profile</h2>
    <a href="<?php echo base_url('dashboard'); ?>" class="btn btn-outline-secondary"><i class="fas fa-arrow-left"></i> Dashboard</a>
</div>

<?php if ($this->session->flashdata('error')): ?>
    <div class="alert alert-danger"><?php echo html_escape($this->session->flashdata('error')); ?></div>
<?php endif; ?>

<?php echo validation_errors('<div class="alert alert-danger">', '</div>'); ?>

<?php if (!empty($pending_request)): ?>
    <div class="alert alert-info">
        <i class="fas fa-clock"></i> Your request is already pending administrator review. You will receive a notification once it is approved or rejected.
    </div>
<?php else: ?>
    <div class="card">
        <div class="card-header"><h6 class="mb-0">Member Information</h6></div>
        <div class="card-body">
            <form method="POST" action="<?php echo base_url('member/request'); ?>">
                <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>First Name</label>
                        <input type="text" class="form-control" name="first_name" value="<?php echo set_value('first_name', isset($user['name']) ? strtok($user['name'], ' ') : ''); ?>" required>
                    </div>
                    <div class="form-group col-md-6">
                        <label>Last Name</label>
                        <input type="text" class="form-control" name="last_name" value="<?php echo set_value('last_name'); ?>" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-4">
                        <label>Gender</label>
                        <select class="form-control" name="gender">
                            <option value="Other">Other</option>
                            <option value="Female">Female</option>
                            <option value="Male">Male</option>
                        </select>
                    </div>
                    <div class="form-group col-md-4">
                        <label>Date of Birth</label>
                        <input type="date" class="form-control" name="date_of_birth" value="<?php echo set_value('date_of_birth'); ?>">
                    </div>
                    <div class="form-group col-md-4">
                        <label>National ID</label>
                        <input type="text" class="form-control" name="national_id" value="<?php echo set_value('national_id'); ?>" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Email</label>
                        <input type="email" class="form-control" name="email" value="<?php echo set_value('email', $user['email'] ?? ''); ?>" required>
                    </div>
                    <div class="form-group col-md-6">
                        <label>Phone Number</label>
                        <input type="text" class="form-control" name="phone_number" value="<?php echo set_value('phone_number'); ?>" required>
                    </div>
                </div>
                <div class="form-group">
                    <label>Physical Address</label>
                    <textarea class="form-control" name="physical_address" rows="3"><?php echo set_value('physical_address'); ?></textarea>
                </div>
                <button type="submit" class="btn btn-primary"><i class="fas fa-paper-plane"></i> Submit Request</button>
            </form>
        </div>
    </div>
<?php endif; ?>

<?php $this->load->view('layouts/footer'); ?>
