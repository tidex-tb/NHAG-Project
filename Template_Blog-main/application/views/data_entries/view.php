<?php $this->load->view('layouts/header', array('show_sidebar' => true, 'page_title' => 'Data Entry Details', 'current_page' => 'data_entries', 'is_admin' => true)); ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-clipboard-list"></i> Data Entry Details</h2>
    <a href="<?php echo base_url('data-entries'); ?>" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left"></i> Back
    </a>
</div>

<?php if ($this->session->flashdata('success')): ?>
    <div class="alert alert-success"><?php echo html_escape($this->session->flashdata('success')); ?></div>
<?php endif; ?>

<div class="row">
    <div class="col-md-5">
        <div class="card">
            <div class="card-header">Region and Group</div>
            <div class="card-body">
                <p><strong>Region:</strong> <?php echo html_escape($entry['region_name']); ?></p>
                <p><strong>Municipal Area:</strong> <?php echo html_escape($entry['municipal_area_name'] ?: 'N/A'); ?></p>
                <p><strong>Area Type:</strong> <?php echo html_escape($entry['area_type']); ?></p>
                <p><strong>Area Name:</strong> <?php echo html_escape($entry['area_name']); ?></p>
                <p><strong>Number of Areas:</strong> <?php echo (int) $entry['area_count']; ?></p>
                <p><strong>Year Captured:</strong> <?php echo (int) $entry['capture_year']; ?></p>
                <p><strong>Group:</strong> <?php echo html_escape($entry['group_number']); ?> <?php echo $entry['group_name'] ? '(' . html_escape($entry['group_name']) . ')' : ''; ?></p>
                <p><strong>Members:</strong> <?php echo (int) $entry['number_of_members']; ?> total, <?php echo (int) $entry['number_of_males']; ?> male, <?php echo (int) $entry['number_of_females']; ?> female</p>
                <?php if ($entry['entry_notes']): ?>
                    <p><strong>Notes:</strong><br><?php echo nl2br(html_escape($entry['entry_notes'])); ?></p>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="col-md-7">
        <div class="card">
            <div class="card-header">Financial Summary</div>
            <div class="card-body p-0">
                <table class="table mb-0">
                    <tbody>
                        <?php
                        $money_fields = array(
                            'savings_brought_forward' => 'Savings Brought Forward',
                            'six_month_savings' => 'Savings at 6 Month Mark',
                            'total_land' => 'Land',
                            'total_member_withdrawals' => 'Member Withdrawals',
                            'total_loans_issued' => 'Loans Issued',
                            'total_loans_repaid' => 'Loans Repaid',
                            'total_interest_earned' => 'Interest Earned',
                            'total_advance_payments' => 'Advance Payments',
                            'total_short' => 'Short',
                            'total_cash' => 'Cash',
                            'total_bank_amount' => 'Bank Amount',
                            'total_bank_charges' => 'Bank Charges',
                            'total_business' => 'Business',
                            'total_daily_needs' => 'Daily Needs',
                            'total_ppe' => 'PPE',
                            'total_buy_a_brick' => 'Buy A Brick',
                            'total_expenditure' => 'Computed Expenditure'
                        );
                        ?>
                        <?php foreach ($money_fields as $field => $label): ?>
                            <tr>
                                <th><?php echo $label; ?></th>
                                <td class="text-right">N$ <?php echo number_format($entry[$field], 2); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php $this->load->view('layouts/footer'); ?>
