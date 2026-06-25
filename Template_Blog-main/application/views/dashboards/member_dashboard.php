<?php $this->load->view('layouts/header', array('show_sidebar' => true, 'page_title' => 'Dashboard', 'current_page' => 'dashboard', 'is_admin' => false)); ?>

<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-home"></i> My Savings Dashboard</h2>
    <div>
        <span class="badge badge-success">Member</span>
    </div>
</div>

<!-- Alert Messages -->
<?php if ($this->session->flashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle"></i> <?php echo $this->session->flashdata('success'); ?>
        <button type="button" class="close" data-dismiss="alert">
            <span>&times;</span>
        </button>
    </div>
<?php endif; ?>

<?php if (!empty($profile_missing)): ?>
    <div class="alert alert-info" role="alert">
        <div class="d-flex justify-content-between align-items-center flex-wrap">
            <div class="mb-2 mb-md-0">
                <i class="fas fa-info-circle"></i>
                Your account is active, but it is not linked to a member profile yet.
                <?php echo !empty($pending_member_request) ? 'Your request is pending administrator approval.' : 'Submit your details for administrator approval to unlock contributions, ledger, and statements.'; ?>
            </div>
            <?php if (empty($pending_member_request)): ?>
                <a href="<?php echo base_url('member/request'); ?>" class="btn btn-primary btn-sm">
                    <i class="fas fa-paper-plane"></i> Request Member Profile
                </a>
            <?php else: ?>
                <span class="badge badge-warning p-2">Request Pending</span>
            <?php endif; ?>
        </div>
    </div>
<?php endif; ?>

<!-- Personal Statistics -->
<div class="row mb-4">
    <div class="col-md-4">
        <div class="stat-card">
            <h5>Total Savings</h5>
            <h2>N$ <?php echo isset($total_savings) ? number_format($total_savings, 2) : '0.00'; ?></h2>
            <small class="text-muted"><i class="fas fa-piggy-bank"></i> Your savings balance</small>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card">
            <h5>Pending Contributions</h5>
            <h2><?php echo isset($pending_count) ? $pending_count : '0'; ?></h2>
            <small class="text-muted"><i class="fas fa-hourglass-half"></i> Awaiting approval</small>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card">
            <h5>Approved This Month</h5>
            <h2>N$ <?php echo isset($monthly_approved) ? number_format($monthly_approved, 2) : '0.00'; ?></h2>
            <small class="text-muted"><i class="fas fa-check" style="color: #27ae60;"></i> This month</small>
        </div>
    </div>
</div>

<?php if (empty($profile_missing)): ?>
<!-- Action Cards -->
<div class="row mb-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title"><i class="fas fa-hand-holding-usd"></i> Submit New Contribution</h5>
                <p class="card-text">Add your savings contribution to your account. Upload your proof of payment for quick approval.</p>
                <a href="<?php echo base_url('contributions/submit'); ?>" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Submit Contribution
                </a>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title"><i class="fas fa-file-pdf"></i> Download Statement</h5>
                <p class="card-text">View and download your savings statement for a specific period. Perfect for record keeping.</p>
                <a href="<?php echo base_url('statements'); ?>" class="btn btn-info">
                    <i class="fas fa-download"></i> View Statements
                </a>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Recent Contributions -->
<div class="row mb-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-history"></i> Recent Contributions</h5>
            </div>
            <div class="card-body">
                <?php if (isset($recent_contributions) && !empty($recent_contributions)): ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Reference</th>
                                    <th>Amount</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recent_contributions as $contrib): ?>
                                    <tr>
                                        <td><?php echo $contrib['reference_number']; ?></td>
                                        <td>N$ <?php echo number_format($contrib['amount'], 2); ?></td>
                                        <td><?php echo date('M d, Y', strtotime($contrib['created_at'])); ?></td>
                                        <td>
                                            <?php
                                            $status_class = 'badge-secondary';
                                            $status = strtolower($contrib['status']);
                                            if ($status === 'approved') $status_class = 'badge-success';
                                            if ($status === 'pending') $status_class = 'badge-warning';
                                            if ($status === 'rejected') $status_class = 'badge-danger';
                                            ?>
                                            <span class="badge <?php echo $status_class; ?>">
                                                <?php echo ucfirst($contrib['status']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <a href="<?php echo base_url('contributions/view/' . $contrib['id']); ?>" class="btn btn-sm btn-outline-primary">
                                                View
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="text-muted text-center py-3">No contributions yet. <a href="<?php echo base_url('contributions/submit'); ?>">Submit your first contribution</a></p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Savings Growth Chart -->
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-chart-line"></i> Savings Growth</h5>
            </div>
            <div class="card-body">
                <canvas id="savingsGrowthChart" height="250"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Chart Initialization Script -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('savingsGrowthChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: <?php echo json_encode(isset($chart_labels) ? $chart_labels : ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun']); ?>,
                datasets: [{
                    label: 'Savings Balance',
                    data: <?php echo json_encode(isset($chart_data) ? $chart_data : [0, 0, 0, 0, 0, 0]); ?>,
                    borderColor: '#27ae60',
                    backgroundColor: 'rgba(39, 174, 96, 0.1)',
                    tension: 0.4,
                    fill: true,
                    pointRadius: 6,
                    pointBackgroundColor: '#27ae60',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointHoverRadius: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return 'N$ ' + value.toLocaleString();
                            }
                        }
                    }
                }
            }
        });
    });
</script>

<?php $this->load->view('layouts/footer'); ?>
