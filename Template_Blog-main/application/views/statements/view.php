<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-file-alt"></i> Savings Statement</h2>
    <div>
        <a href="<?php echo base_url('statements/download-pdf/' . $statement['id']); ?>" class="btn btn-outline-danger">
            <i class="fas fa-file-pdf"></i> Download PDF
        </a>
        <a href="<?php echo base_url('statements'); ?>" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left"></i> Back
        </a>
    </div>
</div>

<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <h6 class="mb-0">Statement #<?php echo $statement['statement_number']; ?></h6>
                <span class="badge badge-info"><?php echo $statement['statement_type']; ?></span>
            </div>
            <div class="card-body">

                <!-- Member & Period -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h6 class="text-muted">Member</h6>
                        <p class="mb-1"><strong><?php echo $member['first_name'] . ' ' . $member['last_name']; ?></strong></p>
                        <p class="mb-0 text-muted"><?php echo $member['member_number']; ?></p>
                    </div>
                    <div class="col-md-6 text-md-right">
                        <h6 class="text-muted">Statement Period</h6>
                        <p class="mb-1"><strong><?php echo date('M d, Y', strtotime($statement['period_start'])); ?></strong></p>
                        <p class="mb-0">to <strong><?php echo date('M d, Y', strtotime($statement['period_end'])); ?></strong></p>
                    </div>
                </div>

                <!-- Summary -->
                <div class="row mb-4 text-center">
                    <div class="col-md-4">
                        <div class="p-3 bg-light rounded">
                            <small class="text-muted">Opening Balance</small>
                            <h5>N$ <?php echo number_format($statement['opening_balance'], 2); ?></h5>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 bg-light rounded">
                            <small class="text-muted">Total Contributions</small>
                            <h5 class="text-success">N$ <?php echo number_format($statement['total_contributions'], 2); ?></h5>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 bg-primary text-white rounded">
                            <small>Closing Balance</small>
                            <h5>N$ <?php echo number_format($statement['closing_balance'], 2); ?></h5>
                        </div>
                    </div>
                </div>

                <!-- Transactions -->
                <h6 class="border-bottom pb-2 mb-3">Transactions</h6>
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr><th>Date</th><th>Type</th><th>Reference</th><th>Credit</th><th>Debit</th><th>Balance</th></tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($ledger)): ?>
                                <?php foreach ($ledger as $entry): ?>
                                    <tr>
                                        <td><?php echo date('M d, Y', strtotime($entry['transaction_date'])); ?></td>
                                        <td><?php echo $entry['transaction_type']; ?></td>
                                        <td><?php echo $entry['reference_number']; ?></td>
                                        <td class="text-success"><?php echo $entry['credit_amount'] > 0 ? 'N$ ' . number_format($entry['credit_amount'], 2) : '-'; ?></td>
                                        <td class="text-danger"><?php echo $entry['debit_amount'] > 0 ? 'N$ ' . number_format($entry['debit_amount'], 2) : '-'; ?></td>
                                        <td><strong>N$ <?php echo number_format($entry['balance_after'], 2); ?></strong></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="6" class="text-center text-muted py-3">No transactions in this period.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
