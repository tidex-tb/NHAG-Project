<?php $this->load->view('layouts/header', array('show_sidebar' => true, 'page_title' => 'Contributions', 'current_page' => 'contributions', 'is_admin' => false)); ?>

<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-handshake"></i> Savings Contributions</h2>
    <?php if ($this->user_model->has_permission($this->session->userdata('user_id'), 'submit_contribution')): ?>
        <a href="<?php echo base_url('contributions/submit'); ?>" class="btn btn-primary">
            <i class="fas fa-plus"></i> Submit Contribution
        </a>
    <?php endif; ?>
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

<!-- Filter Bar -->
<div class="card mb-4">
    <div class="card-body">
        <div class="row">
            <div class="col-md-8">
                <input type="text" class="form-control" id="searchInput" placeholder="Search by reference number or member name...">
            </div>
            <div class="col-md-4">
                <select class="form-control" id="statusFilter">
                    <option value="">-- All Status --</option>
                    <option value="pending">Pending</option>
                    <option value="approved">Approved</option>
                    <option value="rejected">Rejected</option>
                </select>
            </div>
        </div>
    </div>
</div>

<!-- Contributions Table -->
<div class="card">
    <div class="table-responsive">
        <table class="table table-hover" id="contributionsTable">
            <thead>
                <tr>
                    <th>Reference #</th>
                    <th>Member</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th>Submitted</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($contributions)): ?>
                    <?php foreach ($contributions as $contrib): ?>
                        <tr>
                            <td>
                                <strong><?php echo html_escape($contrib['reference_number']); ?></strong>
                            </td>
                            <td><?php echo html_escape($contrib['member_name'] ?? trim(($contrib['first_name'] ?? '') . ' ' . ($contrib['last_name'] ?? ''))); ?></td>
                            <td>N$ <?php echo number_format($contrib['amount'], 2); ?></td>
                            <td>
                                <?php
                                $status_class = 'badge-secondary';
                                if ($contrib['status'] === 'Approved') $status_class = 'badge-success';
                                if ($contrib['status'] === 'Pending') $status_class = 'badge-warning';
                                if ($contrib['status'] === 'Rejected') $status_class = 'badge-danger';
                                ?>
                                <span class="badge <?php echo $status_class; ?>">
                                    <?php echo html_escape($contrib['status']); ?>
                                </span>
                            </td>
                            <td><?php echo date('M d, Y', strtotime($contrib['created_at'])); ?></td>
                            <td>
                                <a href="<?php echo base_url('contributions/view/' . $contrib['id']); ?>" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-eye"></i> View
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">
                            No contributions found.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Initialize DataTables -->
<script>
    $(document).ready(function() {
        $('#contributionsTable').DataTable({
            pageLength: 25,
            order: [
                [4, 'desc']
            ] // Sort by submitted date descending
        });

        // Search functionality
        $('#searchInput').on('keyup', function() {
            $('#contributionsTable').DataTable().search($(this).val()).draw();
        });

        // Status filter
        $('#statusFilter').on('change', function() {
            if ($(this).val()) {
                $('#contributionsTable').DataTable().column(3).search('^' + $(this).val() + '$', true, false).draw();
            } else {
                $('#contributionsTable').DataTable().column(3).search('').draw();
            }
        });
    });
</script>

<?php $this->load->view('layouts/footer'); ?>
