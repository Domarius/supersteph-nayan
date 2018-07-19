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

                        <div style="margin-bottom: 5px;" ng-if="list.booking_status == 'Cancelled'">
                          <button type="button" class="btn btn-sm btn-info" ng-click="reactive(list.id);">Re-Active</button>
                        </div>
                        <!-- {{get.paid_amount}} --><!-- 
                        <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#paidAmountModal" ng-click="PaidID(get.id, get.event_amount, get.remain_amount)">Pay Amount</button> -->
                      
                     <!--  <label>Pay Fee</label> <input type="text"  name="pay_amount" class="form-control" placeholder="Pay Fee" ng-model="get.pay_amount" style="width: 70px; float: right; margin-right: 10%;"> -->
                      <input type="checkbox" ng-model="get.email_confirm" style="margin-left:20px"> <strong>Send customer Email </strong>
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
                    </div>
                  </div>
                    <div class="col-md-12">
                    <div class="col-md-4">
                      <div class="form-group">
                        <label>Show Type</label>
                      </div>
                    </div>

                    <div class="col-md-5">
                      <div class="form-group">
                        <input type="text"  name="show_type_text" class="form-control" placeholder="Show Type" ng-model="get.show_type_text" required>
                      </div>
                    </div>
                  </div>

                  <div class="col-md-12">
                    <div class="col-md-4">
                      <div class="form-group">
                        <label>Role</label>
                      </div>
                    </div>

                    <div class="col-md-8">
                      <div class="form-group">
                     <!--  mobile
                       <input type="text"  name="mobile_number" class="form-control" placeholder="Mobile" ng-model="get.show_type" required>
                       Category -->
                       <ui-select multiple data-ng-model="get.show_type" theme="bootstrap" required>
                          <ui-select-match placeholder="Please Select Category.." ng-cloak>{{$item.category_name}}</ui-select-match>
                          <ui-select-choices repeat="type in categoryListing | propsFilter: {category_name: $select.search}">
                          <small>
                            {{type.category_name}}
                          </small>
                          </ui-select-choices>
                        </ui-select> 


                        <!-- <ui-select multiple data-ng-model="get.category_name" theme="bootstrap" required>
                            <ui-select-match placeholder="Please Select Category.." ng-cloak>{{$item.category_name}}</ui-select-match>
                            <ui-select-choices repeat="type.id as type in categoryListing |   propsFilter: {category_name: $select.search}">
                            <small>
                              {{type.category_name}}
                            </small>
                            </ui-select-choices>
                          </ui-select> -->
                      </div>
                    </div>
                  </div>

                  <div class="col-md-12">
                    <div class="col-md-4">
                      <div class="form-group">
                        <label>Show time</label>
                      </div>
                    </div>

                    <div class="col-md-8">
                      <div class="form-group">
                        <!-- <input ps-input-time sy-timepicker-popup="HH:mm" class="form-control" ng-model="get.show_time" show-meridian="false" is-open="opened1" /> -->
                        <div uib-timepicker ng-model="get.show_time" hour-step="hstep" minute-step="mstep" show-meridian="ismeridian"></div>
                      </div>
                    </div>
                  </div>

                  <div class="col-md-12">
                    <div class="col-md-3">
                      <div class="form-group">
                        <label>Duration</label>
                      </div>
                    </div>

                    <div class="col-md-9">
                      <div class="form-group">
                        <ui-select data-ng-model="get.duration" theme="bootstrap" required>
                          <ui-select-match placeholder="Please Select Duration.." ng-cloak>{{$select.selected.label}}</ui-select-match>
                          <ui-select-choices repeat="type.value as type in durationListing | filter: $select.search">
                            <small>
                              {{type.label}}
                            </small>
                          </ui-select-choices>
                        </ui-select>
                      </div>
                    </div>
                  </div>

                  <div class="col-md-12">
                    <div class="col-md-4">
                      <div class="form-group">
                        <label>Where did you hear of SuperSteph?</label>
                      </div>
                    </div>

                    <div class="col-md-5">
                      <div class="form-group">
                        <input type="text"  name="duration" class="form-control" placeholder="" ng-model="get.hear_supersteph">
                      </div>
                    </div>
                  </div>

                  <div class="col-md-12">
                    <div class="col-md-4">
                      <div class="form-group">
                        <label>How many Party Bags required? ($3.50 each- available only in Brisbane branch only)</label>
                      </div>
                    </div>

                    <div class="col-md-5">
                      <div class="form-group">
                        <input type="text" class="form-control" placeholder="" ng-model="get.party_bags">
                      </div>
                    </div>
                  </div>

                  <div class="col-md-12">
                    <div class="col-md-4">
                      <div class="form-group">
                        <label>Address of party</label>
                      </div>
                    </div>

                    <div class="col-md-5">
                      <div class="form-group">
                        <input type="text" class="form-control" placeholder="" ng-model="get.party_address">
                      </div>
                    </div>
                  </div>

                  <div class="col-md-12">
                    <div class="col-md-4">
                      <div class="form-group">
                        <label>Home address (if same as party, please put "as above")</label>
                      </div>
                    </div>

                    <div class="col-md-5">
                      <div class="form-group">
                        <input type="text" class="form-control" placeholder="" ng-model="get.home_address">
                      </div>
                    </div>
                  </div>

                  <div class="col-md-12">
                    <div class="col-md-4">
                      <div class="form-group">
                        <label>Parking facilities (any paid parking is to be paid by the client)</label>
                      </div>
                    </div>

                    <div class="col-md-5">
                      <div class="form-group">
                        <input type="text" class="form-control" placeholder="" ng-model="get.parking_facility">
                      </div>
                    </div>
                  </div>

                  <div class="col-md-12">
                    <div class="col-md-4">
                      <div class="form-group">
                        <label>Is the party inside or outside?</label>
                      </div>
                    </div>

                    <div class="col-md-5">
                      <div class="form-group">
                        <input type="text" class="form-control" placeholder="" ng-model="get.party_in_out">
                      </div>
                    </div>
                  </div>

                  <div class="col-md-12">
                    <div class="col-md-4">
                      <div class="form-group">
                        <label>Rain contingency plans (A MUST for outside parties)</label>
                      </div>
                    </div>

                    <div class="col-md-5">
                      <div class="form-group">
                        <input type="text" class="form-control" placeholder="" ng-model="get.rain_plan">
                      </div>
                    </div>
                  </div>

                  <div class="col-md-12">
                    <div class="col-md-4">
                      <div class="form-group">
                        <label>2nd Mobile number</label>
                      </div>
                    </div>

                    <div class="col-md-5">
                      <div class="form-group">
                        <input type="text" class="form-control" placeholder="" ng-model="get.second_mobile">
                      </div>
                    </div>
                  </div>

                  <div class="col-md-12">
                    <div class="col-md-4">
                      <div class="form-group">
                        <label>Name of child</label>
                      </div>
                    </div>

                    <div class="col-md-5">
                      <div class="form-group">
                        <input type="text" class="form-control" placeholder="" ng-model="get.child_name">
                      </div>
                    </div>
                  </div>

                  <div class="col-md-12">
                    <div class="col-md-4">
                      <div class="form-group">
                        <label>Age on birthday</label>
                      </div>
                    </div>

                    <div class="col-md-5">
                      <div class="form-group">
                        <input type="text" class="form-control" placeholder="" ng-model="get.age">
                      </div>
                    </div>
                  </div>

                  <div class="col-md-12">
                    <div class="col-md-4">
                      <div class="form-group">
                        <label>Date of birth</label>
                      </div>
                    </div>

                    <div class="col-md-5">
                      <div class="form-group">
                        <input type="text" class="form-control" placeholder="YYYY-MM-dd" ng-model="get.dob">
                      </div>
                    </div>
                  </div>

                  <div class="col-md-12">
                    <div class="col-md-4">
                      <div class="form-group">
                        <label>Main age of children at the party</label>
                      </div>
                    </div>

                    <div class="col-md-5">
                      <div class="form-group">
                        <input type="text" class="form-control" placeholder="" ng-model="get.children_party">
                      </div>
                    </div>
                  </div>

                  <div class="col-md-12">
                    <div class="col-md-4">
                      <div class="form-group">
                        <label>Boys, girls, or mixed party?</label>
                      </div>
                    </div>

                    <div class="col-md-5">
                      <div class="form-group">
                        <input type="text" class="form-control" placeholder="" ng-model="get.gender_party">
                      </div>
                    </div>
                  </div>

                  <div class="col-md-12">
                    <div class="col-md-4">
                      <div class="form-group">
                        <label>Number of children at the party. NOTE: Some party packages have a maximum number of children. Please contact us if you have more than the maximum</label>
                      </div>
                    </div>

                    <div class="col-md-5">
                      <div class="form-group">
                        <input type="text" class="form-control" placeholder="" ng-model="get.children_count">
                      </div>
                    </div>
                  </div>

                  <div class="col-md-12">
                    <div class="col-md-4">
                      <div class="form-group">
                        <label>Has your child seen my show before? If yes, where?</label>
                      </div>
                    </div>

                    <div class="col-md-5">
                      <div class="form-group">
                        <input type="text" class="form-control" placeholder="" ng-model="get.party_seen">
                      </div>
                    </div>
                  </div>

                  <div class="col-md-12">
                    <div class="col-md-4">
                      <div class="form-group">
                        <label>Enter your FULL NAME to show you have read and agree to the Terms and Conditions</label>
                      </div>
                    </div>

                    <div class="col-md-5">
                      <div class="form-group">
                        <input type="text" class="form-control" placeholder="" ng-model="get.show_fullname">
                      </div>
                    </div>
                  </div>

                  <div class="col-md-12">
                    <div class="col-md-4">
                      <div class="form-group">
                       
                      </div>
                    </div>
 <!-- class="form-control" placeholder=""  -->
                    <div class="col-md-5">
                      <div class="form-group">
                        <a href="<?php echo base_url() ?>welcome/bookingRequest/?{{parameters.page}}">
                          <button type="button" class="btn btn-warning">Back</button>
                        </a>

                        <button type="submit" class="btn btn-success" ng-click="editBookingRequest()">Save</button>

                        

                        <!-- <div style="margin-bottom: 5px;">
                          <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#editShowAmountModal" ng-click="editShowID(list.id, list.event_amount)">Add/Edit Amount</button>
                        </div>
                        <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#paidAmountModal" ng-click="PaidID(list.id, list.event_amount, list.remain_amount)">Pay Amount</button>
                      </div> -->
                    </div>
                  </div>

                </div>

              </form>
            </div>   
          </div>

        </div>
        
        <div class="col-md-6">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <div class="box-body">
                        <div class="table-responsive">  
                            <table class="table table-striped table-bordered table-hover">
                                <thead style="background: #009688;">
                                  <tr>
                                    <!-- <th>S.No</th> -->
                                    <th>Performer Image</th>
                                    <th>Performer Name</th>
                                    <th>Performer Mobile</th>
                                    <th> Role</th>
                                    <th>Start Time</th>
                                    <th>Duration</th>
                                    
                                    
                                  </tr>
                                </thead>
                                <tbody>
                                  <tr ng-repeat="list in listing1">
                                    <!-- <td>{{$index+1}}   {{list.id}}  </td> -->
                                   <!--  <form action="<?php echo base_url() ?>welcome/edittimeperformer" method="post" >
                                   -->  <td>
                                      <img ng-src="{{list.profile_image}}" style="height: 60px; width: 60px;">
                                    </td>

                                    <td>{{list.name}}</td>
                                    <td>{{list.mobile}}</td>
                                    <td>{{list.role}}</td>

                                    <!-- <td>{{list.assign_date}}</td>  -->
                                    <!--  <td>{{list.date}}</td> -->
                                    
                                    
                                   
                        <!--  <td><!-- <input type="text" name="time" value="{{list.start_time}}" >

<div class="form-group"><input ps-input-time sy-timepicker-popup="HH:mm" class="form-control" ng-model="get.show_time" show-meridian="false" is-open="opened1" /> -->
                       <!--  <div uib-timepicker ng-model="get.show_time" hour-step="hstep" minute-step="mstep" is-open="opened[$index]" show-meridian="ismeridian" ng-change="changeBooking(list.booking_status, list.id)"></div> -->
                      <!--     <div uib-timepicker ng-model="list.start_time" hour-step="hstep" minute-step="mstep" show-meridian="ismeridian"></div>
                 
                      </div>
                                    </td> -->
                                    <td>{{list.start_time}}</td>
                                   <!--  <td>{{list.end_time}}</td> -->
                                    <td>{{list.duration}} 
                     <!--  <select ng-model="list.duration">
                        <option ng-repeat="x in durationListing" value="{{x.value}}">{{x.label}}</option>
                      </select> -->
                                    </td>
                                  
                              <!--        <td>
                         <input type="button" class="btn btn-sm btn-warning" Value="Edit" ng-click="editBookAssignPerformer(list[$index])">  -->
                       
                          <!--  <input type="button" class="btn btn-sm btn-warning" Value="Edit" ng-click="editAssignPerformer(list[$index])"> --> 
                            <!-- <a href="<?php echo base_url() ?>welcome/cancelPerformer/?id={{list.id}}&parameter={{parameters.page}}&booking={{get.id}}" ><span class="btn btn-sm btn-info" ng-click="cancelPerformer(list.id);">Remove</span></a> 
                        </td> -->
                                    <!-- </form> -->
                                  </tr>
                                </tbody>
                            </table>
                          <!--   <a href="<?php echo base_url() ?>welcome/bookPerformer/?{{id}}/?{{parameters.page}}">
                          <button type="button" class="btn btn-sm btn-warning"> Assign Performers</button>
                        </a> -->

                     <a href="<?php echo base_url() ?>welcome/editBookAssignPerformer/?{{id}}/?{{parameters.page}}">
                          <button type="button" class="btn btn-sm btn-warning"> Edit Performers</button>
                        </a> 
                        <!--  <button type="submit" class="btn btn-success" ng-click="editBookAssignPerformer()">Save</button>
                       -->  </div>
                    </div>
                </div>
            </div>
            
           
                
   


      <!-- view Booking Modal -->
      <div class="modal fade" id="myModal" role="dialog">
        <div class="modal-dialog">
        
            <!-- Modal content-->
            <div class="modal-content">
              <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal">&times;</button>
                  <h4 class="modal-title">Cancel Booking</h4>
              </div>
              <div class="modal-body">
                <div class="row">
                  <div class="col-sm-2">
                    <label>Reason</label>
                  </div>
                  <div class="col-sm-10">
                    <textarea rows="4" cols="50" placeholder="Enter Reason" ng-model="get.reason"></textarea>
                  </div>
                </div>
              </div>
              <div class="modal-footer">
                  <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                  <button type="button" class="btn btn-primary" ng-click="cancelBooking()">Save</button>
              </div>
            </div>
          
        </div>
      </div>


      <!-- add Amount Modal -->
      <div class="modal fade" id="addAmountModal" role="dialog">
        <div class="modal-dialog">
        
            <!-- Modal content-->
            <div class="modal-content">
              <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal">&times;</button>
                  <h4 class="modal-title">Add Amount</h4>
              </div>
              <div class="modal-body">
                <div class="row">
                  <div class="col-sm-3">
                    <label>Amount</label>
                  </div>
                  <div class="col-sm-7">
                    <input type="text" name="amount" class="form-control" placeholder="Enter Amount" ng-model="get.event_amount" required>
                  </div>
                </div>
              </div>
              <div class="modal-footer">
                  <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                  <button type="button" class="btn btn-primary" ng-click="addAmount()">Save</button>
              </div>
            </div>
          
        </div>
      </div>

      <!-- paid Amount Modal -->
      <div class="modal fade" id="paidAmountModal" role="dialog">
        <div class="modal-dialog">
        
            <!-- Modal content-->
            <div class="modal-content">
              <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal">&times;</button>
                  <h4 class="modal-title">Paid Amount</h4>
              </div>
              <div class="modal-body">
                <div class="row">
                  <div class="col-sm-3">
                    <label>Event Amount</label>
                  </div>9023100160
                  <div class="col-sm-7">
                    <input type="text" name="event_amount" class="form-control" placeholder="Event Amount" ng-model="event_amount" readonly="">
                  </div>
                </div>
                <br>
                <div class="row">
                  <div class="col-sm-3">
                    <label>Remain Amount</label>
                  </div>
                  <div class="col-sm-7">
                    <input type="text" name="remain_amount" class="form-control" placeholder="Event Amount" ng-model="remain_amount" readonly="">
                  </div>
                </div>
                <br>
                <div class="row">
                  <div class="col-sm-3">
                    <label>Paid Amount</label>
                  </div>
                  <div class="col-sm-7">
                    <input type="text" name="paid_amount" class="form-control" placeholder="Paid Amount" ng-model="get.paid_amount" required>
                  </div>
                </div>
              </div>
              <div class="modal-footer">
                  <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                  <button type="button" class="btn btn-primary" ng-click="paidAmount()">Save</button>
              </div>
            </div>
          
        </div>
      </div>

      <!-- edit Show Amount Modal -->
      <div class="modal fade" id="editShowAmountModal" role="dialog" style="">
        <div class="modal-dialog">
        
            <!-- Modal content-->
            <div class="modal-content">
              <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal">&times;</button>
                  <h4 class="modal-title">Show Amount</h4>
              </div>
              <div class="modal-body">
                <div class="row">
                  <div class="col-sm-3">
                    <label>Event Amount</label>
                  </div>
                  <div class="col-sm-7">
                    <input type="text" name="edit_event_amount" class="form-control" placeholder="Event Amount" ng-model="get.edit_event_amount" required>
                  </div>
                </div>
              </div>
              <div class="modal-footer">
                  <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                  <button type="button" class="btn btn-primary" ng-click="editShowAmount()">Save</button>
                  <!-- <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#paidAmountModal" ng-click="PaidID(list.id, list.event_amount, list.remain_amount)">Pay Amount</button> -->
              </div>
            </div>
          
        </div>
      </div>

  </div>

</div>
     </div>
      </div>  
    </section>
  </div>
</div>
