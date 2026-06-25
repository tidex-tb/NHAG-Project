<?php $this->load->view('layouts/header', array('show_sidebar' => true, 'page_title' => 'Data Entries', 'current_page' => 'data_entries', 'is_admin' => true)); ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-clipboard-list"></i> Regional Group Data Entries</h2>
    <a href="<?php echo base_url('data-entries/create'); ?>" class="btn btn-primary">
        <i class="fas fa-plus"></i> New Entry
    </a>
</div>

<?php if ($this->session->flashdata('success')): ?>
    <div class="alert alert-success"><?php echo html_escape($this->session->flashdata('success')); ?></div>
<?php endif; ?>

<div class="row mb-4">
    <div class="col-md-3">
        <div class="stat-card">
            <h5>Entries</h5>
            <h2><?php echo (int) ($totals['entry_count'] ?? 0); ?></h2>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <h5>Areas Counted</h5>
            <h2><?php echo (int) ($totals['total_areas'] ?? 0); ?></h2>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <h5>Members</h5>
            <h2><?php echo (int) ($totals['total_members'] ?? 0); ?></h2>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <h5>Six Month Savings</h5>
            <h2>N$ <?php echo number_format($totals['six_month_savings'] ?? 0, 2); ?></h2>
        </div>
    </div>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0" id="dataEntriesTable">
            <thead>
                <tr>
                    <th>Region</th>
                    <th>Area Type</th>
                    <th>Area</th>
                    <th>Year</th>
                    <th>Group</th>
                    <th>Members</th>
                    <th>6 Month Savings</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($entries as $entry): ?>
                    <tr>
                        <td><?php echo html_escape($entry['region_name']); ?></td>
                        <td><?php echo html_escape($entry['area_type']); ?></td>
                        <td><?php echo html_escape($entry['area_name']); ?></td>
                        <td><?php echo (int) $entry['capture_year']; ?></td>
                        <td><?php echo html_escape($entry['group_number']); ?></td>
                        <td><?php echo (int) $entry['number_of_members']; ?></td>
                        <td>N$ <?php echo number_format($entry['six_month_savings'], 2); ?></td>
                        <td>
                            <a href="<?php echo base_url('data-entries/view/' . $entry['id']); ?>" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($entries)): ?>
                    <tr><td colspan="8" class="text-center text-muted py-4">No data entries yet.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('#dataEntriesTable').DataTable({ pageLength: 25, order: [[3, 'desc']] });
    });
</script>

<?php $this->load->view('layouts/footer'); ?>
