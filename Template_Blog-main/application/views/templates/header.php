<html>

<head>
    <title>AVIEN</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/Template_Blog/assets/css/bootstrap.css">
    <link rel="icon" src="/Template_Blog/assets/images/favicon" type="image/png" sizes="16x16">

    <script src="http://getbootstrap.com/docs/assets/js/ie8-responsive-file-warning.js"></script>


    <title>Product example for Bootstrap</title>

    <!-- <link rel="canonical" href="https://getbootstrap.com/docs/4.0/examples/product/"> -->
    <link rel="stylesheet" href="path/to/font-awesome/css/font-awesome.min.css">


    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
</head>


<body class="body">

    <nav class="site-header navbar navbar-expand-lg navbar-dark bg-dark sticky-top py-1">
        <div class="container d-flex flex-column flex-md-row justify-content-between">

            <a class="navbar-brand" href="<?php echo base_url(); ?>"><img src="/Template_Blog/assets/images/LOGO_1" width="10%" height="auto" />Avien</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav mr-auto">
                    <li class="nav-item active">
                        <a class="nav-link" style="font-family:cursive;" href="<?php echo base_url(); ?>">Home <span class="sr-only"></span></a>
                    </li>
                    <li>
                        <a class="nav-link" href="<?php echo base_url(); ?>posts">BLOG</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo base_url(); ?>categories">Categories</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo base_url(); ?>about">About</a>
                    </li>
                </ul>
                <ul class="navbar-nav mr-auto">
                    <?php if (!$this->session->userdata('logged_in')) : ?>
                        <li class="nav-item active">
                            <a class="nav-link" href="<?php echo base_url(); ?>auth/login">Login<span class="sr-only"></span></a>
                        </li>
                        <li class="nav-item active">
                            <a class="nav-link" href="<?php echo base_url(); ?>users/register">Register<span class="sr-only"></span></a>
                        </li>
                    <?php endif; ?>
                    <?php if ($this->session->userdata('logged_in')) : ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                Actions
                            </a>
                            <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                                <a class="dropdown-item" href="<?php echo base_url(); ?>posts/create">Create Post</a>
                                <a class="dropdown-item" href="<?php echo base_url(); ?>categories/create">Create Category</a>
                                <a class="dropdown-item" href="<?php echo base_url(); ?>auth/logout">Logout</a>
                            </div>
                        </li>

                    <?php endif; ?>
                </ul>

            </div>
        </div>
    </nav>
    <div class="col-12 flex-fill" style="background-color: #555555;">
        <!--- Flash Message -->
        <?php if ($this->session->flashdata('user_registered')) : ?>
            <p class="alert alert-success"><?php echo $this->session->flashdata('user_registered'); ?></p>
        <?php endif; ?>

        <?php if ($this->session->flashdata('post_created')) : ?>
            <p class="alert alert-success"><?php echo $this->session->flashdata('post_created'); ?></p>
        <?php endif; ?>

        <?php if ($this->session->flashdata('post_updated')) : ?>
            <p class="alert alert-success"><?php echo $this->session->flashdata('post_updated'); ?></p>
        <?php endif; ?>

        <?php if ($this->session->flashdata('category_created')) : ?>
            <p class="alert alert-success"><?php echo $this->session->flashdata('category_created'); ?></p>
        <?php endif; ?>

        <?php if ($this->session->flashdata('post_deleted')) : ?>
            <p class="alert alert-success"><?php echo $this->session->flashdata('post_deleted'); ?></p>
        <?php endif; ?>

        <?php if ($this->session->flashdata('login_failed')) : ?>
            <p class="alert alert-danger"><?php echo $this->session->flashdata('login_failed'); ?></p>
        <?php endif; ?>

        <?php if ($this->session->flashdata('user_loggedin')) : ?>
            <p class="alert alert-success"><?php echo $this->session->flashdata('user_loggedin'); ?></p>
        <?php endif; ?>

        <?php if ($this->session->flashdata('user_loggedout')) : ?>
            <p class="alert alert-success"><?php echo $this->session->flashdata('user_loggedout', 'You are now logged out'); ?></p>
        <?php endif; ?>
