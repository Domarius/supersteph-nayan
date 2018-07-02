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

      <div class="right_col" role="main" ng-app="myApp" ng-controller="generalAddBookingController" ng-cloak>

        <script src="<?php echo base_url() ?>jsController/admin_generalAddBooking.js"></script>

        <!-- toaster directive --> 
          <toaster-container toaster-options="{'position-class': 'toast-top-right','time-out': 5000,'close-button':true}"></toaster-container> 
        <!-- / toaster directive -->


        <div class="content-wrapper">
         
          <section class="content">
            <div class="row">
             
              <div class="col-md-9">

                <div class="box box-primary">
                  <div class="box-body">
                    <div class="col-sm-6" ng-repeat="list in listing">
                      <div class="row">
                        <div class="col-sm-3">
                          <label>Name:</label>  
                        </div>
                        <div class="col-sm-6">
                          <p>{{list.name}}</p>
                        </div>
                      </div> 
                      <div class="row">
                        <div class="col-sm-3">
                          <label>Category:</label>  
                        </div>
                        <div class="col-sm-6">
                          <p>{{list.category_name}}</p>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-sm-3">
                          <label>Email:</label>  
                        </div>
                        <div class="col-sm-6">
                          <p>{{list.email}}</p>
                        </div>
                      </div> 
                      <div class="row">
                        <div class="col-sm-3">
                          <label>Mobile:</label>  
                        </div>
                        <div class="col-sm-6">
                          <p>{{list.mobile}}</p>
                        </div>
                      </div> 
                      <div class="row">
                        <div class="col-sm-3">
                          <label>Description:</label>  
                        </div>
                        <div class="col-sm-6">
                          <p>{{list.description}}</p>
                        </div>
                      </div> 
                      <div class="row">
                        <div class="col-sm-3">
                          <label>Image:</label>  
                        </div>
                        <div class="col-sm-6">
                          <img ng-src="{{list.profile_image}}" style="height: 60px; width: 60px;">
                        </div>
                      </div> 
                    </div>
                  </div>
                </div>

                <div class="box box-primary">
              
                  <form  name="addStep">
                    <div class="box-body">
<h3 Style="color:red;" > Note: Please fill out all fields to submit the Form</h3>
                      <div class="form-group">
                        <label>Where did you hear of SuperSteph?:</label>
                        <input type="text" class="form-control" placeholder="" ng-model="get.hear_supersteph" required>
                      </div>
                    
                      <div class="form-group">
                        <label>How many Party Bags required? ($3.50 each- available only in Brisbane branch only):</label>
                        <input type="text" class="form-control" placeholder="" ng-model="get.party_bags" required>
                      </div>

                      <div class="form-group">
                        <label>Address of party:</label>
                        <input type="text" class="form-control" placeholder="" ng-model="get.party_address" required>
                      </div>
                    
                      <div class="form-group">
                        <label>Home address (if same as party, please put "as above"):</label>
                        <input type="text" class="form-control" placeholder="" ng-model="get.home_address" required>
                      </div>
                    
                      <div class="form-group">
                        <label>Parking facilities (any paid parking is to be paid by the client):</label>
                        <input type="text" class="form-control" placeholder="" ng-model="get.parking_facility" required>
                      </div>
                    
                      <div class="form-group">
                        <label>Is the party inside or outside?:</label>
                        <input type="text" class="form-control" placeholder="" ng-model="get.party_in_out" required>
                      </div>
                    
                      <div class="form-group">
                        <label>Rain contingency plans (A MUST for outside parties):</label>
                        <input type="text" class="form-control" placeholder="" ng-model="get.rain_plan" required>
                      </div>
                    
                      <div class="form-group">
                        <label>2nd Mobile number:</label>
                        <input type="text" class="form-control" placeholder="" ng-model="get.second_mobile" required>
                      </div>
                    
                      <div class="form-group">
                        <label>Name of child:</label>
                        <input type="text" class="form-control" placeholder="" ng-model="get.child_name" required>
                      </div>
                    
                      <div class="form-group">
                        <label>Age on birthday:</label>
                        <input type="text" class="form-control" placeholder="" ng-model="get.age" required>
                      </div>
                    
                      <div class="form-group">
                        <label>Date of birth:</label>
                        <input type="text" class="form-control" placeholder="" ng-model="get.dob" required>
                      </div>
                    
                      <div class="form-group">
                        <label>Main age of children at the party:</label>
                        <input type="text" class="form-control" placeholder="" ng-model="get.children_party" required>
                      </div>
                    
                      <div class="form-group">
                        <label>Boys, girls, or mixed party?:</label>
                        <input type="text" class="form-control" placeholder="" ng-model="get.gender_party" required>
                      </div>
                    
                      <div class="form-group">
                        <label>Number of children at the party. NOTE: Some party packages have a maximum number of children. Please contact us if you have more than the maximum:</label>
                        <input type="text" class="form-control" placeholder="" ng-model="get.children_count" required>
                      </div>
                    
                      <div class="form-group">
                        <label>Has your child seen my show before? If yes, where?:</label>
                        <input type="text" class="form-control" placeholder="" ng-model="get.party_seen" required>
                      </div>
                    
                      <div class="form-group">
                        <label>Enter your FULL NAME to show you have read and agree to the Terms and Conditions:</label>
                        <input type="text" class="form-control" placeholder="" ng-model="get.show_fullname" required>
                      </div>
                    
                      <div class="form-link">
                        <p>
                          <a href="http://www.supersteph.com/terms" style="text-decoration:underline !important;" target="_blank">Please CLICK HERE to read the terms and conditions</a>
                        </p>
                      </div>
                    </div>
               
                    <div class="box-footer">
                      <button type="submit" class="btn btn-success" ng-disabled="addStep.$invalid" ng-click="addGeneralBooking()">Save</button>
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