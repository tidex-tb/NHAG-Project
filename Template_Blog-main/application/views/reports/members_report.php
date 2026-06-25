<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-users"></i> Members Report</h2>
    <a href="<?php echo base_url('reports'); ?>" class="btn btn-outline-secondary"><i class="fas fa-arrow-left"></i> Back</a>
</div>

<div class="row">
    <div class="col-md-3"><div class="stat-card"><h5>Total</h5><h2><?php echo (int) $stats['total_members']; ?></h2></div></div>
    <div class="col-md-3"><div class="stat-card"><h5>Active</h5><h2><?php echo (int) $stats['active_members']; ?></h2></div></div>
    <div class="col-md-3"><div class="stat-card"><h5>Suspended</h5><h2><?php echo (int) $stats['suspended_members']; ?></h2></div></div>
    <div class="col-md-3"><div class="stat-card"><h5>Inactive</h5><h2><?php echo (int) $stats['inactive_members']; ?></h2></div></div>
</div>

<div class="card">
    <div class="card-header">Employment Status</div>
    <div class="table-responsive">
        <table class="table mb-0">
            <thead><tr><th>Status</th><th>Members</th></tr></thead>
            <tbody>
                <?php foreach ($employment_data as $row): ?>
                    <tr><td><?php echo html_escape($row['employment_status'] ?: 'Unknown'); ?></td><td><?php echo (int) $row['count']; ?></td></tr>
                <?php endforeach; ?>
                <?php if (empty($employment_data)): ?>
                    <tr><td colspan="2" class="text-center text-muted">No member data available.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="card">
    <div class="card-header">Export</div>
    <div class="card-body">
        <a href="<?php echo base_url('reports/export/csv?report_type=members'); ?>" class="btn btn-outline-secondary mr-2">CSV</a>
        <a href="<?php echo base_url('reports/export/excel?report_type=members'); ?>" class="btn btn-outline-success mr-2">Excel</a>
        <a href="<?php echo base_url('reports/export/pdf?report_type=members'); ?>" class="btn btn-outline-danger">Printable Report</a>
    </div>
</div>
