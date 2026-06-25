<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-handshake"></i> Contributions Report</h2>
    <a href="<?php echo base_url('reports'); ?>" class="btn btn-outline-secondary"><i class="fas fa-arrow-left"></i> Back</a>
</div>

<div class="row">
    <?php foreach (array('Pending' => 'warning', 'Approved' => 'success', 'Rejected' => 'danger') as $status => $color): ?>
        <div class="col-md-4">
            <div class="stat-card">
                <h5><?php echo $status; ?></h5>
                <h2 class="text-<?php echo $color; ?>"><?php echo (int) ($stats[$status]['count'] ?? 0); ?></h2>
                <small>N$ <?php echo number_format($stats[$status]['total'] ?? 0, 2); ?></small>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<div class="card">
    <div class="card-header">Export</div>
    <div class="card-body">
        <a href="<?php echo base_url('reports/export/csv?report_type=contributions'); ?>" class="btn btn-outline-secondary mr-2">CSV</a>
        <a href="<?php echo base_url('reports/export/excel?report_type=contributions'); ?>" class="btn btn-outline-success mr-2">Excel</a>
        <a href="<?php echo base_url('reports/export/pdf?report_type=contributions'); ?>" class="btn btn-outline-danger">Printable Report</a>
    </div>
</div>
