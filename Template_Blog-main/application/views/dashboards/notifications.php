<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-bell"></i> Notification Center</h2>
    <a href="<?php echo base_url('dashboard'); ?>" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left"></i> Dashboard
    </a>
</div>

<div class="card">
    <div class="card-body p-0">
        <?php if (!empty($notifications)): ?>
            <div class="list-group list-group-flush">
                <?php foreach ($notifications as $notification): ?>
                    <?php
                    $notification_url = null;
                    if (($notification['related_entity_type'] ?? '') === 'member_profile_request' && in_array((int) $this->session->userdata('role_id'), array(1, 2))) {
                        $notification_url = base_url('admin/member-requests/view/' . $notification['related_entity_id']);
                    }
                    ?>
                    <div class="list-group-item d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="mb-1 <?php echo empty($notification['is_read']) ? 'font-weight-bold' : ''; ?>">
                                <?php echo html_escape($notification['title']); ?>
                            </h6>
                            <p class="mb-1"><?php echo html_escape($notification['message']); ?></p>
                            <small class="text-muted">
                            <?php if ($notification_url): ?><a href="<?php echo $notification_url; ?>" class="btn btn-sm btn-outline-primary mt-2"><i class="fas fa-user-check"></i> Review Request</a><?php endif; ?>
                                <?php echo date('M d, Y H:i', strtotime($notification['created_at'])); ?>
                                <?php if (!empty($notification['notification_type'])): ?>
                                    · <?php echo html_escape($notification['notification_type']); ?>
                                <?php endif; ?>
                            </small>
                        </div>
                        <?php if (empty($notification['is_read'])): ?>
                            <button class="btn btn-sm btn-outline-primary" onclick="markNotificationAsRead(<?php echo (int) $notification['id']; ?>); this.closest('.list-group-item').classList.add('text-muted'); this.remove();">
                                Mark read
                            </button>
                        <?php else: ?>
                            <span class="badge badge-light border">Read</span>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="text-center text-muted py-5">
                <i class="fas fa-bell-slash fa-2x mb-3"></i>
                <p>No notifications yet.</p>
            </div>
        <?php endif; ?>
    </div>
</div>
