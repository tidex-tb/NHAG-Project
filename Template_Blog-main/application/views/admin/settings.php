<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-sliders-h"></i> System Settings</h2>
</div>

<?php if ($this->session->flashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show">
        <i class="fas fa-check-circle"></i> <?php echo $this->session->flashdata('success'); ?>
        <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
    </div>
<?php endif; ?>

<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="card">
            <div class="card-body p-4">
                <form method="POST" action="<?php echo base_url('admin/settings'); ?>">
                    <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
                    <?php if (!empty($settings)): ?>
                        <?php foreach ($settings as $setting): ?>
                            <div class="form-group">
                                <label class="text-capitalize"><?php echo htmlspecialchars(str_replace('_', ' ', $setting['key'])); ?></label>
                                <?php if (isset($setting['type']) && $setting['type'] === 'boolean'): ?>
                                    <select class="form-control" name="setting_<?php echo htmlspecialchars($setting['key']); ?>">
                                        <option value="1" <?php echo $setting['value'] == '1' ? 'selected' : ''; ?>>Enabled</option>
                                        <option value="0" <?php echo $setting['value'] == '0' ? 'selected' : ''; ?>>Disabled</option>
                                    </select>
                                <?php else: ?>
                                    <input type="text" class="form-control" name="setting_<?php echo htmlspecialchars($setting['key']); ?>" value="<?php echo htmlspecialchars($setting['value']); ?>">
                                <?php endif; ?>
                                <?php if (!empty($setting['description'])): ?>
                                    <small class="text-muted"><?php echo htmlspecialchars($setting['description']); ?></small>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                        <hr>
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-save"></i> Save Settings
                        </button>
                    <?php else: ?>
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-info-circle fa-2x mb-3"></i>
                            <p>No settings configured yet.</p>
                        </div>
                    <?php endif; ?>
                </form>
            </div>
        </div>
    </div>
</div>
