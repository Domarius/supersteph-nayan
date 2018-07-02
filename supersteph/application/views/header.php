<!DOCTYPE html>

<html>
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Super steph</title>
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">

    <link rel="stylesheet" href="<?php echo base_url(); ?>bower_components/bootstrap/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>bower_components/font-awesome/css/font-awesome.min.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>bower_components/Ionicons/css/ionicons.min.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>dist/css/AdminLTE.min.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>dist/css/skins/_all-skins.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">

    <link rel="stylesheet" href="<?php echo base_url(); ?>angularJs/toaster.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>angularJs/select.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>angularJs/angular-material.min.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>angularJs/angularjs-datetime-picker.css">

    <!-- Angular Js-->
    <script src="<?php echo base_url() ?>angularJs/angular.min.js"></script>
    <script src="<?php echo base_url() ?>angularJs/angular-ui-router.js"></script>
    <script src="<?php echo base_url() ?>angularJs/toaster.min.js"></script>
    <script src="<?php echo base_url() ?>angularJs/ng-file-upload-shim.min.js"></script>
    <script src="<?php echo base_url() ?>angularJs/ng-file-upload.min.js"></script>
    <script src="<?php echo base_url() ?>angularJs/select.js"></script>
    <script src="<?php echo base_url() ?>angularJs/angularjs-datetime-picker.js"></script>
    
    <!-- Angular Timepicker-->
    <script src="<?php echo base_url() ?>angularJs/position.js"></script>
    <script src="<?php echo base_url() ?>angularJs/timepicker.js"></script>
    <script src="<?php echo base_url() ?>angularJs/timepicker-tpl.js"></script>
    <script src="<?php echo base_url() ?>angularJs/psTimePicker.js"></script>

    <!-- Angular Datepicker-->
    <script src="<?php echo base_url() ?>angularJs/angular-animate.min.js"></script>
    <script src="<?php echo base_url() ?>angularJs/angular-aria.min.js"></script>
    <script src="<?php echo base_url() ?>angularJs/angular-messages.min.js"></script>
    <script src="<?php echo base_url() ?>angularJs/angular-material.min.js"></script>
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.21.0/moment.js"></script>
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/angular-moment/1.2.0/angular-moment.js"></script>

    <script src="http://angular-ui.github.io/bootstrap/ui-bootstrap-tpls-2.5.0.js"></script>
<style>
label {
    display: inline-block;
    max-width: 100%;
    margin-bottom: 5px;
    font-weight: 700;
    margin-left: 13px;
    font-size: 18px;
}
</style>
  </head>

  <body class="hold-transition skin-blue sidebar-mini">
    <div class="wrapper">
      <header class="main-header">

        <a href="" class="logo">
          <span class="logo-mini"><b>S</b>S</span>
          <span class="logo-lg"><b>Super</b>Steph</span>
        </a>

        <nav class="navbar navbar-static-top">
          <a href="" class="sidebar-toggle" data-toggle="push-menu" role="button">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </a>

          <div class="navbar-custom-menu">
            <ul class="nav navbar-nav">
              <li class="messages-menu">
                <a href="<?php echo base_url(); ?>welcome/logout">
                  Log out
                  <span class="label label-success"></span>
                </a>
              </li>
            </ul>    
          </div>

        </nav>
      </header>

      <aside class="main-sidebar">

        <section class="sidebar">
          <div class="user-panel">
            <div class="pull-left image">
              <img src="<?php echo base_url(); ?>dist/img/user2-160x160.jpg" class="img-circle" alt="User Image">
            </div>
            <div class="pull-left info">
              <p>Rakesh Ch</p>
              <a href=""><i class="fa fa-circle text-success"></i> Online</a>
            </div>
          </div>
<label for="Day to Day">Day to day </label>
          <ul class="sidebar-menu" data-widget="tree" style="margin-left: 30px;">

            

            <li>
              <a href="<?php echo base_url(); ?>welcome/bookingRequest/?1">
                <i class="fa fa-snowflake-o"></i> <span>Bookings</span>
              </a>
            </li>
             <li>
              <a href="<?php echo base_url(); ?>welcome/archiveManager">
                <i class="fa fa-snowflake-o"></i> <span>Archived Bookings</span>
              </a>
            </li>
             <li>
              <a href="<?php echo base_url(); ?>welcome/emailManager">
                <i class="fa fa-snowflake-o"></i> <span>Who to Harass</span>
              </a>
            </li>
            <li>
              <a href="<?php echo base_url(); ?>welcome/bulkdownload/?1">
                <i class="fa fa-snowflake-o"></i> <span>Print Upcoming Bookings</span>
              </a>
            </li>
            <li>
              <a href="<?php echo base_url(); ?>welcome/lookingForward">
                <i class="fa fa-snowflake-o"></i> <span>Looking Forward</span>
              </a>
            </li>
             <li>
              <a href="<?php echo base_url(); ?>welcome/feedback">
                <i class="fa fa-snowflake-o"></i> <span>Edit Feedback</span>
              </a>
            </li>
            <li>
              <a href="<?php echo base_url(); ?>welcome/chasingthankyou">
                <i class="fa fa-snowflake-o"></i> <span>Thanks</span>
              </a>
            </li>


            

          </ul>

          <label for="Day to Day">Setup </label>
          <ul class="sidebar-menu" data-widget="tree" style="margin-left: 30px;">

            

          <!--  
            -->
            <li>
              <a href="<?php echo base_url(); ?>welcome/performer">
                <i class="fa fa-snowflake-o"></i> <span>Performer</span>  
              </a>
            </li>
            <li>
              <a href="<?php echo base_url(); ?>welcome/category">
                <i class="fa fa-snowflake-o"></i> <span>Roles</span>  
              </a>
            </li>
           
           <!--  <li>
              <a href="<?php echo base_url(); ?>welcome/category">
                <i class="fa fa-th"></i> <span>Category</span>
              </a>
            </li> -->

           

           <!--  <li>
              <a href="<?php echo base_url(); ?>welcome/emailManager">
                <i class="fa fa-google-plus"></i> <span>Email Manager</span>
              </a>
            </li> -->

            <li>
              <a href="<?php echo base_url(); ?>welcome/history">
                <i class="fa fa-snowflake-o"></i> <span>Admin history</span>
              </a>
            </li>
             <li>
              <a href="<?php echo base_url(); ?>welcome/emailTemplate">
                <i class="fa fa-snowflake-o"></i> <span>Email Template</span>
              </a>
            </li>
          </ul>

        </section>
      </aside>

      <script >
        var base_url="<?php echo base_url() ?>";
      </script>

      