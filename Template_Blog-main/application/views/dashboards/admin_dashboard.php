<?php $this->load->view('layouts/header', array('show_sidebar' => true, 'page_title' => 'Dashboard', 'current_page' => 'dashboard', 'is_admin' => true)); ?>

<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
	<h2><i class="fas fa-tachometer-alt"></i> Dashboard</h2>
	<div>
		<span class="badge badge-info">Administrator</span>
	</div>
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

<?php if ($this->session->flashdata('error')): ?>
	<div class="alert alert-danger alert-dismissible fade show" role="alert">
		<i class="fas fa-exclamation-circle"></i> <?php echo $this->session->flashdata('error'); ?>
		<button type="button" class="close" data-dismiss="alert">
			<span>&times;</span>
		</button>
	</div>
<?php endif; ?>

<?php $entry_totals = isset($data_entry_totals) ? $data_entry_totals : array(); ?>
<!-- Key Statistics -->
<div class="row mb-4">
	<div class="col-md-3">
		<div class="stat-card">
			<h5>Data Entries</h5>
			<h2><?php echo (int) ($entry_totals['entry_count'] ?? 0); ?></h2>
			<small class="text-muted"><i class="fas fa-clipboard-list" style="color: #3498db;"></i> Regional records captured</small>
		</div>
	</div>
	<div class="col-md-3">
		<div class="stat-card">
			<h5>Areas Counted</h5>
			<h2><?php echo (int) ($entry_totals['total_areas'] ?? 0); ?></h2>
			<small class="text-muted"><i class="fas fa-map-marker-alt" style="color: #1abc9c;"></i> Areas from data entries</small>
		</div>
	</div>
	<div class="col-md-3">
		<div class="stat-card">
			<h5>Entry Members</h5>
			<h2><?php echo (int) ($entry_totals['total_members'] ?? 0); ?></h2>
			<small class="text-muted"><i class="fas fa-users" style="color: #27ae60;"></i> Members recorded in entries</small>
		</div>
	</div>
	<div class="col-md-3">
		<div class="stat-card">
			<h5>Six Month Savings</h5>
			<h2>N$ <?php echo number_format($entry_totals['six_month_savings'] ?? 0, 2); ?></h2>
			<small class="text-muted"><i class="fas fa-money-bill-wave" style="color: #27ae60;"></i> Savings from entries</small>
		</div>
	</div>
</div>

<!-- Recent Activity Section -->
<div class="row mb-4">
	<div class="col-md-6">
		<div class="card">
			<div class="card-header">
				<h5 class="mb-0"><i class="fas fa-users"></i> Recent Member Registrations</h5>
			</div>
			<div class="card-body">
				<?php if (isset($recent_members) && !empty($recent_members)): ?>
					<div class="list-group">
						<?php foreach ($recent_members as $member): ?>
							<a href="<?php echo base_url('members/view/' . $member['id']); ?>" class="list-group-item list-group-item-action">
								<div class="d-flex justify-content-between">
									<strong><?php echo $member['name']; ?></strong>
									<small class="text-muted"><?php echo date('M d', strtotime($member['created_at'])); ?></small>
								</div>
								<small class="text-muted"><?php echo $member['member_number']; ?></small>
							</a>
						<?php endforeach; ?>
					</div>
				<?php else: ?>
					<p class="text-muted text-center py-3">No recent registrations</p>
				<?php endif; ?>
			</div>
		</div>
	</div>

	<div class="col-md-6">
		<div class="card">
			<div class="card-header">
				<h5 class="mb-0"><i class="fas fa-exclamation-triangle"></i> Pending Contributions</h5>
			</div>
			<div class="card-body">
				<?php if (isset($recent_contributions) && !empty($recent_contributions)): ?>
					<div class="list-group">
						<?php foreach ($recent_contributions as $contrib): ?>
							<a href="<?php echo base_url('contributions/view/' . $contrib['id']); ?>" class="list-group-item list-group-item-action">
								<div class="d-flex justify-content-between">
									<strong>N$ <?php echo number_format($contrib['amount'], 2); ?></strong>
									<span class="badge badge-warning"><?php echo ucfirst($contrib['status']); ?></span>
								</div>
								<small class="text-muted"><?php echo $contrib['reference_number']; ?> - <?php echo $contrib['member_name']; ?></small>
							</a>
						<?php endforeach; ?>
					</div>
				<?php else: ?>
					<p class="text-muted text-center py-3">No pending contributions</p>
				<?php endif; ?>
			</div>
		</div>
	</div>
</div>

<!-- Charts Section -->
<div class="row">
	<div class="col-md-6">
		<div class="card">
			<div class="card-header">
				<h5 class="mb-0"><i class="fas fa-chart-line"></i> Monthly Savings Trend</h5>
			</div>
			<div class="card-body">
				<canvas id="savingsChart" height="200"></canvas>
			</div>
		</div>
	</div>

	<div class="col-md-6">
		<div class="card">
			<div class="card-header">
				<h5 class="mb-0"><i class="fas fa-chart-pie"></i> Contribution Status</h5>
			</div>
			<div class="card-body">
				<canvas id="statusChart" height="200"></canvas>
			</div>
		</div>
	</div>
</div>

<!-- Quick Actions -->
<div class="row mt-4">
	<div class="col-12">
		<div class="card">
			<div class="card-header">
				<h5 class="mb-0"><i class="fas fa-lightning-bolt"></i> Quick Actions</h5>
			</div>
			<div class="card-body">
				<div class="btn-group btn-group-lg" role="group">
					<a href="<?php echo base_url('members/create'); ?>" class="btn btn-primary">
						<i class="fas fa-user-plus"></i> Register Member
					</a>
					<a href="<?php echo base_url('contributions'); ?>" class="btn btn-success">
						<i class="fas fa-handshake"></i> View Contributions
					</a>
					<a href="<?php echo base_url('reports'); ?>" class="btn btn-info">
						<i class="fas fa-chart-bar"></i> Generate Report
					</a>
					<a href="<?php echo base_url('admin'); ?>" class="btn btn-warning">
						<i class="fas fa-cog"></i> Settings
					</a>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- Chart Initialization Script -->
<script>
	document.addEventListener('DOMContentLoaded', function() {
		// Monthly Savings Chart
		const savingsCtx = document.getElementById('savingsChart').getContext('2d');
		new Chart(savingsCtx, {
			type: 'line',
			data: {
				labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
				datasets: [{
					label: 'Total Savings',
					data: <?php echo json_encode(isset($monthly_savings) ? $monthly_savings : [0, 0, 0, 0, 0, 0]); ?>,
					borderColor: '#3498db',
					backgroundColor: 'rgba(52, 152, 219, 0.1)',
					tension: 0.4,
					fill: true,
					pointRadius: 5,
					pointBackgroundColor: '#3498db'
				}]
			},
			options: {
				responsive: true,
				maintainAspectRatio: false,
				plugins: {
					legend: {
						display: true,
						position: 'top'
					}
				},
				scales: {
					y: {
						beginAtZero: true,
						ticks: {
							callback: function(value) {
								return 'N$ ' + value.toLocaleString();
							}
						}
					}
				}
			}
		});

		// Contribution Status Chart
		const statusCtx = document.getElementById('statusChart').getContext('2d');
		new Chart(statusCtx, {
			type: 'doughnut',
			data: {
				labels: ['Approved', 'Pending', 'Rejected'],
				datasets: [{
					data: <?php echo json_encode(isset($contribution_status) ? $contribution_status : [0, 0, 0]); ?>,
					backgroundColor: [
						'#27ae60',
						'#f39c12',
						'#e74c3c'
					]
				}]
			},
			options: {
				responsive: true,
				maintainAspectRatio: false,
				plugins: {
					legend: {
						display: true,
						position: 'bottom'
					}
				}
			}
		});
	});
</script>

<?php $this->load->view('layouts/footer'); ?>
