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

    <!-- Angular Js-->
    <script src="<?php echo base_url() ?>angularJs/angular.min.js"></script>
    <script src="<?php echo base_url() ?>angularJs/angular-ui-router.js"></script>
    <script src="<?php echo base_url() ?>angularJs/toaster.min.js"></script>
    <script src="<?php echo base_url() ?>angularJs/ng-file-upload-shim.min.js"></script>
    <script src="<?php echo base_url() ?>angularJs/ng-file-upload.min.js"></script>
    <script src="<?php echo base_url() ?>angularJs/select.js"></script>


    <!-- Angular Timepicker-->
    <script src="<?php echo base_url() ?>angularJs/position.js"></script>
    <script src="<?php echo base_url() ?>angularJs/psTimePicker.js"></script>
    <script src="<?php echo base_url() ?>angularJs/timepicker.js"></script>
    <script src="<?php echo base_url() ?>angularJs/timepicker-tpl.js"></script>

    <!-- Angular Datepicker-->
    <script src="<?php echo base_url() ?>angularJs/angular-animate.min.js"></script>
    <script src="<?php echo base_url() ?>angularJs/angular-aria.min.js"></script>
    <script src="<?php echo base_url() ?>angularJs/angular-messages.min.js"></script>
    <script src="<?php echo base_url() ?>angularJs/angular-material.min.js"></script>

  </head>

  <body class="hold-transition skin-blue sidebar-mini">
    <div class="wrapper">
      
      <script >
        var base_url="<?php echo base_url() ?>";
      </script>

      <div class="right_col" role="main" ng-app="myApp" ng-controller="generalAddFeedbackController" ng-cloak>

        <script src="<?php echo base_url() ?>jsController/admin_generalAddFeedback.js"></script>

        <!-- toaster directive --> 
          <toaster-container toaster-options="{'position-class': 'toast-top-right','time-out': 5000,'close-button':true}"></toaster-container> 
        <!-- / toaster directive -->

        <style>
          .marginTab{
            margin-bottom: 20px;
          }
        </style>


        <div class="content-wrapper">

          <section class="content">
            <div class="row">
              <div class="col-md-9">

                <div class="box box-primary">
                  <div class="box-body">
                    <div class="col-sm-6" ng-repeat="list in listing">
                      <div class="row">
                        <div class="col-sm-5">
                          <label>Booking Name:</label>  
                        </div>
                        <div class="col-sm-6">
                          <p>{{list.booking_name}}</p>
                        </div>
                      </div> 
                      <div class="row">
                        <div class="col-sm-5">
                          <label>Show Date:</label>  
                        </div>
                        <div class="col-sm-6">
                          <p>{{list.showDate}}</p>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-sm-5">
                          <label>Show Time:</label>  
                        </div>
                        <div class="col-sm-6">
                          <p>{{list.showTime}}</p>
                        </div>
                      </div> 
                      <div class="row">
                        <div class="col-sm-5">
                          <label>Character:</label>  
                        </div>
                        <div class="col-sm-6">
                          <p>{{list.showType}}</p>
                        </div>
                      </div> 
                      <div class="row">
                        <div class="col-sm-5">
                          <label>Show Type:</label>  
                        </div>
                        <div class="col-sm-6">
                          <p>{{list.show_type_text}}</p>
                        </div>
                      </div> 
                      <div class="row">
                        <div class="col-sm-5">
                          <label>Child Name:</label>  
                        </div>
                        <div class="col-sm-6">
                          <p>{{list.child_name}}</p>
                        </div>
                      </div> 
                    </div>
                  </div>
                </div>

                <div class="box box-primary">
              
                  <form  name="addStep">
                    <div class="box-body">

                      <div class="col-sm-12">
                        <label style="margin-top: 15px;">Goods point about party (Write in 3rd person to aid us in composing the mail)</label>
                      </div>
                      <div class="col-sm-12">
                        <textarea rows="4" cols="120" placeholder="" ng-model="get.good_points" required=""></textarea>
                      </div>
                     
                      <div class="col-sm-12">
                        <label style="margin-top: 15px;">Bad point about party (Problems which may affect the client's opinion of us - be honest! We will get bad parties!)</label>
                      </div>
                      <div class="col-sm-12">
                        <textarea rows="4" cols="120" placeholder="" ng-model="get.bad_points" required=""></textarea>
                      </div>
                     
                      <div class="col-sm-12">
                        <label style="margin-top: 15px;">Rating your Party!</label>
                      </div>
                      <div class="col-sm-12">
                        <select ng-model="get.rating" required="">
                          <option value="">Please choose--</option>
                          <option value="Best party ever- the show buzzed!!">Best party ever- the show "buzzed"!</option>
                          <option value="Great party!">Great party!</option>
                          <option value="Average - I wont remember it in a few days">Average - I won't remember it in a few days</option>
                          <option value="Not Good:(Terrible - Im surprised Im still sane!)">Not Good:(Terrible - I'm surprised I'm still sane!)</option>
                        </select>
                      </div>
                     
                    </div>
               
                    <div class="box-footer">
                      <button type="button" class="btn btn-success" ng-disabled="addStep.$invalid" ng-click="addGeneralFeedback()">Save</button>
                    </div>
                  </form>
                </div>
            
              </div>
            </div>
          </section>
        </div>


      </div>
    </div>
    <!-- ./wrapper -->

    <!-- jQuery 3 -->
    <script src="<?php echo base_url(); ?>bower_components/jquery/dist/jquery.min.js"></script>
    <!-- Bootstrap 3.3.7 -->
    <script src="<?php echo base_url(); ?>bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
    <!-- DataTables -->
    <script src="<?php echo base_url(); ?>bower_components/datatables.net/js/jquery.dataTables.min.js"></script>
    <script src="<?php echo base_url(); ?>bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>
    <!-- SlimScroll -->
    <script src="<?php echo base_url(); ?>bower_components/jquery-slimscroll/jquery.slimscroll.min.js"></script>
    <!-- FastClick -->
    <script src="<?php echo base_url(); ?>bower_components/fastclick/lib/fastclick.js"></script>
    <!-- AdminLTE App -->
    <script src="<?php echo base_url(); ?>dist/js/adminlte.min.js"></script>
    <!-- AdminLTE for demo purposes -->
    <script src="<?php echo base_url(); ?>dist/js/demo.js"></script>
    <!-- page script -->
    
  </body>
</html>