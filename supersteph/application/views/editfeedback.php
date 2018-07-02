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


  </head>

  <body class="hold-transition skin-blue sidebar-mini">
    <div class="wrapper">
      
      <script >
        var base_url="<?php echo base_url() ?>";
      </script>

      <div class="right_col" role="main" >
<!-- ng-app="myApp" ng-controller="generalAddFeedbackController" ng-cloak
        <script src="<?php echo base_url() ?>jsController/admin_generalAddFeedback.js"></script>
 -->
        <!-- toaster directive --> 
         
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
<form  action="<?php echo base_url() ?>welcome/saveeditedfeedback" method="post" >
                    
               
<?php 
foreach($feedback_details as $feedback){
	
?>

                <div class="box box-primary">
              
                  <div class="box-body">

                      <div class="col-sm-12">
                        <label style="margin-top: 15px;">Goods point about party (Write in 3rd person to aid us in composing the mail)</label>
                      </div>
                      <div class="col-sm-12">
                      <input type="hidden" name="id" value="<?php echo $feedback['id']; ?>">
                        <textarea rows="4" cols="120" placeholder="" name="good_points" required=""><?php echo $feedback['good_points']; ?> </textarea>
                      </div>
                     
                      <div class="col-sm-12">
                        <label style="margin-top: 15px;">Bad point about party (Problems which may affect the client's opinion of us - be honest! We will get bad parties!)</label>
                      </div>
                      <div class="col-sm-12">
                        <textarea rows="4" cols="120" placeholder="" name="bad_points"  required=""><?php echo $feedback['bad_points']; ?></textarea>
                      </div>
                     
                      <div class="col-sm-12">
                        <label style="margin-top: 15px;">Rating your Party!</label>
                      </div>
                      <div class="col-sm-12">
                        <select required="" name="rating" >
                          <option <?php if($feedback['rating']==""){ echo "selected" ; } ?> value="">Please choose--</option>
                          <option <?php if($feedback['rating']=="Best party ever- the show buzzed!!"){ echo "selected" ; } ?> value="Best party ever- the show buzzed!!">Best party ever- the show ,buzzed!</option>
                          <option <?php if($feedback['rating']=="Great party!"){ echo "selected" ; } ?> value="Great party!">Great party!</option>
                          <option <?php if($feedback['rating']=="Average - I wont remember it in a few days"){ echo "selected" ; } ?> value="Average - I wont remember it in a few days">Average - I won't remember it in a few days</option>
                          <option <?php if($feedback['rating']=="Not Good:(Terrible - Im surprised Im still sane!)"){ echo "selected" ; } ?> value="Not Good:(Terrible - Im surprised Im still sane!)">Not Good:(Terrible - I'm surprised I'm still sane!)</option>
                        </select>
                      </div>
                     
                    </div>
               
                    <div class="box-footer">
                    <input type="submit" name="Update" value="Save" class="btn btn-success" >
                     <!--  <button type="button" class="btn btn-success" >Save</button> -->
                    </div>
                  
                </div>
            
              </div>
            </div>
          </section>
        </div>
<?php } ?>
</form>
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
    <!-- page script -->
    
  </body>
</html>