<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-id-card"></i> Member Profile</h2>
    <div>
        <a href="<?php echo base_url('members/edit/' . $member['id']); ?>" class="btn btn-outline-primary">
            <i class="fas fa-edit"></i> Edit
        </a>
        <a href="<?php echo base_url('members'); ?>" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left"></i> Back
        </a>
    </div>
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
    <!-- Member Info -->
    <div class="col-md-4">
        <div class="card">
            <div class="card-header"><h6 class="mb-0"><i class="fas fa-user"></i> Personal Details</h6></div>
            <div class="card-body">
                <p><strong>Member #:</strong> <?php echo html_escape($member['member_number']); ?></p>
                <p><strong>Name:</strong> <?php echo html_escape($member['first_name'] . ' ' . $member['last_name']); ?></p>
                <p><strong>Gender:</strong> <?php echo html_escape($member['gender']); ?></p>
                <p><strong>DOB:</strong> <?php echo $member['date_of_birth'] ? date('M d, Y', strtotime($member['date_of_birth'])) : 'N/A'; ?></p>
                <p><strong>National ID:</strong> <?php echo html_escape($member['national_id']); ?></p>
                <p><strong>Phone:</strong> <?php echo html_escape($member['phone_number'] ?? 'N/A'); ?></p>
                <p><strong>Email:</strong> <?php echo html_escape($member['email'] ?? 'N/A'); ?></p>
                <p><strong>Address:</strong> <?php echo nl2br(html_escape($member['physical_address'] ?? 'N/A')); ?></p>
                <p><strong>Status:</strong>
                    <?php if ($member['status'] === 'Active'): ?>
                        <span class="badge badge-success">Active</span>
                    <?php elseif ($member['status'] === 'Suspended'): ?>
                        <span class="badge badge-warning">Suspended</span>
                    <?php else: ?>
                        <span class="badge badge-secondary">Inactive</span>
                    <?php endif; ?>
                </p>
                <p><strong>Joined:</strong> <?php echo date('M d, Y', strtotime($member['registration_date'])); ?></p>
            </div>
        </div>

        <div class="card">
            <div class="card-header"><h6 class="mb-0"><i class="fas fa-wallet"></i> Savings Summary</h6></div>
            <div class="card-body text-center">
                <h3 class="text-success">N$ <?php echo number_format($member['current_balance'], 2); ?></h3>
                <small class="text-muted">Current Balance</small>
                <hr>
                <h5>N$ <?php echo number_format($member['total_savings'], 2); ?></h5>
                <small class="text-muted">Total Savings</small>
            </div>
        </div>

        <!-- Suspend / Activate -->
        <?php if ($member['status'] === 'Active'): ?>
            <form method="POST" action="<?php echo base_url('members/suspend/' . $member['id']); ?>" onsubmit="return confirm('Suspend this member?')">
                <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
                <div class="input-group mb-3">
                    <input type="text" name="reason" class="form-control" placeholder="Reason for suspension (optional)">
                    <div class="input-group-append">
                        <button class="btn btn-warning" type="submit"><i class="fas fa-pause"></i> Suspend</button>
                    </div>
                </div>
            </form>
        <?php else: ?>
            <form method="POST" action="<?php echo base_url('members/activate/' . $member['id']); ?>" onsubmit="return confirm('Activate this member?')">
                <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
                <button class="btn btn-success btn-block" type="submit"><i class="fas fa-play"></i> Activate Member</button>
            </form>
        <?php endif; ?>
    </div>

    <!-- Contributions -->
    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0"><i class="fas fa-handshake"></i> Contributions</h6>
                <a href="<?php echo base_url('contributions?member=' . $member['id']); ?>" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-sm mb-0">
                        <thead>
                            <tr>
                                <th>Reference</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($contributions)): ?>
                                <?php foreach ($contributions as $c): ?>
                                    <tr>
                                        <td><?php echo html_escape($c['reference_number']); ?></td>
                                        <td>N$ <?php echo number_format($c['amount'], 2); ?></td>
                                        <td>
                                            <?php
                                            $badge = $c['status'] === 'Approved' ? 'badge-success' : ($c['status'] === 'Rejected' ? 'badge-danger' : 'badge-warning');
                                            ?>
                                            <span class="badge <?php echo $badge; ?>"><?php echo html_escape($c['status']); ?></span>
                                        </td>
                                        <td><?php echo date('M d, Y', strtotime($c['transaction_date'])); ?></td>
                                        <td><a href="<?php echo base_url('contributions/view/' . $c['id']); ?>" class="btn btn-xs btn-outline-primary"><i class="fas fa-eye"></i></a></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="5" class="text-center text-muted py-3">No contributions yet.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Ledger -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0"><i class="fas fa-book"></i> Ledger</h6>
                <a href="<?php echo base_url('ledger/member/' . $member['id']); ?>" class="btn btn-sm btn-outline-primary">Full Ledger</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-sm mb-0">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Type</th>
                                <th>Credit</th>
                                <th>Debit</th>
                                <th>Balance</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($ledger)): ?>
                                <?php foreach ($ledger as $entry): ?>
                                    <tr>
                                        <td><?php echo date('M d, Y', strtotime($entry['transaction_date'])); ?></td>
                                        <td><?php echo html_escape($entry['transaction_type']); ?></td>
                                        <td class="text-success"><?php echo $entry['credit_amount'] > 0 ? 'N$ ' . number_format($entry['credit_amount'], 2) : '-'; ?></td>
                                        <td class="text-danger"><?php echo $entry['debit_amount'] > 0 ? 'N$ ' . number_format($entry['debit_amount'], 2) : '-'; ?></td>
                                        <td><strong>N$ <?php echo number_format($entry['balance_after'], 2); ?></strong></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="5" class="text-center text-muted py-3">No ledger entries.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
