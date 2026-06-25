<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-book"></i> Member Ledger — <?php echo html_escape($member['first_name'] . ' ' . $member['last_name']); ?></h2>
    <a href="<?php echo base_url('members/view/' . $member['id']); ?>" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left"></i> Back to Profile
    </a>
</div>

<!-- Balance Summary -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="stat-card">
            <h5>Current Balance</h5>
            <h2 class="text-success">N$ <?php echo number_format(isset($balance) ? $balance : 0, 2); ?></h2>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <h5>Total Entries</h5>
            <h2><?php echo isset($total) ? $total : 0; ?></h2>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0" id="memberLedgerTable">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Type</th>
                        <th>Reference</th>
                        <th>Description</th>
                        <th>Credit</th>
                        <th>Debit</th>
                        <th>Balance</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($entries)): ?>
                        <?php foreach ($entries as $entry): ?>
                            <tr>
                                <td><?php echo date('M d, Y', strtotime($entry['transaction_date'])); ?></td>
                                <td><span class="badge badge-secondary"><?php echo html_escape($entry['transaction_type']); ?></span></td>
                                <td><?php echo html_escape($entry['reference_number']); ?></td>
                                <td><?php echo html_escape($entry['description'] ?? '-'); ?></td>
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
        $('#memberLedgerTable').DataTable({ pageLength: 25, order: [[0, 'desc']], paging: false });
    });
</script>
