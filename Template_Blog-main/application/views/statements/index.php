<?php $this->load->view('layouts/header', array('show_sidebar' => true, 'page_title' => 'Statements', 'current_page' => 'statements')); ?>

<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-file-alt"></i> My Statements</h2>
    <?php if (empty($is_staff)): ?>
        <a href="<?php echo base_url('statements/generate'); ?>" class="btn btn-primary">
            <i class="fas fa-plus"></i> Generate New Statement
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

<!-- Statements Table -->
<div class="card">
    <div class="table-responsive">
        <table class="table table-hover" id="statementsTable">
            <thead>
                <tr>
                    <th>Statement #</th>
                    <?php if (!empty($is_staff)): ?><th>Member</th><?php endif; ?>
                    <th>Period</th>
                    <th>Opening Balance</th>
                    <th>Contributions</th>
                    <th>Closing Balance</th>
                    <th>Generated</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($statements)): ?>
                    <?php foreach ($statements as $stmt): ?>
                        <tr>
                            <td>
                                <strong><?php echo $stmt['statement_number']; ?></strong>
                            </td>
                            <?php if (!empty($is_staff)): ?>
                                <td>
                                    <?php echo html_escape(trim(($stmt['first_name'] ?? '') . ' ' . ($stmt['last_name'] ?? '')) ?: 'Unknown'); ?>
                                    <small class="text-muted d-block"><?php echo html_escape($stmt['member_number'] ?? ''); ?></small>
                                </td>
                            <?php endif; ?>
                            <td>
                                <?php echo date('M d, Y', strtotime($stmt['period_start'])); ?> -
                                <?php echo date('M d, Y', strtotime($stmt['period_end'])); ?>
                            </td>
                            <td>N$ <?php echo number_format($stmt['opening_balance'], 2); ?></td>
                            <td>N$ <?php echo number_format($stmt['total_contributions'], 2); ?></td>
                            <td class="text-success font-weight-bold">N$ <?php echo number_format($stmt['closing_balance'], 2); ?></td>
                            <td><?php echo date('M d, Y', strtotime($stmt['generated_at'])); ?></td>
                            <td>
                                <div class="btn-group btn-group-sm" role="group">
                                    <a href="<?php echo base_url('statements/view/' . $stmt['id']); ?>" class="btn btn-outline-primary" title="View">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                    <a href="<?php echo base_url('statements/download-pdf/' . $stmt['id']); ?>" class="btn btn-outline-success" title="Download">
                                        <i class="fas fa-download"></i> Download
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="<?php echo !empty($is_staff) ? 8 : 7; ?>" class="text-center text-muted py-4">
                            No statements generated yet.
                            <?php if (empty($is_staff)): ?>
                                <a href="<?php echo base_url('statements/generate'); ?>">Generate your first statement</a>
                            <?php endif; ?>
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
        $('#statementsTable').DataTable({
            pageLength: 25,
            order: [
                [5, 'desc']
            ] // Sort by generated date descending
        });
    });
</script>

<?php $this->load->view('layouts/footer'); ?>
