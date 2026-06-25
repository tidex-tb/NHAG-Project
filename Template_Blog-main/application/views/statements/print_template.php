<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Statement <?php echo html_escape($statement['statement_number']); ?></title>
    <style>
        body { font-family: Arial, sans-serif; color: #222; margin: 32px; }
        h1 { margin-bottom: 4px; }
        .muted { color: #666; }
        .summary { display: table; width: 100%; margin: 24px 0; }
        .summary div { display: table-cell; padding: 12px; border: 1px solid #ddd; }
        table { width: 100%; border-collapse: collapse; margin-top: 16px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background: #f1f3f5; }
        .right { text-align: right; }
    </style>
</head>
<body>
    <h1>NHAG Savings Statement</h1>
    <p class="muted">Statement #<?php echo html_escape($statement['statement_number']); ?></p>

    <p>
        <strong>Member:</strong> <?php echo html_escape($member['first_name'] . ' ' . $member['last_name']); ?><br>
        <strong>Member Number:</strong> <?php echo html_escape($member['member_number']); ?><br>
        <strong>Period:</strong> <?php echo html_escape($statement['period_start']); ?> to <?php echo html_escape($statement['period_end']); ?>
    </p>

    <div class="summary">
        <div><strong>Opening Balance</strong><br>N$ <?php echo number_format($statement['opening_balance'], 2); ?></div>
        <div><strong>Contributions</strong><br>N$ <?php echo number_format($statement['total_contributions'], 2); ?></div>
        <div><strong>Closing Balance</strong><br>N$ <?php echo number_format($statement['closing_balance'], 2); ?></div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Type</th>
                <th>Reference</th>
                <th class="right">Credit</th>
                <th class="right">Debit</th>
                <th class="right">Balance</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($ledger)): ?>
                <?php foreach ($ledger as $entry): ?>
                    <tr>
                        <td><?php echo html_escape($entry['transaction_date']); ?></td>
                        <td><?php echo html_escape($entry['transaction_type']); ?></td>
                        <td><?php echo html_escape($entry['reference_number']); ?></td>
                        <td class="right">N$ <?php echo number_format($entry['credit_amount'], 2); ?></td>
                        <td class="right">N$ <?php echo number_format($entry['debit_amount'], 2); ?></td>
                        <td class="right">N$ <?php echo number_format($entry['balance_after'], 2); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="6">No transactions in this period.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>
