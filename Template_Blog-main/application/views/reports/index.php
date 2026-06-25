<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-chart-bar"></i> Reports & Analytics</h2>
</div>

<div class="row">
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-header"><i class="fas fa-piggy-bank"></i> Savings Report</div>
            <div class="card-body d-flex flex-column">
                <p class="text-muted">Review savings totals by period and export savings data.</p>
                <a href="<?php echo base_url('reports/savings'); ?>" class="btn btn-primary mt-auto">Open Savings Report</a>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-header"><i class="fas fa-handshake"></i> Contributions Report</div>
            <div class="card-body d-flex flex-column">
                <p class="text-muted">Track pending, approved, and rejected contribution activity.</p>
                <a href="<?php echo base_url('reports/contributions'); ?>" class="btn btn-primary mt-auto">Open Contributions Report</a>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-header"><i class="fas fa-users"></i> Members Report</div>
            <div class="card-body d-flex flex-column">
                <p class="text-muted">Analyze member counts, status, and employment groupings.</p>
                <a href="<?php echo base_url('reports/members'); ?>" class="btn btn-primary mt-auto">Open Members Report</a>
            </div>
        </div>
    </div>
</div>

<div class="card mt-4">
    <div class="card-header"><i class="fas fa-download"></i> Quick Exports</div>
    <div class="card-body">
        <a href="<?php echo base_url('reports/export/csv?report_type=members'); ?>" class="btn btn-outline-secondary mr-2">Members CSV</a>
        <a href="<?php echo base_url('reports/export/csv?report_type=contributions'); ?>" class="btn btn-outline-secondary mr-2">Contributions CSV</a>
        <a href="<?php echo base_url('reports/export/excel?report_type=members'); ?>" class="btn btn-outline-success mr-2">Members Excel</a>
        <a href="<?php echo base_url('reports/export/pdf?report_type=members'); ?>" class="btn btn-outline-danger">Printable Members Report</a>
    </div>
</div>
