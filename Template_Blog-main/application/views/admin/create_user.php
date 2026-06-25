<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-user-plus"></i> Add New User</h2>
    <a href="<?php echo base_url('admin/users'); ?>" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left"></i> Back to Users
    </a>
</div>

<?php if (isset($errors) && !empty($errors)): ?>
    <div class="alert alert-danger">
        <?php foreach ($errors as $e): ?><div><?php echo $e; ?></div><?php endforeach; ?>
    </div>
<?php endif; ?>

<div class="row">
    <div class="col-md-7 mx-auto">
        <div class="card">
            <div class="card-body p-4">
                <form method="POST" action="<?php echo base_url('admin/users/create'); ?>">
                    <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
                    <div class="form-group">
                        <label>Full Name *</label>
                        <input type="text" class="form-control" name="name" value="<?php echo set_value('name'); ?>" required>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>Username *</label>
                            <input type="text" class="form-control" name="username" value="<?php echo set_value('username'); ?>" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Email *</label>
                            <input type="email" class="form-control" name="email" value="<?php echo set_value('email'); ?>" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>Password *</label>
                            <input type="password" class="form-control" name="password" required minlength="6">
                        </div>
                        <div class="form-group col-md-6">
                            <label>Phone Number</label>
                            <input type="tel" class="form-control" name="phone_number" value="<?php echo set_value('phone_number'); ?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Role *</label>
                        <select class="form-control" name="role_id" required>
                            <option value="">-- Select Role --</option>
                            <?php if (!empty($roles)): ?>
                                <?php foreach ($roles as $role): ?>
                                    <option value="<?php echo $role['id']; ?>" <?php echo set_value('role_id') == $role['id'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($role['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                    <hr>
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="fas fa-save"></i> Create User
                    </button>
                    <a href="<?php echo base_url('admin/users'); ?>" class="btn btn-outline-secondary btn-lg">Cancel</a>
                </form>
            </div>
        </div>
    </div>
</div>
