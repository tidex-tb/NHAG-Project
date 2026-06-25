<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-user-check"></i> Member Profile Requests</h2>
</div>

<?php if ($this->session->flashdata('success')): ?>
    <div class="alert alert-success"><?php echo html_escape($this->session->flashdata('success')); ?></div>
<?php endif; ?>
<?php if ($this->session->flashdata('error')): ?>
    <div class="alert alert-danger"><?php echo html_escape($this->session->flashdata('error')); ?></div>
<?php endif; ?>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>Requested By</th>
                    <th>National ID</th>
                    <th>Phone</th>
                    <th>Status</th>
                    <th>Submitted</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($requests as $request): ?>
                    <tr>
                        <td><?php echo html_escape($request['first_name'] . ' ' . $request['last_name']); ?><br><small class="text-muted"><?php echo html_escape($request['username']); ?></small></td>
                        <td><?php echo html_escape($request['national_id']); ?></td>
                        <td><?php echo html_escape($request['phone_number']); ?></td>
                        <td><span class="badge badge-<?php echo $request['status'] === 'Pending' ? 'warning' : ($request['status'] === 'Approved' ? 'success' : 'danger'); ?>"><?php echo html_escape($request['status']); ?></span></td>
                        <td><?php echo date('M d, Y H:i', strtotime($request['created_at'])); ?></td>
                        <td><a class="btn btn-sm btn-outline-primary" href="<?php echo base_url('admin/member-requests/view/' . $request['id']); ?>"><i class="fas fa-eye"></i> Review</a></td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($requests)): ?>
                    <tr><td colspan="6" class="text-center text-muted py-4">No member profile requests yet.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
