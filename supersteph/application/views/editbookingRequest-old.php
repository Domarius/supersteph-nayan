<link rel="stylesheet" href="https://cdn.rawgit.com/codekraft-studio/angular-page-loader/master/dist/angular-page-loader.css">
<script type="text/javascript" src="https://cdn.rawgit.com/codekraft-studio/angular-page-loader/master/dist/angular-page-loader.min.js"></script>

<div class="right_col" role="main" ng-app="myApp" ng-controller="editBookingRequestController" ng-cloak>

<!-- <page-loader></page-loader> -->
  <script src="<?php echo base_url() ?>jsController/admin_editbookingRequest.js"></script>

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
      .md-virtual-repeat-container .md-virtual-repeat-scroller {
    bottom: 48px;
    box-sizing: border-box;
    left: 28px !important;
    margin: 0;
    overflow-x: hidden;
    padding: 0;
    position: absolute;
    right: 0;
    top: 0;
    width: 327px !important;  
    /* height: 199px !important; */
}
.md-datepicker-input {
    font-size: 14px;
    box-sizing: border-box;
    border: none;
    box-shadow: none;
    outline: 0;
    background: 0 0;
    min-width: 120px;
    max-width: 328px;
    pointer-events: none;
}
  </style>
  <style>
.glyphicon {
    display:none !important;
  }
      </style>
  <div class="content-wrapper">
    
    <section class="content-header">
      <h1>Edit Booking </h1>
      <ol class="breadcrumb">
        <li><a href=""><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="">Booking</a></li>
        <li class="active">Edit Booking</li>
      </ol>
    </section>

    <section class="content"> <!-- /?{{id}} -->
    <!-- <a href="<?php echo base_url() ?>welcome/bookingRequest/?{{parameters.page}}">
                          <button type="button" class="btn btn-warning">Back</button>
                        </a> -->
                      
      <div class="row">
       
        <div class="col-md-6">

          <div class="box box-primary">
            <div class="box-header with-border">
              
              <form name="editStep" >
<div class="col-md-10">
                      <div class="form-group">
                        <a href="<?php echo base_url() ?>welcome/bookingRequest/?{{parameters.page}}">
                          <button type="button" class="btn btn-warning">Back</button>
                        </a>
                        <button type="submit" ng-disabled="isDisabled" ng-model="isDisabled" class="btn btn-success" ng-click="editBookingRequest(); disableClick();"  id="btn1" >Save</button>
                         <a class="btn btn-sm btn-danger" data-toggle="modal" data-target="#myModal" ng-click="BookingID(get.id)">
                            <i class="fa fa-trash-o" aria-hidden="true"></i> Cancel
                          </a>
                           <a class="btn btn-sm btn-danger" data-toggle="modal" data-target="#myModal" ng-click="BookingID(get.id)">
                            <i class="fa fa-trash-o" aria-hidden="true"></i> PaperWork Form
                          </a>
                        <div style="margin-bottom: 5px;" ng-if="list.booking_status == 'Cancelled'">
                          <button type="button" class="btn btn-sm btn-info" ng-click="reactive(list.id);">Re-Active</button>
                        </div>
                        <!-- {{get.paid_amount}} --><!-- 
                        <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#paidAmountModal" ng-click="PaidID(get.id, get.event_amount, get.remain_amount)">Pay Amount</button> -->
                      
                     <!--  <label>Pay Fee</label> <input type="text"  name="pay_amount" class="form-control" placeholder="Pay Fee" ng-model="get.pay_amount" style="width: 70px; float: right; margin-right: 10%;"> -->
                      <input type="checkbox" ng-model="get.email_confirm" style="margin-left:20px"> <strong>Send customer Email </strong>
                       <input type="checkbox" ng-model="get.email_paperwork" style="margin-left:20px"> <strong>Send Paperwork Email </strong>
                     
                      </div>
                      <div class="col-md-12">
                    

                    <div class="col-md-6">
                      <div class="form-group">

                       <input type="hidden"  name="remain_amount" class="form-control" id="amount" placeholder="Remain Fee" ng-model="get.remain_amount" value="okay" >
                          </div>
                    </div>
                  </div>
                  <div class="col-md-3">
                      <div class="form-group">
                        
                      </div>
                    </div>

                    <div class="col-md-6">
                      <div class="form-group">
                       
                      </div>
                    </div>
                  </div>
                    </div>
                <div class="box-body"> 

                  <div class="col-md-12">
                    <div class="col-md-4">
                      <div class="form-group">
                     <label>Paid Fee</label> 
                      </div>
                    </div>

                    <div class="col-md-5">
                      <div class="form-group"><!-- id="paid" -->
                      <!--  <input type="text"   name="pay_amount" class="form-control" placeholder="Pay Fee" ng-model="get.paid_amount" style="width: 70px; float: right; margin-right: 10%;"> 
 -->
                        <input type="text"  name="pay_amount" class="form-control" placeholder="Pay Fee" ng-model="get.paid_amount" style="width: 70px; float: right; margin-right: 10%;">
                      </div>
                    </div>
                  </div>

                  <div class="col-md-12">
                    <div class="col-md-4">
                      <div class="form-group">
                        <label>Name</label>
                      </div>
                    </div>

                    <div class="col-md-5">
                      <div class="form-group">
                        <input type="text"  name="name" class="form-control" placeholder="Name" ng-model="get.name" required>
                      </div>
                    </div>
                  </div>

                  <div class="col-md-12">
                    <div class="col-md-4">
                      <div class="form-group">
                        <label>Email</label>
                      </div>
                    </div>

                    <div class="col-md-5">
                      <div class="form-group">
                        <input type="text"  name="email" class="form-control" placeholder="Email" ng-model="get.email" required>
                      </div>
                    </div>
                  </div>

                  <div class="col-md-12">
                    <div class="col-md-4">
                      <div class="form-group">
                        <label>Mobile Number</label>
                      </div>
                    </div>

                    <div class="col-md-5">
                      <div class="form-group">
                        <input type="text"  name="mobile_number" class="form-control" placeholder="Mobile" ng-model="get.mobile_number" required>
                      </div>
                    </div>
                  </div>

                  <div class="col-md-12">
                    <div class="col-md-4">
                      <div class="form-group">
                        <label>Event Date</label>
                      </div>
                    </div>

                    <div class="col-md-8">
                      <div class="form-group">
                        <md-content>
                          <md-datepicker ng-model = "get.event_date" md-placeholder="dd/mm/yyyy" readonly ></md-datepicker>
                        </md-content>
                      </div>
                    </div>
                  </div>
                   <div class="col-md-12">
                    <div class="col-md-4">
                      <div class="form-group">
                        <label>Fee</label>
                      </div>
                    </div>

                    <div class="col-md-5">
                      <div class="form-group">
                        <input type="text"  name="event_amount" ng-keyup="evnt(get.event_amount)" class="form-control" placeholder="Fee" ng-model="get.event_amount" required >
                        <input type="hidden"  name="paid" class="form-control" placeholder="Fee" ng-model="get.paid"  />
                      </div>
            