<?php $this->load->view('layouts/header', array('show_sidebar' => true, 'page_title' => 'Create Data Entry', 'current_page' => 'data_entries', 'is_admin' => true)); ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-clipboard-list"></i> Create Data Entry</h2>
    <a href="<?php echo base_url('data-entries'); ?>" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left"></i> Back
    </a>
</div>

<?php echo validation_errors('<div class="alert alert-danger">', '</div>'); ?>
<?php if ($this->session->flashdata('error')): ?>
    <div class="alert alert-danger"><?php echo html_escape($this->session->flashdata('error')); ?></div>
<?php endif; ?>

<form method="POST" action="<?php echo base_url('data-entries/create'); ?>">
    <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">

    <div class="card">
        <div class="card-header">Region and Area</div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4 form-group">
                    <label>Region *</label>
                    <input type="text" name="region_name" class="form-control" value="<?php echo set_value('region_name'); ?>" required>
                </div>
                <div class="col-md-4 form-group">
                    <label>Municipal Area</label>
                    <input type="text" name="municipal_area_name" class="form-control" value="<?php echo set_value('municipal_area_name'); ?>">
                </div>
                <div class="col-md-4 form-group">
                    <label>Area Type *</label>
                    <select name="area_type" class="form-control" required>
                        <?php foreach (array('Rural', 'Settlement', 'Town', 'Village', 'Municipal', 'Other') as $type): ?>
                            <option value="<?php echo $type; ?>" <?php echo set_select('area_type', $type); ?>><?php echo $type; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4 form-group">
                    <label>Area Name *</label>
                    <input type="text" name="area_name" class="form-control" value="<?php echo set_value('area_name'); ?>" required>
                </div>
                <div class="col-md-4 form-group">
                    <label>Number of Areas</label>
                    <input type="number" name="area_count" class="form-control" min="0" value="<?php echo set_value('area_count', 1); ?>">
                </div>
                <div class="col-md-4 form-group">
                    <label>Year Captured *</label>
                    <input type="number" name="capture_year" class="form-control" min="1900" max="2100" value="<?php echo set_value('capture_year', date('Y')); ?>" required>
                </div>
            </div>
            <div class="form-group">
                <label>Column 4 / Notes</label>
                <textarea name="entry_notes" class="form-control" rows="2"><?php echo set_value('entry_notes'); ?></textarea>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">Group and Members</div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3 form-group">
                    <label>Group Number *</label>
                    <input type="text" name="group_number" class="form-control" value="<?php echo set_value('group_number'); ?>" required>
                </div>
                <div class="col-md-3 form-group">
                    <label>Group Name</label>
                    <input type="text" name="group_name" class="form-control" value="<?php echo set_value('group_name'); ?>">
                </div>
                <div class="col-md-2 form-group">
                    <label>Members *</label>
                    <input type="number" name="number_of_members" class="form-control" min="0" value="<?php echo set_value('number_of_members', 0); ?>" required>
                </div>
                <div class="col-md-2 form-group">
                    <label>Males *</label>
                    <input type="number" name="number_of_males" class="form-control" min="0" value="<?php echo set_value('number_of_males', 0); ?>" required>
                </div>
                <div class="col-md-2 form-group">
                    <label>Females *</label>
                    <input type="number" name="number_of_females" class="form-control" min="0" value="<?php echo set_value('number_of_females', 0); ?>" required>
                </div>
            </div>
        </div>
    </div>

    <?php $money_fields = array(
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
        'total_buy_a_brick' => 'Buy A Brick'
    ); ?>

    <div class="card">
        <div class="card-header">Financial Fields</div>
        <div class="card-body">
            <div class="row">
                <?php foreach ($money_fields as $field => $label): ?>
                    <div class="col-md-3 form-group">
                        <label><?php echo $label; ?></label>
                        <input type="number" step="0.01" min="0" name="<?php echo $field; ?>" class="form-control" value="<?php echo set_value($field, 0); ?>">
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <div class="text-right mb-4">
        <button type="submit" class="btn btn-primary btn-lg">
            <i class="fas fa-save"></i> Save Data Entry
        </button>
    </div>
</form>

<?php $this->load->view('layouts/footer'); ?>
