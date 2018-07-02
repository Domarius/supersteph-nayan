<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Supersteph</title>
    
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <link rel="stylesheet" href="<?php echo base_url(); ?>bower_components/bootstrap/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>bower_components/font-awesome/css/font-awesome.min.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>bower_components/Ionicons/css/ionicons.min.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>dist/css/AdminLTE.min.css"> 
    <link rel="stylesheet" href="<?php echo base_url(); ?>plugins/iCheck/square/blue.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">


    <script src="<?php echo base_url(); ?>bower_components/jquery/dist/jquery.min.js"></script>
    <script src="<?php echo base_url(); ?>bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
    <script src="<?php echo base_url(); ?>plugins/iCheck/icheck.min.js"></script>


    <link href="<?php echo base_url() ?>angularJs/toaster.css" rel="stylesheet">

    <!-- Angular Js-->
    <script src="<?php echo base_url() ?>angularJs/angular.min.js"></script>
    <script src="<?php echo base_url() ?>angularJs/angular-ui-router.js"></script>
    <script src="<?php echo base_url() ?>angularJs/toaster.min.js"></script>

  </head>
  <body class="hold-transition login-page">
    <div ng-app="myApp" ng-controller="assignAcceptPerformerController" ng-cloak>

      <script src="<?php echo base_url() ?>jsController/admin_assignAcceptPerformer.js"></script>
      <script >
        var angular_base_url="<?php echo base_url() ?>";
      </script>
      
      <!-- toaster directive --> 
        <toaster-container toaster-options="{'position-class': 'toast-top-right','time-out': 5000,'close-button':true}"></toaster-container> 
      <!-- / toaster directive -->

      <div class="login-box"  ng-if="show == '1' ">
        
        <div class="login-box-body">
          <p class="login-box-msg" style="font-size: 50px;">Thank You</p>
        </div>
      </div>

    </div>
  </body>
</html>
