<?php echo form_open(); ?>

<div class="container">
  <div class="row">
    <div class="col-md-6 offset-md-3">
      <h2 class="text-center text-dark mt-5"></h2>

      <div class="card my-5  bg-dark">

        <form class="card-body cardbody-dark p-lg-5 ">

          <div class="text-center">
            <img src="/Template_Blog/assets/images/LOGO_1" class="img-fluid profile-image-pic img-thumbnail rounded-circle my-3" width="200px" alt="profile">
          </div>

          <div class="row">
            <div class="col-sm-6 offset-sm-3 mb-3 text-light text-center">
              <label for="username">Username</label>
              <input type="text" class="form-control" id="username" name="username" placeholder="Enter Username" required autofocus>
            </div>
          </div>
          <div class="row">
            <div class="col-sm-6 offset-sm-3 mb-3 text-light text-center">
              <label for="password">Password</label>
              <input type="password" class="form-control" id="password" name="password" placeholder="Enter Password" required autofocus>
            </div>
          </div>

          <div class="text-center"><button type="submit" class="btn btn-primary px-5 mb-5 w-100">Login</button></div>
          <div id="emailHelp" class="form-text text-center mb-5 text-light">Not
            Registered? <a href="users/register" class="text-light fw-bold"> Create an
              Account</a>
          </div>
        </form>
      </div>

    </div>
  </div>
</div>
<?php echo form_close(); ?>
<link rel="stylesheet" href="/Template_Blog/assets/css/bootstrap.css">
<link rel="icon" src="/Template_Blog/assets/images/favicon" type="image/png" sizes="16x16">

<script src="http://getbootstrap.com/docs/assets/js/ie8-responsive-file-warning.js"></script>