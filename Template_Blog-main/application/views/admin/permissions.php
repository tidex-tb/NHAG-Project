<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-lock"></i> Permissions</h2>
</div>

<?php if (!empty($permissions)): ?>
    <?php foreach ($permissions as $module => $perms): ?>
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="mb-0 text-capitalize"><i class="fas fa-folder"></i> <?php echo htmlspecialchars($module); ?></h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-sm mb-0">
                        <thead>
                            <tr><th style="width:40%">Permission</th><th>Name (key)</th><th>Action</th></tr>
                        </thead>
                        <tbody>
                            <?php foreach ($perms as $perm): ?>
                                <tr>
                                    <td><strong><?php echo htmlspecialchars($perm['name']); ?></strong></td>
                                    <td><code><?php echo htmlspecialchars($perm['name']); ?></code></td>
                                    <td><?php echo isset($perm['action']) ? htmlspecialchars($perm['action']) : '-'; ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
<?php else: ?>
    <div class="alert alert-info">No permissions defined yet.</div>
<?php endif; ?>
