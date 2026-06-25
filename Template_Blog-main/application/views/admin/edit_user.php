<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-user-edit"></i> Edit User</h2>
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
            <div class="card-header"><h6 class="mb-0">Editing: <?php echo htmlspecialchars($user['username']); ?></h6></div>
            <div class="card-body p-4">
                <form method="POST" action="<?php echo base_url('admin/users/edit/' . $user['id']); ?>">
                    <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
                    <div class="form-group">
                        <label>Full Name *</label>
                        <input type="text" class="form-control" name="name" value="<?php echo set_value('name', $user['name']); ?>" required>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>Username</label>
                            <input type="text" class="form-control" value="<?php echo htmlspecialchars($user['username']); ?>" disabled>
                            <small class="text-muted">Username cannot be changed</small>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Phone Number</label>
                            <input type="tel" class="form-control" name="phone_number" value="<?php echo set_value('phone_number', $user['phone_number']); ?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Role *</label>
                        <select class="form-control" name="role_id" required>
                            <option value="">-- Select Role --</option>
                            <?php if (!empty($roles)): ?>
                                <?php foreach ($roles as $role): ?>
                                    <option value="<?php echo $role['id']; ?>" <?php echo (set_value('role_id', $user['role_id']) == $role['id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($role['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>New Password <small class="text-muted">(leave blank to keep current)</small></label>
                        <input type="password" class="form-control" name="password" minlength="6" placeholder="Leave blank to keep password">
                    </div>
                    <hr>
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="fas fa-save"></i> Save Changes
                    </button>
                    <a href="<?php echo base_url('admin/users'); ?>" class="btn btn-outline-secondary btn-lg">Cancel</a>
                </form>
            </div>
        </div>
    </div>
</div>
