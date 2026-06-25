<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-shield-alt"></i> Role Management</h2>
</div>

<div class="row">
    <?php if (!empty($roles)): ?>
        <?php foreach ($roles as $role): ?>
            <div class="col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h6 class="mb-0"><?php echo htmlspecialchars($role['name']); ?></h6>
                        <span class="badge badge-primary">ID: <?php echo $role['id']; ?></span>
                    </div>
                    <div class="card-body">
                        <p class="text-muted mb-3"><?php echo isset($role['description']) ? htmlspecialchars($role['description']) : 'No description.'; ?></p>
                        <?php if (!empty($role['permissions'])): ?>
                            <h6 class="border-bottom pb-2 mb-2">Permissions</h6>
                            <div>
                                <?php foreach ($role['permissions'] as $perm): ?>
                                    <span class="badge badge-light border mr-1 mb-1"><?php echo htmlspecialchars($perm['name']); ?></span>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <p class="text-muted small mb-0">No permissions assigned.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="col-12">
            <div class="alert alert-info">No roles defined yet.</div>
        </div>
    <?php endif; ?>
</div>
