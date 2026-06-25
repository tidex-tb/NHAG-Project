<?php $is_super_admin = (int) $this->session->userdata('role_id') === 1; ?>

<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-cog"></i> System Administration</h2>
</div>

<?php if ($this->session->flashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show">
        <i class="fas fa-check-circle"></i> <?php echo $this->session->flashdata('success'); ?>
        <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
    </div>
<?php endif; ?>

<div class="row">
    <div class="col-md-4 mb-4">
        <div class="card h-100">
            <div class="card-header"><h6 class="mb-0"><i class="fas fa-user-tie"></i> User Management</h6></div>
            <div class="card-body d-flex flex-column">
                <p class="text-muted">Create and manage system users, assign operational roles, and keep accounts active.</p>
                <div class="mt-auto">
                    <a href="<?php echo base_url('admin/users'); ?>" class="btn btn-primary btn-block">
                        <i class="fas fa-users"></i> Manage Users
                    </a>
                    <a href="<?php echo base_url('admin/users/create'); ?>" class="btn btn-outline-primary btn-block mt-2">
                        <i class="fas fa-user-plus"></i> Add New User
                    </a>
                </div>
            </div>
        </div>
    </div>

    <?php if ($is_super_admin): ?>
        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <div class="card-header"><h6 class="mb-0"><i class="fas fa-shield-alt"></i> Roles &amp; Permissions</h6></div>
                <div class="card-body d-flex flex-column">
                    <p class="text-muted">Configure user roles and the permissions associated with each role.</p>
                    <div class="mt-auto">
                        <a href="<?php echo base_url('admin/roles'); ?>" class="btn btn-info btn-block">
                            <i class="fas fa-shield-alt"></i> Manage Roles
                        </a>
                        <a href="<?php echo base_url('admin/permissions'); ?>" class="btn btn-outline-info btn-block mt-2">
                            <i class="fas fa-lock"></i> View Permissions
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <div class="card-header"><h6 class="mb-0"><i class="fas fa-history"></i> Audit Logs</h6></div>
                <div class="card-body d-flex flex-column">
                    <p class="text-muted">Review a complete audit trail of all actions performed in the system.</p>
                    <div class="mt-auto">
                        <a href="<?php echo base_url('admin/audit-logs'); ?>" class="btn btn-secondary btn-block">
                            <i class="fas fa-list"></i> View Audit Logs
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <div class="card-header"><h6 class="mb-0"><i class="fas fa-sliders-h"></i> System Settings</h6></div>
                <div class="card-body d-flex flex-column">
                    <p class="text-muted">Configure system-wide settings, notification preferences, and platform options.</p>
                    <div class="mt-auto">
                        <a href="<?php echo base_url('admin/settings'); ?>" class="btn btn-warning btn-block">
                            <i class="fas fa-wrench"></i> System Settings
                        </a>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>
