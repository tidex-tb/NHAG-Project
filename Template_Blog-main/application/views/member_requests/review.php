<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-user-check"></i> Review Member Request</h2>
    <a href="<?php echo base_url('admin/member-requests'); ?>" class="btn btn-outline-secondary"><i class="fas fa-arrow-left"></i> Requests</a>
</div>

<?php if ($this->session->flashdata('error')): ?>
    <div class="alert alert-danger"><?php echo html_escape($this->session->flashdata('error')); ?></div>
<?php endif; ?>

<div class="row">
    <div class="col-md-7">
        <div class="card">
            <div class="card-header"><h6 class="mb-0">Submitted Information</h6></div>
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-sm-4">Name</dt><dd class="col-sm-8"><?php echo html_escape($request['first_name'] . ' ' . $request['last_name']); ?></dd>
                    <dt class="col-sm-4">Username</dt><dd class="col-sm-8"><?php echo html_escape($request['username']); ?></dd>
                    <dt class="col-sm-4">Email</dt><dd class="col-sm-8"><?php echo html_escape($request['email']); ?></dd>
                    <dt class="col-sm-4">Phone</dt><dd class="col-sm-8"><?php echo html_escape($request['phone_number']); ?></dd>
                    <dt class="col-sm-4">National ID</dt><dd class="col-sm-8"><?php echo html_escape($request['national_id']); ?></dd>
                    <dt class="col-sm-4">Gender</dt><dd class="col-sm-8"><?php echo html_escape($request['gender']); ?></dd>
                    <dt class="col-sm-4">Date of Birth</dt><dd class="col-sm-8"><?php echo $request['date_of_birth'] ? html_escape($request['date_of_birth']) : 'Not provided'; ?></dd>
                    <dt class="col-sm-4">Address</dt><dd class="col-sm-8"><?php echo nl2br(html_escape($request['physical_address'] ?: 'Not provided')); ?></dd>
                    <dt class="col-sm-4">Status</dt><dd class="col-sm-8"><span class="badge badge-<?php echo $request['status'] === 'Pending' ? 'warning' : ($request['status'] === 'Approved' ? 'success' : 'danger'); ?>"><?php echo html_escape($request['status']); ?></span></dd>
                </dl>
            </div>
        </div>
    </div>
    <div class="col-md-5">
        <div class="card">
            <div class="card-header"><h6 class="mb-0">Decision</h6></div>
            <div class="card-body">
                <?php if ($request['status'] === 'Pending'): ?>
                    <form method="POST" action="<?php echo base_url('admin/member-requests/approve/' . $request['id']); ?>" class="mb-3">
                        <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
                        <div class="form-group"><label>Admin Notes</label><textarea name="admin_notes" class="form-control" rows="3"></textarea></div>
                        <button class="btn btn-success btn-block" type="submit"><i class="fas fa-check"></i> Approve and Create Member</button>
                    </form>
                    <form method="POST" action="<?php echo base_url('admin/member-requests/reject/' . $request['id']); ?>">
                        <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
                        <div class="form-group"><label>Rejection Reason</label><textarea name="admin_notes" class="form-control" rows="3" required></textarea></div>
                        <button class="btn btn-outline-danger btn-block" type="submit"><i class="fas fa-times"></i> Reject Request</button>
                    </form>
                <?php else: ?>
                    <p class="text-muted">This request has already been reviewed.</p>
                    <?php if (!empty($request['admin_notes'])): ?><p><strong>Notes:</strong><br><?php echo nl2br(html_escape($request['admin_notes'])); ?></p><?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
