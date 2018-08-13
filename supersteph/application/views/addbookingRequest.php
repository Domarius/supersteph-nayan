<div class="right_col" role="main" ng-app="myApp" ng-controller="addBookingRequestController" ng-cloak>

  <script src="<?php echo base_url() ?>jsController/admin_addbookingRequest.js"></script>

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
      <h1>Add Booking Request</h1>
      <ol class="breadcrumb">
        <li><a href=""><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="">Booking</a></li>
        <li class="active">Add Booking</li>
      </ol>
    </section>

    <section class="content">
      <div class="row">
        <div class="col-md-12">

          <div class="box box-primary">
            <div class="box-header with-border">
              
              <form name="editStep">

                <div class="box-body"> 

                  <div class="col-md-12">
                    <div class="col-md-2">
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
                    <div class="col-md-2">
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
                    <div class="col-md-2">
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
                    <div class="col-md-2">
                      <div class="form-group">
                        <label>Suburb</label>
                      </div>
                    </div>

                    <div class="col-md-5">
                      <div class="form-group">
                        <input type="text"  name="party_address" class="form-control" placeholder="Party Address" ng-model="get.party_address" required>
                      </div>
                    </div>
                  </div>

                  <div class="col-md-12">
                    <div class="col-md-2">
                      <div class="form-group">
                        <label>Event Date</label>
                      </div>
                    </div>

                    <div class="col-md-5">
                      <div class="form-group">
                        <md-content>
                          <md-datepicker ng-model = "get.event_date"  md-placeholder="mm/dd/yyyy"></md-datepicker required>
                        </md-content>
                      </div>
                    </div>
                  </div>

                   <div class="col-md-12">
                    <div class="col-md-2">
                      <div class="form-group">
                        <label>Role</label>
                      </div>
                    </div>

                    <div class="col-md-5">
                      <div class="form-group"> 
                        <ui-select multiple data-ng-model="get.show_type" theme="bootstrap" required>
                          <ui-select-match placeholder="Please Select Category.." ng-cloak>{{$item.category_name}}</ui-select-match>
                          <ui-select-choices repeat="type.id as type in categoryListing |   propsFilter: {category_name: $select.search}">
                          <small>
                            {{type.category_name}}
                          </small>
                          </ui-select-choices>
                        </ui-select>
                      </div>
                    </div>
                  </div> 
                  <div class="col-md-12">
                    <div class="col-md-2">
                      <div class="form-group">
                        <label>Event Fee</label>
                      </div>
                    </div>

                <div class="col-md-5">
                      <div class="form-group">
                        <input type="text"  name="event_amount" class="form-control" placeholder="Event Fee" ng-model="get.event_amount" required>
                      </div>
                    </div>
                  </div>
                  <!--  <div class="col-md-12">
                    <div class="col-md-2">
                      <div class="form-group">
                        <label>Show Type</label>
                      </div>
                    </div>

                <div class="col-md-5">
                      <div class="form-group">
                        <input type="text"  name="show_type" class="form-control" placeholder="Event Fee" ng-model="get.show_type" required>
                      </div>
                    </div>
                  </div>
                   -->
                  <div class="col-md-12">
                    <div class="col-md-2">
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
                    <div class="col-md-2">
                      <div class="form-group">
                        <label>Show time</label>
                      </div>
                    </div>

                    <div class="col-md-5">
                      <div class="form-group">
                        <!-- <input ps-input-time sy-timepicker-popup="HH:mm" class="form-control" ng-model="get.show_time" show-meridian="false" is-open="opened1" /> -->
                        <input  type="text" name="show_time" ng-model="get.show_time" class="form-control" placeholder="Show Time">
                       <!--  <div uib-timepicker ng-model="get.show_time" hour-step="hstep" minute-step="mstep" show-meridian="ismeridian"></div>
                   -->    </div>
                    </div>
                  </div>

                  <div class="col-md-12">
                    <div class="col-md-2">
                      <div class="form-group">
                        <label>Duration</label>
                      </div>
                    </div>

                    <div class="col-md-5">
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
                    <div class="col-md-2">
                      <div class="form-group">
                        <label>Notes</label>
                       </div>
                    </div>

                    <div class="col-md-5">
                      <div class="form-group">
                      <textarea  name="notes" class="form-control" placeholder="Admin Notes" ng-model="get.notes" required> </textarea>
                       </div>
                    </div>
                  </div>





   <!--     <div class="col-md-12">
                    <div class="col-md-4">
                      <div class="form-group">
                        <label>Notes</label>
                      </div>
                    </div>

                    <div class="col-md-5">
                      <div class="form-group">
                        <textarea  name="notes" class="form-control" placeholder="Admin Notes" ng-model="get.notes" required> </textarea>
                      </div>
                    </div>
                  </div>
 -->
                  <div class="col-md-12">
                    <div class="col-md-2">
                      <div class="form-group">
                        
                      </div>
                    </div>

                    <div class="col-md-5">
                      <div class="form-group">
                        <a href="<?php echo base_url() ?>welcome/bookingRequest">
                          <button type="button" class="btn btn-warning">Cancel</button>
                        </a>

                        <button type="submit" class="btn btn-success" ng-disabled="editStep.$invalid" ng-click="addBookingRequest()">Save</button>
                      </div>
                    </div>
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