<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-handshake"></i> Contribution Details</h2>
    <a href="<?php echo base_url('contributions'); ?>" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left"></i> Back
    </a>
</div>

<?php if ($this->session->flashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show">
        <i class="fas fa-check-circle"></i> <?php echo $this->session->flashdata('success'); ?>
        <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
    </div>
<?php endif; ?>
<?php if ($this->session->flashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show">
        <i class="fas fa-exclamation-circle"></i> <?php echo $this->session->flashdata('error'); ?>
        <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
    </div>
<?php endif; ?>

<div class="row">
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-header"><h6 class="mb-0"><i class="fas fa-info-circle"></i> Contribution Info</h6></div>
            <div class="card-body">
                <table class="table table-borderless mb-0">
                    <tr><th style="width:40%">Reference #</th><td><strong><?php echo html_escape($contribution['reference_number']); ?></strong></td></tr>
                    <tr><th>Member</th><td><?php echo html_escape($contribution['first_name'] . ' ' . $contribution['last_name']); ?> (<?php echo html_escape($contribution['member_number']); ?>)</td></tr>
                    <tr><th>Amount</th><td><strong class="text-success">N$ <?php echo number_format($contribution['amount'], 2); ?></strong></td></tr>
                    <tr><th>Payment Method</th><td><?php echo html_escape($contribution['payment_method']); ?></td></tr>
                    <tr><th>Transaction Date</th><td><?php echo date('M d, Y', strtotime($contribution['transaction_date'])); ?></td></tr>
                    <tr><th>Submitted</th><td><?php echo date('M d, Y H:i', strtotime($contribution['created_at'])); ?></td></tr>
                    <tr><th>Status</th><td>
                        <?php
                        $badge = $contribution['status'] === 'Approved' ? 'badge-success' : ($contribution['status'] === 'Rejected' ? 'badge-danger' : 'badge-warning');
                        ?>
                        <span class="badge <?php echo $badge; ?> badge-pill"><?php echo html_escape($contribution['status']); ?></span>
                    </td></tr>
                    <?php if ($contribution['status'] === 'Approved'): ?>
                        <tr><th>Approved On</th><td><?php echo date('M d, Y', strtotime($contribution['approval_date'])); ?></td></tr>
                    <?php endif; ?>
                    <?php if ($contribution['status'] === 'Rejected' && $contribution['rejection_reason']): ?>
                        <tr><th>Rejection Reason</th><td class="text-danger"><?php echo nl2br(html_escape($contribution['rejection_reason'])); ?></td></tr>
                    <?php endif; ?>
                    <?php if ($contribution['notes']): ?>
                        <tr><th>Notes</th><td><?php echo nl2br(htmlspecialchars($contribution['notes'])); ?></td></tr>
                    <?php endif; ?>
                </table>
            </div>
        </div>

        <!-- Documents -->
        <div class="card">
            <div class="card-header"><h6 class="mb-0"><i class="fas fa-paperclip"></i> Attached Documents</h6></div>
            <div class="card-body">
                <?php if (!empty($documents)): ?>
                    <ul class="list-group list-group-flush">
                        <?php foreach ($documents as $doc): ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <i class="fas fa-file"></i>
                                    <?php echo html_escape($doc['file_name']); ?>
                                    <small class="text-muted ml-2">(<?php echo round($doc['file_size'] / 1024, 1); ?> KB)</small>
                                </div>
                                <a href="<?php echo base_url('contributions/download-document/' . $doc['id']); ?>" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-download"></i>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <p class="text-muted text-center py-2 mb-0">No documents attached.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Approve / Reject Actions -->
    <?php $can_approve = $this->user_model->has_permission($this->session->userdata('user_id'), 'approve_contribution'); ?>
    <?php $can_reject = $this->user_model->has_permission($this->session->userdata('user_id'), 'reject_contribution'); ?>
    <?php if ($contribution['status'] === 'Pending' && ($can_approve || $can_reject)): ?>
        <div class="col-md-6">
            <?php if ($can_approve): ?>
                <div class="card mb-3 border-success">
                    <div class="card-header bg-success text-white"><h6 class="mb-0"><i class="fas fa-check"></i> Approve Contribution</h6></div>
                    <div class="card-body">
                        <form method="POST" action="<?php echo base_url('contributions/approve/' . $contribution['id']); ?>" onsubmit="return confirm('Approve this contribution?')">
                            <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
                            <div class="form-group">
                                <label>Approval Notes (optional)</label>
                                <textarea class="form-control" name="approval_notes" rows="3" placeholder="Any notes about this approval..."></textarea>
                            </div>
                            <button type="submit" class="btn btn-success btn-block"><i class="fas fa-check-circle"></i> Approve</button>
                        </form>
                    </div>
                </div>
            <?php endif; ?>
            <?php if ($can_reject): ?>
                <div class="card border-danger">
                    <div class="card-header bg-danger text-white"><h6 class="mb-0"><i class="fas fa-times"></i> Reject Contribution</h6></div>
                    <div class="card-body">
                        <form method="POST" action="<?php echo base_url('contributions/reject/' . $contribution['id']); ?>" onsubmit="return confirm('Reject this contribution?')">
                            <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
                            <div class="form-group">
                                <label>Rejection Reason *</label>
                                <textarea class="form-control" name="rejection_reason" rows="3" placeholder="Explain why this contribution is being rejected..." required></textarea>
                            </div>
                            <button type="submit" class="btn btn-danger btn-block"><i class="fas fa-times-circle"></i> Reject</button>
                        </form>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>
