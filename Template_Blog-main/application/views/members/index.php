<?php $this->load->view('layouts/header', array('show_sidebar' => true, 'page_title' => 'Members', 'current_page' => 'members', 'is_admin' => true)); ?>

<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-users"></i> Member Management</h2>
    <a href="<?php echo base_url('members/create'); ?>" class="btn btn-primary">
        <i class="fas fa-user-plus"></i> Register New Member
    </a>
</div>

<!-- Alert Messages -->
<?php if ($this->session->flashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle"></i> <?php echo $this->session->flashdata('success'); ?>
        <button type="button" class="close" data-dismiss="alert">
            <span>&times;</span>
        </button>
    </div>
<?php endif; ?>

<!-- Search & Filter Bar -->
<div class="card mb-4">
    <div class="card-body">
        <div class="row">
            <div class="col-md-8">
                <div class="input-group">
                    <input type="text" class="form-control" id="searchInput" placeholder="Search members by name, member number, or email...">
                    <div class="input-group-append">
                        <button class="btn btn-outline-secondary" type="button">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <select class="form-control" id="statusFilter">
                    <option value="">-- All Status --</option>
                    <option value="active">Active</option>
                    <option value="suspended">Suspended</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>
        </div>
    </div>
</div>

<!-- Members Table -->
<div class="card">
    <div class="table-responsive">
        <table class="table table-hover" id="membersTable">
            <thead>
                <tr>
                    <th>Member Number</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Status</th>
                    <th>Balance</th>
                    <th>Joined</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($members)): ?>
                    <?php foreach ($members as $member): ?>
                        <tr>
                            <td>
                                <strong><?php echo $member['member_number']; ?></strong>
                            </td>
                            <td><?php echo html_escape($member['name']); ?></td>
                            <td><?php echo html_escape($member['email']); ?></td>
                            <td><?php echo html_escape($member['phone_number']); ?></td>
                            <td>
                                <?php
                                $status_class = 'badge-secondary';
                                $status_text = $member['status'];
                                if ($member['status'] === 'Active') {
                                    $status_class = 'badge-success';
                                } elseif ($member['status'] === 'Suspended') {
                                    $status_class = 'badge-warning';
                                }
                                ?>
                                <span class="badge <?php echo $status_class; ?>"><?php echo html_escape($status_text); ?></span>
                            </td>
                            <td>N$ <?php echo number_format($member['current_balance'], 2); ?></td>
                            <td><?php echo date('M d, Y', strtotime($member['created_at'])); ?></td>
                            <td>
                                <div class="btn-group btn-group-sm" role="group">
                                    <a href="<?php echo base_url('members/view/' . $member['id']); ?>" class="btn btn-outline-primary" title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="<?php echo base_url('members/edit/' . $member['id']); ?>" class="btn btn-outline-secondary" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <?php if ($member['status'] === 'Active'): ?>
                                        <a href="<?php echo base_url('members/suspend/' . $member['id']); ?>" class="btn btn-outline-warning" title="Suspend" onclick="return confirm('Suspend this member?')">
                                            <i class="fas fa-pause"></i>
                                        </a>
                                    <?php else: ?>
                                        <a href="<?php echo base_url('members/activate/' . $member['id']); ?>" class="btn btn-outline-success" title="Activate" onclick="return confirm('Activate this member?')">
                                            <i class="fas fa-play"></i>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">
                            No members found. <a href="<?php echo base_url('members/create'); ?>">Register a new member</a>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Pagination (if applicable) -->
<?php if (isset($pagination)): ?>
    <nav aria-label="Page navigation" class="mt-3">
        <ul class="pagination justify-content-center">
            <?php echo $pagination; ?>
        </ul>
    </nav>
<?php endif; ?>

<!-- Initialize DataTables -->
<script>
    $(document).ready(function() {
        $('#membersTable').DataTable({
            pageLength: 25,
            order: [
                [6, 'desc']
            ], // Sort by joined date descending
            columnDefs: [{
                targets: 7,
                orderable: false,
                searchable: false
            }]
        });

        // Search functionality
        $('#searchInput').on('keyup', function() {
            $('#membersTable').DataTable().search($(this).val()).draw();
        });

        // Status filter
        $('#statusFilter').on('change', function() {
            if ($(this).val()) {
                $('#membersTable').DataTable().column(4).search($(this).val()).draw();
            } else {
                $('#membersTable').DataTable().column(4).search('').draw();
            }
        });
    });
</script>

<?php $this->load->view('layouts/footer'); ?>
