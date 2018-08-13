<div class="right_col" role="main" ng-app="myApp" ng-controller="editBookAssignPerformerController" ng-cloak>

  <script src="<?php echo base_url() ?>jsController/admin_editBookAssignPerformer.js"></script>

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
  <style>
.glyphicon {
    display:none !important;  
  }
      </style>
  <div class="content-wrapper">
    
    <section class="content-header">
      <h1>Edit Booking : Performers</h1>
      <ol class="breadcrumb">
        <li><a href=""><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="">Edit Booking</a></li>
        <li class="active">Edit Booking : Performers</li>
      </ol>
    </section>

    <section class="content">
      <div class="row">
        <div class="col-md-12">

          <div class="box box-primary">
            <div class="box-header with-border">
              
              <form name="editStep">

                <div class="box-body"> 

                  <!-- <div class="row" ng-repeat="book in listingBooking">
                    <div class="col-sm-3" style="margin-bottom: 15px;">
                      <p>Host</p>
                      <input type="text" ng-model="book.name" style="background-color: #9E9E9E;" readonly="">
                    </div>
                    <div class="col-sm-1">
                    </div>
                    <div class="col-sm-3" style="margin-bottom: 15px;">
                      <p>Start Time</p>
                      <input type="text" ng-model="book.show_time" style="background-color: #9E9E9E;" readonly="">
                    </div>
                    <div class="col-sm-1">
                    </div>
                    <div class="col-sm-2" style="margin-bottom: 15px;">
                      <p>Duration</p>
                      <input type="text" ng-model="book.duration" style="background-color: #9E9E9E;" readonly="">
                    </div>
                    <div class="col-sm-2" style="margin-bottom: 15px;">
                      <p>Remove</p>
                      
                    </div>

                    <br>
                  </div> -->

                 <!--  <br> -->
                 
                  <!-- <div class="row">
                    <div class="col-sm-3">
                      <p>Performer Name</p>
                    </div>
                    <div class="col-sm-1">
                    </div>
                    <div class="col-sm-3">
                      <p>Start Time</p>   
                    </div>
                    <div class="col-sm-1">
                    </div>
                    <div class="col-sm-2">
                      <p>Duration</p>
                    </div>
                    <div class="col-sm-2">
                      <p>Remove</p>
                    </div>
                    <br>
                  </div> -->

<div class="col-md-12">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <div class="box-body">
                        <div class="table-responsive">  
                            <table class="table table-striped table-bordered table-hover">
                                <thead style="background: #009688;">
                                <tr>
<th>Performer image</th>
<th>Performer Name</th>
<th>Performer Mobile</th>
<th>Role</th>
<th>Performer Start Time</th>
<th>Performer Duration</th>
<!-- <th>Email</th> -->
<th>Action</th>
</tr>
 </thead>
                                <tbody>
<tr ng-repeat="list in listingPerformer track by $index">
<td> <img ng-src="{{list.profile_image}}" style="height: 60px; width: 60px;"> </td>
<td>{{list.name}} </td>
<td>{{list.mobile_a}}</td>
<td> <!-- <input type="text" ng-model="list.role" > {{list.category_name}} -->     
<!--(categoryListing | filter: $select.search) -->
   <ui-select multiple data-ng-model="list.role" theme="bootstrap" required>
    <ui-select-match placeholder="Please Select Category.."   ng-cloak>{{$item.category_name}}</ui-select-match>
    <ui-select-choices repeat="type.id as type in (categoryListing | filter: $select.search)  track by type.id">
    <small>
      {{type.category_name}}
      
    </small>
    </ui-select-choices>
  </ui-select>

</td>
<td>
<input type="text" ng-model="list.start_time" name="start_time"><!-- {{list.start_time}}
<div uib-timepicker ng-model="list.start_time" hour-step="hstep" minute-step="mstep" show-meridian="ismeridian"></div> -->
                   </td>
<td><select ng-model="list.duration">
                        <option ng-repeat="x in durationListing" value="{{x.value}}">{{x.label}}</option>
                      </select></td>
<!-- <td>
<input type="checkbox" name="list.confirmemail" ng-model="list.confirmemail" ng-checked="true" > Send Email
</td> -->
<td><a href="<?php echo base_url() ?>welcome/cancelPerformer/?id={{list.assign_id}}&parameter={{parameters.page}}&booking={{list.booking_id}}" onclick="return confirm('Are you sure you would like to remove this performer ?');"><span class="btn btn-sm btn-info">Remove</span></a> 
<!--a href="<?php echo base_url() ?>welcome/editAssignPerformer/?{{list.assign_id}}/?{{parameters.page}}" ><span class="btn btn-sm btn-info">Edit</span></a--><!-- <span class="btn btn-sm btn-info" ng-click="editAssignPerformer(list)">Save</span> -->
<span class="btn btn-sm btn-info" ng-click="emailAssignPerformer(list)">Email</span>
 </td>
</tr>
</tbody>
</table> 
</div>
</div>
</div>  
</div>
</div>

                 <!--  <div class="row" ng-repeat="list in listingPerformer track by $index">
                    <div class="col-sm-3" style="margin-bottom: 15px;">
                   
                      <input type="text" ng-model="list.name" style="background-color: #9E9E9E;" readonly="">
                    </div>
                    <div class="col-sm-1">
                    </div>
                    <div class="col-sm-3">
                      <div uib-timepicker ng-model="list.start_time" hour-step="hstep" minute-step="mstep" show-meridian="ismeridian"></div>
                    </div>
                    <div class="col-sm-1">
                    </div>
                    <div class="col-sm-2">
                      <select ng-model="list.duration">
                        <option ng-repeat="x in durationListing" value="{{x.value}}">{{x.label}}</option>
                      </select>
                    </div>
                    <div class="col-sm-2">
                      <a href="<?php echo base_url() ?>welcome/cancelPerformer/?id={{list.assign_id}}&parameter={{parameters.page}}&booking={{list.booking_id}}" ><span class="btn btn-sm btn-info">Remove</span></a>
                    </div>

                    <br>
                  </div> -->

                  <br>
                  
                  <div class="row">
                    
                    <div class="col-md-4">
                      <div class="form-group">
                        <a href="<?php echo base_url() ?>welcome/editbookingRequest/?{{id}}/?{{parameters.page}}" >
                          <button type="button" class="btn btn-warning">Back</button>
                        </a>
 
<!--  ng-disabled="isDisabled" ng-model="isDisabled" disableClick();"  id="btn1"-->
                        
                        <button type="submit" class="btn btn-success" ng-disabled="isDisabled" ng-model="isDisabled"  ng-click="editBookAssignPerformer()" >Save</button> 
                         <a href="<?php echo base_url() ?>welcome/bookPerformer/?{{id}}/?{{parameters.page}}">
                          <button type="button" class="btn btn-sm btn-warning"> Assign Performers</button>
                        </a>
                      </div>

                    </div>
                    <div class="col-md-2">
                      <div class="form-group">
                      
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