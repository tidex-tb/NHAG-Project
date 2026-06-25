<?php $this->load->view('layouts/header', array('show_sidebar' => true, 'page_title' => 'Finance Dashboard', 'current_page' => 'dashboard', 'is_admin' => false)); ?>

<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-chart-line"></i> Finance Dashboard</h2>
    <span class="badge badge-info">Finance Officer</span>
</div>

<?php if ($this->session->flashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show">
        <i class="fas fa-check-circle"></i> <?php echo $this->session->flashdata('success'); ?>
        <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
    </div>
<?php endif; ?>

<?php $entry_totals = isset($data_entry_totals) ? $data_entry_totals : array(); ?>
<!-- Stats -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="stat-card">
            <h5>Data Entries</h5>
            <h2><?php echo (int) ($entry_totals['entry_count'] ?? 0); ?></h2>
            <small class="text-muted"><i class="fas fa-clipboard-list text-info"></i> Regional records captured</small>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <h5>Entry Members</h5>
            <h2><?php echo (int) ($entry_totals['total_members'] ?? 0); ?></h2>
            <small class="text-muted"><i class="fas fa-users text-success"></i> Members recorded</small>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <h5>Six Month Savings</h5>
            <h2>N$ <?php echo number_format($entry_totals['six_month_savings'] ?? 0, 2); ?></h2>
            <small class="text-muted"><i class="fas fa-piggy-bank text-success"></i> From data entries</small>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <h5>Pending Contributions</h5>
            <h2><?php echo isset($contribution_stats['pending']) ? $contribution_stats['pending'] : '0'; ?></h2>
            <small class="text-muted"><i class="fas fa-hourglass-half text-warning"></i> Awaiting review</small>
        </div>
    </div>
</div>

<div class="row">
    <!-- Pending Contributions -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0"><i class="fas fa-clock"></i> Pending Contributions</h6>
                <a href="<?php echo base_url('contributions?status=Pending'); ?>" class="btn btn-sm btn-outline-warning">View All</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-sm mb-0">
                        <thead>
                            <tr><th>Reference</th><th>Member</th><th>Amount</th><th>Date</th><th></th></tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($pending_contributions)): ?>
                                <?php foreach ($pending_contributions as $c): ?>
                                    <tr>
                                        <td><?php echo $c['reference_number']; ?></td>
                                        <td><?php echo $c['first_name'] . ' ' . $c['last_name']; ?></td>
                                        <td>N$ <?php echo number_format($c['amount'], 2); ?></td>
                                        <td><?php echo date('M d', strtotime($c['transaction_date'])); ?></td>
                                        <td><a href="<?php echo base_url('contributions/view/' . $c['id']); ?>" class="btn btn-xs btn-outline-primary"><i class="fas fa-eye"></i></a></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="5" class="text-center text-muted py-3">No pending contributions.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Approvals -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0"><i class="fas fa-check-double"></i> Recent Approvals</h6>
                <a href="<?php echo base_url('contributions?status=Approved'); ?>" class="btn btn-sm btn-outline-success">View All</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-sm mb-0">
                        <thead>
                            <tr><th>Reference</th><th>Member</th><th>Amount</th><th>Approved</th></tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($recent_approvals)): ?>
                                <?php foreach ($recent_approvals as $c): ?>
                                    <tr>
                                        <td><?php echo $c['reference_number']; ?></td>
                                        <td><?php echo $c['first_name'] . ' ' . $c['last_name']; ?></td>
                                        <td>N$ <?php echo number_format($c['amount'], 2); ?></td>
                                        <td><?php echo date('M d', strtotime($c['approval_date'])); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="4" class="text-center text-muted py-3">No recent approvals.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="card mt-4">
    <div class="card-header"><h6 class="mb-0"><i class="fas fa-bolt"></i> Quick Actions</h6></div>
    <div class="card-body">
        <a href="<?php echo base_url('contributions?status=Pending'); ?>" class="btn btn-warning mr-2">
            <i class="fas fa-clock"></i> Review Pending
        </a>
        <a href="<?php echo base_url('ledger'); ?>" class="btn btn-info mr-2">
            <i class="fas fa-book"></i> View Ledger
        </a>
        <a href="<?php echo base_url('reports'); ?>" class="btn btn-secondary">
            <i class="fas fa-chart-bar"></i> Reports
        </a>
    </div>
</div>

<?php $this->load->view('layouts/footer'); ?>
