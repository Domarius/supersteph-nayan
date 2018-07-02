<div class="right_col" role="main" ng-app="myApp" ng-controller="ViewBookingRequestController" ng-cloak>

  <script src="<?php echo base_url() ?>jsController/admin_viewbookingRequest.js"></script>

  <!-- toaster directive --> 
    <toaster-container toaster-options="{'position-class': 'toast-top-right','time-out': 5000,'close-button':true}"></toaster-container> 
  <!-- / toaster directive -->

  <style>
    .search-filter{
      padding: 7px;
      width: 400px;
    }
    .margin-tab{
      margin-top: : 15px !important;
    }
  </style>
  <div class="content-wrapper">
    
    <section class="content-header">
      <h1>View Booking Request</h1>
      <ol class="breadcrumb">
        <li><a href=""><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="">Booking</a></li>
        <li class="active">View Booking Request</li>
      </ol>
    </section>

    <section class="content">
      <div class="row">
        <div class="col-md-12">

          <div class="box box-primary">
            <div class="box-header">
              <div class="col-sm-2"> 
                <a href="<?php echo base_url(); ?>welcome/actionbookingRequest/?{{id}}/?{{parameters.page}}" class="btn btn-primary" >Back</a>
              </div>
            </div>
            <div class="box-header with-border">
              
                <form name="addStep">

                  <div class="box-body"> 

                    <div class="form-group">
                      <label>Name:</label>
                      <input type="text" class="form-control" placeholder="" ng-model="get.name" readonly="">
                    </div>

                    <div class="form-group">
                      <label>Email:</label>
                      <input type="text" class="form-control" placeholder="" ng-model="get.email" readonly="">
                    </div>

                    <div class="form-group">
                      <label>Mobile Number:</label>
                      <input type="text" class="form-control" placeholder="" ng-model="get.mobile_number" readonly="">
                    </div>

                    <div class="form-group">
                      <label>Event date:</label>
                      <input type="text" class="form-control" placeholder="" ng-model="get.event_date" readonly="">
                    </div>

                    <div class="form-group">
                      <label>Show Type:</label>
                      <input type="text" class="form-control" placeholder="" ng-model="get.show_type" readonly="">
                    </div>

                    <div class="form-group">
                      <label>Show Time:</label>
                      <input type="text" class="form-control" placeholder="" ng-model="get.show_time" readonly="">
                    </div>

                    <div class="form-group">
                      <label>Duration:</label>
                      <input type="text" class="form-control" placeholder="" ng-model="get.duration" readonly="">
                    </div>

                    <div class="form-group">
                      <label>Booking Status:</label>
                      <input type="text" class="form-control" placeholder="" ng-model="get.booking_status" readonly="">
                    </div>

                    <div class="form-group">
                      <label>Where did you hear of SuperSteph?:</label>
                      <input type="text" class="form-control" placeholder="" ng-model="get.hear_supersteph" readonly="">
                    </div>

                    <div class="form-group">
                      <label>How many Party Bags required? ($3.50 each- available only in Brisbane branch only):</label>
                      <input type="text" class="form-control" placeholder="" ng-model="get.party_bags" readonly="">
                    </div>

                    <div class="form-group">
                      <label>Address of party:</label>
                      <input type="text" class="form-control" placeholder="" ng-model="get.party_address" readonly="">
                    </div>
                  
                    <div class="form-group">
                      <label>Home address (if same as party, please put "as above"):</label>
                      <input type="text" class="form-control" placeholder="" ng-model="get.home_address" readonly="">
                    </div>
                  
                    <div class="form-group">
                      <label>Parking facilities (any paid parking is to be paid by the client):</label>
                      <input type="text" class="form-control" placeholder="" ng-model="get.parking_facility" readonly="">
                    </div>
                  
                    <div class="form-group">
                      <label>Is the party inside or outside?:</label>
                      <input type="text" class="form-control" placeholder="" ng-model="get.party_in_out" readonly="">
                    </div>
                  
                    <div class="form-group">
                      <label>Rain contingency plans (A MUST for outside parties):</label>
                      <input type="text" class="form-control" placeholder="" ng-model="get.rain_plan" readonly="">
                    </div>
                  
                    <div class="form-group">
                      <label>2nd Mobile number:</label>
                      <input type="text" class="form-control" placeholder="" ng-model="get.second_mobile" readonly=""> 
                    </div>
                  
                    <div class="form-group">
                      <label>Name of child:</label>
                      <input type="text" class="form-control" placeholder="" ng-model="get.child_name" readonly="">
                    </div>
                  
                    <div class="form-group">
                      <label>Age on birthday:</label>
                      <input type="text" class="form-control" placeholder="" ng-model="get.age" readonly="">
                    </div>
                  
                    <div class="form-group">
                      <label>Date of birth:</label>
                      <input type="text" class="form-control" placeholder="" ng-model="get.dob" readonly="">
                    </div>
                  
                    <div class="form-group">
                      <label>Main age of children at the party:</label>
                      <input type="text" class="form-control" placeholder="" ng-model="get.children_party" readonly="">
                    </div>
                  
                    <div class="form-group">
                      <label>Boys, girls, or mixed party?:</label>
                      <input type="text" class="form-control" placeholder="" ng-model="get.gender_party" readonly="">
                    </div>
                  
                    <div class="form-group">
                      <label>Number of children at the party. NOTE: Some party packages have a maximum number of children. Please contact us if you have more than the maximum:</label>
                      <input type="text" class="form-control" placeholder="" ng-model="get.children_count" readonly="">
                    </div>
                  
                    <div class="form-group">
                      <label>Has your child seen my show before? If yes, where?:</label>
                      <input type="text" class="form-control" placeholder="" ng-model="get.party_seen" readonly="">
                    </div>
                  
                    <div class="form-group">
                      <label>Enter your FULL NAME to show you have read and agree to the Terms and Conditions:</label>
                      <input type="text" class="form-control" placeholder="" ng-model="get.show_fullname" readonly="">
                    </div>

                  </div>

                </form>
            </div>   
          </div>

        </div>
      </div>  
    </section>
  </div>
</div>