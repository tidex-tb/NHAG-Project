<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-piggy-bank"></i> Savings Report</h2>
    <a href="<?php echo base_url('reports'); ?>" class="btn btn-outline-secondary"><i class="fas fa-arrow-left"></i> Back</a>
</div>

<div class="card">
    <div class="card-body">
        <form class="form-inline mb-3" method="GET" action="<?php echo base_url('reports/savings'); ?>">
            <label class="mr-2">Period</label>
            <select name="period" class="form-control mr-2">
                <?php foreach (array('daily', 'weekly', 'monthly', 'annual') as $period): ?>
                    <option value="<?php echo $period; ?>" <?php echo $this->input->get('period') === $period ? 'selected' : ''; ?>><?php echo ucfirst($period); ?></option>
                <?php endforeach; ?>
            </select>
            <input type="date" name="date" class="form-control mr-2" value="<?php echo html_escape($this->input->get('date') ?: date('Y-m-d')); ?>">
            <button class="btn btn-primary" type="submit">Apply</button>
        </form>

        <h4>Total Savings: <span class="text-success">N$ <?php echo number_format($total_savings, 2); ?></span></h4>
        <div class="table-responsive mt-3">
            <table class="table table-hover">
                <thead><tr><th>Date</th><th>Total</th></tr></thead>
                <tbody>
                    <?php foreach ($chart_data as $row): ?>
                        <tr><td><?php echo html_escape($row['date']); ?></td><td>N$ <?php echo number_format($row['total'], 2); ?></td></tr>
                    <?php endforeach; ?>
                    <?php if (empty($chart_data)): ?>
                        <tr><td colspan="2" class="text-center text-muted">No savings activity found for this period.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
