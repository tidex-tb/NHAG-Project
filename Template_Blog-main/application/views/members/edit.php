<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-edit"></i> Edit Member</h2>
    <a href="<?php echo base_url('members/view/' . $member['id']); ?>" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left"></i> Back to Profile
    </a>
</div>

<?php if ($this->session->flashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show">
        <i class="fas fa-exclamation-circle"></i> <?php echo $this->session->flashdata('error'); ?>
        <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
    </div>
<?php endif; ?>
<?php if (isset($errors) && !empty($errors)): ?>
    <div class="alert alert-danger">
        <?php foreach ($errors as $e): ?><div><?php echo $e; ?></div><?php endforeach; ?>
    </div>
<?php endif; ?>

<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="card">
            <div class="card-header"><h6 class="mb-0">Member Information — <?php echo $member['member_number']; ?></h6></div>
            <div class="card-body p-4">
                <form method="POST" action="<?php echo base_url('members/edit/' . $member['id']); ?>">
                    <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">

                    <h6 class="mb-3 pb-2 border-bottom"><i class="fas fa-user"></i> Personal Details</h6>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>First Name *</label>
                            <input type="text" class="form-control" name="first_name" value="<?php echo set_value('first_name', $member['first_name']); ?>" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Last Name *</label>
                            <input type="text" class="form-control" name="last_name" value="<?php echo set_value('last_name', $member['last_name']); ?>" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>Gender</label>
                            <select class="form-control" name="gender">
                                <option value="Male" <?php echo $member['gender'] === 'Male' ? 'selected' : ''; ?>>Male</option>
                                <option value="Female" <?php echo $member['gender'] === 'Female' ? 'selected' : ''; ?>>Female</option>
                                <option value="Other" <?php echo $member['gender'] === 'Other' ? 'selected' : ''; ?>>Other</option>
                            </select>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Phone Number</label>
                            <input type="tel" class="form-control" name="phone_number" value="<?php echo set_value('phone_number', $member['phone_number']); ?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" class="form-control" name="email" value="<?php echo set_value('email', $member['email']); ?>">
                    </div>
                    <div class="form-group">
                        <label>Physical Address</label>
                        <textarea class="form-control" name="physical_address" rows="2"><?php echo set_value('physical_address', $member['physical_address']); ?></textarea>
                    </div>

                    <hr class="my-4">
                    <h6 class="mb-3 pb-2 border-bottom"><i class="fas fa-users"></i> Next of Kin</h6>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>Next of Kin Name</label>
                            <input type="text" class="form-control" name="next_of_kin_name" value="<?php echo set_value('next_of_kin_name', $member['next_of_kin_name']); ?>">
                        </div>
                        <div class="form-group col-md-6">
                            <label>Relationship</label>
                            <input type="text" class="form-control" name="next_of_kin_relationship" value="<?php echo set_value('next_of_kin_relationship', $member['next_of_kin_relationship']); ?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Next of Kin Phone</label>
                        <input type="tel" class="form-control" name="next_of_kin_phone" value="<?php echo set_value('next_of_kin_phone', $member['next_of_kin_phone']); ?>">
                    </div>

                    <hr class="my-4">
                    <h6 class="mb-3 pb-2 border-bottom"><i class="fas fa-briefcase"></i> Employment</h6>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>Employment Status</label>
                            <select class="form-control" name="employment_status">
                                <?php foreach (['Employed','Self-Employed','Unemployed','Student','Retired'] as $s): ?>
                                    <option value="<?php echo $s; ?>" <?php echo $member['employment_status'] === $s ? 'selected' : ''; ?>><?php echo $s; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Employer Name</label>
                            <input type="text" class="form-control" name="employer_name" value="<?php echo set_value('employer_name', $member['employer_name']); ?>">
                        </div>
                    </div>
                    <div class="form-group col-md-6 px-0">
                        <label>Monthly Income</label>
                        <input type="number" step="0.01" class="form-control" name="monthly_income" value="<?php echo set_value('monthly_income', $member['monthly_income']); ?>">
                    </div>

                    <hr class="my-4">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="fas fa-save"></i> Save Changes
                    </button>
                    <a href="<?php echo base_url('members/view/' . $member['id']); ?>" class="btn btn-outline-secondary btn-lg">Cancel</a>
                </form>
            </div>
        </div>
    </div>
</div>
