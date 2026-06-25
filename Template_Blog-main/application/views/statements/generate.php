<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-file-plus"></i> Generate Statement</h2>
    <a href="<?php echo base_url('statements'); ?>" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left"></i> Back
    </a>
</div>

<?php if (isset($errors) && !empty($errors)): ?>
    <div class="alert alert-danger">
        <?php foreach ($errors as $e): ?><div><?php echo $e; ?></div><?php endforeach; ?>
    </div>
<?php endif; ?>

<div class="row">
    <div class="col-md-6 mx-auto">
        <div class="card">
            <div class="card-header"><h6 class="mb-0">Custom Statement — <?php echo $member['first_name'] . ' ' . $member['last_name']; ?></h6></div>
            <div class="card-body p-4">
                <form method="POST" action="<?php echo base_url('statements/generate'); ?>">
                    <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
                    <div class="form-group">
                        <label>Start Date *</label>
                        <input type="date" class="form-control" name="start_date" value="<?php echo set_value('start_date', date('Y-m-01')); ?>" required>
                    </div>
                    <div class="form-group">
                        <label>End Date *</label>
                        <input type="date" class="form-control" name="end_date" value="<?php echo set_value('end_date', date('Y-m-t')); ?>" required>
                    </div>
                    <button type="submit" class="btn btn-primary btn-block">
                        <i class="fas fa-file-alt"></i> Generate Statement
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
