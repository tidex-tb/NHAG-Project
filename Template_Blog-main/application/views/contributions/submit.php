<?php $this->load->view('layouts/header', array('show_sidebar' => true, 'page_title' => 'Submit Contribution', 'current_page' => 'contributions')); ?>

<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-hand-holding-usd"></i> Submit Contribution</h2>
    <a href="<?php echo base_url('contributions'); ?>" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left"></i> Back
    </a>
</div>

<!-- Alert Messages -->
<?php if ($this->session->flashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle"></i> <?php echo $this->session->flashdata('error'); ?>
        <button type="button" class="close" data-dismiss="alert">
            <span>&times;</span>
        </button>
    </div>
<?php endif; ?>

<div class="row">
    <div class="col-md-8">
        <!-- Submission Form -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-plus-circle"></i> New Contribution</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="<?php echo base_url('contributions/submit'); ?>" enctype="multipart/form-data">
                    <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">

                    <!-- Contribution Amount -->
                    <div class="form-group">
                        <label for="amount">Contribution Amount (N$) *</label>
                        <input type="number" class="form-control <?php echo form_error('amount') ? 'is-invalid' : ''; ?>"
                            id="amount" name="amount" placeholder="0.00" step="0.01" min="0"
                            value="<?php echo set_value('amount'); ?>" required>
                        <small class="form-text text-muted">Enter the amount you are contributing</small>
                        <?php if (form_error('amount')): ?>
                            <div class="invalid-feedback d-block"><?php echo form_error('amount'); ?></div>
                        <?php endif; ?>
                    </div>

                    <!-- Payment Method -->
                    <div class="form-group">
                        <label for="payment_method">Payment Method *</label>
                        <select class="form-control <?php echo form_error('payment_method') ? 'is-invalid' : ''; ?>"
                            id="payment_method" name="payment_method" required>
                            <option value="">-- Select Payment Method --</option>
                            <option value="bank_transfer" <?php echo set_value('payment_method') === 'bank_transfer' ? 'selected' : ''; ?>>Bank Transfer</option>
                            <option value="cash_deposit" <?php echo set_value('payment_method') === 'cash_deposit' ? 'selected' : ''; ?>>Cash Deposit</option>
                            <option value="cheque" <?php echo set_value('payment_method') === 'cheque' ? 'selected' : ''; ?>>Cheque</option>
                            <option value="mobile_transfer" <?php echo set_value('payment_method') === 'mobile_transfer' ? 'selected' : ''; ?>>Mobile Transfer</option>
                            <option value="other" <?php echo set_value('payment_method') === 'other' ? 'selected' : ''; ?>>Other</option>
                        </select>
                        <?php if (form_error('payment_method')): ?>
                            <div class="invalid-feedback d-block"><?php echo form_error('payment_method'); ?></div>
                        <?php endif; ?>
                    </div>

                    <!-- Payment Date -->
                    <div class="form-group">
                        <label for="payment_date">Date of Payment *</label>
                        <input type="date" class="form-control <?php echo form_error('payment_date') ? 'is-invalid' : ''; ?>"
                            id="payment_date" name="payment_date"
                            value="<?php echo set_value('payment_date'); ?>" required>
                        <?php if (form_error('payment_date')): ?>
                            <div class="invalid-feedback d-block"><?php echo form_error('payment_date'); ?></div>
                        <?php endif; ?>
                    </div>

                    <!-- Description -->
                    <div class="form-group">
                        <label for="description">Description / Notes</label>
                        <textarea class="form-control" id="description" name="description" rows="3"
                            placeholder="Add any additional information about this contribution"><?php echo set_value('description'); ?></textarea>
                    </div>

                    <!-- Proof of Payment -->
                    <div class="form-group">
                        <label for="proof_file">Proof of Payment *</label>
                        <div class="custom-file">
                            <input type="file" class="custom-file-input <?php echo form_error('proof_file') ? 'is-invalid' : ''; ?>"
                                id="proof_file" name="proof_file" accept=".pdf,.jpg,.jpeg,.png,.gif" required>
                            <label class="custom-file-label" for="proof_file">Choose file...</label>
                        </div>
                        <small class="form-text text-muted d-block mt-2">
                            <i class="fas fa-info-circle"></i> Allowed: PDF, JPG, PNG, GIF (Max 5MB)
                        </small>
                        <?php if (form_error('proof_file')): ?>
                            <div class="invalid-feedback d-block"><?php echo form_error('proof_file'); ?></div>
                        <?php endif; ?>
                    </div>

                    <!-- Form Actions -->
                    <div class="form-group pt-3">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-paper-plane"></i> Submit Contribution
                        </button>
                        <a href="<?php echo base_url('contributions'); ?>" class="btn btn-outline-secondary btn-lg">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Information Sidebar -->
    <div class="col-md-4">
        <!-- Account Information -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-user-check"></i> Your Account</h5>
            </div>
            <div class="card-body">
                <p class="mb-1">
                    <strong>Member Number:</strong><br>
                    <span class="text-muted"><?php echo isset($member_number) ? $member_number : 'N/A'; ?></span>
                </p>
                <p class="mb-1">
                    <strong>Current Balance:</strong><br>
                    <span class="text-success">N$ <?php echo isset($current_balance) ? number_format($current_balance, 2) : '0.00'; ?></span>
                </p>
                <p class="mb-0">
                    <strong>Status:</strong><br>
                    <span class="badge badge-success">Active</span>
                </p>
            </div>
        </div>

        <!-- Instructions -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-book"></i> Instructions</h5>
            </div>
            <div class="card-body">
                <h6>Steps to submit:</h6>
                <ol style="font-size: 0.9rem;">
                    <li>Enter contribution amount</li>
                    <li>Select payment method</li>
                    <li>Enter payment date</li>
                    <li>Upload proof of payment (receipt/transaction slip)</li>
                    <li>Add optional notes</li>
                    <li>Click Submit</li>
                </ol>

                <hr>

                <h6>After submission:</h6>
                <ul style="font-size: 0.9rem;">
                    <li>Your contribution will be reviewed by Finance Officer</li>
                    <li>You'll receive notification when approved/rejected</li>
                    <li>Approved amounts will be added to your balance</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- File upload event handler -->
<script>
    document.getElementById('proof_file').addEventListener('change', function() {
        // Update label with selected filename
        const fileName = this.files[0].name;
        const label = document.querySelector('.custom-file-label');
        label.textContent = fileName;

        // Validate file
        const validation = validateFileUpload(this, 5, ['pdf', 'jpg', 'jpeg', 'png', 'gif']);
        if (!validation.valid) {
            showToast(validation.message, 'warning');
            this.value = '';
            label.textContent = 'Choose file...';
        }
    });
</script>

<?php $this->load->view('layouts/footer'); ?>
