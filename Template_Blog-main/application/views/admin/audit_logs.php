<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-history"></i> Audit Logs</h2>
    <small class="text-muted"><?php echo isset($total) ? number_format($total) : '0'; ?> total entries</small>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover table-sm mb-0" id="auditTable">
            <thead>
                <tr>
                    <th>Time</th>
                    <th>User</th>
                    <th>Action</th>
                    <th>Entity</th>
                    <th>Entity ID</th>
                    <th>IP Address</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($logs)): ?>
                    <?php foreach ($logs as $log): ?>
                        <tr>
                            <td><?php echo date('M d Y H:i:s', strtotime($log['logged_at'])); ?></td>
                            <td><?php echo $log['user_id'] ?? 'System'; ?></td>
                            <td>
                                <?php
                                $action_class = 'badge-secondary';
                                if ($log['action'] === 'INSERT') $action_class = 'badge-success';
                                if ($log['action'] === 'UPDATE') $action_class = 'badge-warning';
                                if ($log['action'] === 'DELETE') $action_class = 'badge-danger';
                                ?>
                                <span class="badge <?php echo $action_class; ?>"><?php echo $log['action']; ?></span>
                            </td>
                            <td><?php echo htmlspecialchars($log['entity_type']); ?></td>
                            <td><?php echo $log['entity_id']; ?></td>
                            <td><?php echo $log['ip_address']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="6" class="text-center text-muted py-4">No audit logs found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php if (isset($pagination)): ?>
    <nav class="mt-3"><ul class="pagination justify-content-center"><?php echo $pagination; ?></ul></nav>
<?php endif; ?>

<script>
    $(document).ready(function() {
        $('#auditTable').DataTable({ pageLength: 50, order: [[0, 'desc']], searching: true });
    });
</script>
