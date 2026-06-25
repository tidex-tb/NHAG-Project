<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-book"></i> Savings Ledger</h2>
</div>

<?php if ($this->session->flashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show">
        <i class="fas fa-check-circle"></i> <?php echo $this->session->flashdata('success'); ?>
        <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
    </div>
<?php endif; ?>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0" id="ledgerTable">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Member</th>
                        <th>Type</th>
                        <th>Reference</th>
                        <th>Credit</th>
                        <th>Debit</th>
                        <th>Balance After</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($entries)): ?>
                        <?php foreach ($entries as $entry): ?>
                            <tr>
                                <td><?php echo date('M d, Y', strtotime($entry['transaction_date'])); ?></td>
                                <td>
                                    <a href="<?php echo base_url('members/view/' . $entry['member_id']); ?>">
                                        <?php echo $entry['first_name'] . ' ' . $entry['last_name']; ?>
                                    </a>
                                    <small class="text-muted d-block"><?php echo $entry['member_number']; ?></small>
                                </td>
                                <td><span class="badge badge-secondary"><?php echo $entry['transaction_type']; ?></span></td>
                                <td><?php echo $entry['reference_number']; ?></td>
                                <td class="text-success"><?php echo $entry['credit_amount'] > 0 ? 'N$ ' . number_format($entry['credit_amount'], 2) : '-'; ?></td>
                                <td class="text-danger"><?php echo $entry['debit_amount'] > 0 ? 'N$ ' . number_format($entry['debit_amount'], 2) : '-'; ?></td>
                                <td><strong>N$ <?php echo number_format($entry['balance_after'], 2); ?></strong></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="7" class="text-center text-muted py-4">No ledger entries found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php if (isset($pagination)): ?>
    <nav class="mt-3"><ul class="pagination justify-content-center"><?php echo $pagination; ?></ul></nav>
<?php endif; ?>

<script>
    $(document).ready(function() {
        $('#ledgerTable').DataTable({ pageLength: 25, order: [[0, 'desc']] });
    });
</script>
