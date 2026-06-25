<?php echo validation_errors('<div class="alert alert-danger">', '</div>'); ?>

<?php echo form_open('users/register'); ?>
<div class="container">
	<div class="row">
		<div class="col-md-6 offset-md-3">
			<h2 class="text-center text-dark mt-5"><?= $title; ?></h2>

			<div class="card md-4 bg-dark">

				<div class="card-body cardbody-dark p-lg-5">

					<div class="col-sm-6 offset-sm-3 mb-3 text-light text-center">
						<img src="/Template_Blog/assets/images/LOGO_1" class="img-fluid profile-image-pic img-thumbnail rounded-circle my-3" width="200px" alt="profile">
					</div>
					<div class="form-group col-sm-6 offset-sm-3 mb-3 text-light text-center">
						<label>Name</label>
						<input type="text" class="form-control" name="name" value="<?php echo set_value('name'); ?>" placeholder="Enter Name">
					</div>
					<div class="form-group col-sm-6 offset-sm-3 mb-3 text-light text-center">
						<label>Zipcode</label>
						<input type="text" class="form-control" name="zipcode" value="<?php echo set_value('zipcode'); ?>" placeholder="Zipcode">
					</div>
					<div class="form-group col-sm-6 offset-sm-3 mb-3 text-light text-center">
						<label>Username</label>
						<input type="text" class="form-control" name="username" value="<?php echo set_value('username'); ?>" placeholder="Username">
					</div>
					<div class="form-group col-sm-6 offset-sm-3 mb-3 text-light text-center">
						<label>Email</label>
						<input type="email" class="form-control" name="email" value="<?php echo set_value('email'); ?>" placeholder="email@example.com">
					</div>
					<div class="form-group col-sm-6 offset-sm-3 mb-3 text-light text-center">
						<label>Password</label>
						<input type="password" class="form-control" name="password" placeholder="Password">
					</div>
					<div class="form-group col-sm-6 offset-sm-3 mb-3 text-light text-center">
						<label>Confirm Password</label>
						<input type="password" class="form-control" name="password2" placeholder="Confirm Password">
					</div>
					<br>
					<button type="submit" class="btn btn-dark">Submit</button>
					<br><br>
				</div>
			</div>

		</div>
	</div>
</div>

<?php echo form_close(); ?>
