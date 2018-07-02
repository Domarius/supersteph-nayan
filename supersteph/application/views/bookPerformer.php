<div class="right_col" role="main" ng-app="myApp" ng-controller="BookPerformerController" ng-cloak>

  <script src="<?php echo base_url() ?>jsController/admin_bookPerformer.js"></script>

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
    .glyphicon {
    display:none !important;
  }
  .modal-dialog {
    width: 1170px !important;
    margin: 30px auto;
}
  </style>
  <div class="content-wrapper">
      <section class="content-header">
          <h1>Performer</h1>
          <ol class="breadcrumb">
            <li><a href=""><i class="fa fa-dashboard"></i> Home</a></li>
          <li><a href="">Performer</a></li>
            <li class="active">View Performer</li>
          </ol>
      </section>

      <section class="content">
          <div class="row">
            <div class="col-xs-12">
            
                <div class="box">
                  <div class="box-header">
                    <div class="col-sm-2"> 
                      <a href="<?php echo base_url(); ?>welcome/editbookingRequest/?{{id}}/?{{parameters.page}}" class="btn btn-primary" >Back</a>
                    </div>
                  </div>

                  <div class="box-header">
                    <div class="col-sm-5"> 
                      <input type="text" class="search-filter" placeholder="Search name" ng-model="searchData" ng-change="searchFunction(searchData)">
                    </div>
                    <div class="col-sm-5"> 
                      <ui-select data-ng-model="category_id" theme="bootstrap" ng-change="searchCategoryFunction(category_id)" required>
                        <ui-select-match placeholder="Please Select..." ng-cloak>{{$select.selected.category_name}}</ui-select-match>
                        <ui-select-choices repeat="type.id as type in categoryListing |  filter: $select.search">
                        <small>
                          {{type.category_name}}
                        </small>
                        </ui-select-choices>
                      </ui-select>
                    </div>
                    <div class="col-sm-1"> 
                      <button type="button" class="btn btn-success" ng-click="removeSearch();"><i class="fa fa-trash-o" aria-hidden="true"></i></button>
                    </div>
                    <div class="col-sm-1"> 
                      <button type="button" class="btn btn-success" ng-if="delete_count > 0" data-toggle="modal" data-target="#myModal" ng-click="assignPerformerName();">Assign</button>
                    </div>
                  </div>
             
                  <div class="box-body">
                    <div class="table-responsive">          
                      <table class="table table-striped table-bordered table-hover">
                        <thead style="background: #009688;">
                          <tr>
                            <th>
                              <input type="checkbox" ng-click="selectCheck('All', get.checkalltype)" data-ng-model="get.checkalltype" style="opacity: 1 !important;" /></span>
                            </th> 
                            <!-- <th>S.No</th> -->
                            <th>Name</th>
                            <th>Category</th>
                            <th>Primary Email</th>
                            <th>Primary Mobile</th>
                            <th>Description</th>
                            <th>Image</th>
                          </tr>
                        </thead>
                        <tbody>
                          <tr ng-repeat="list in listing">
                            <td>
                              <input type="checkbox" ng-click="selectCheck(list.id, false)" data-ng-checked="SelectID.indexOf(list.id) > -1" style="opacity: 1 !important;">
                            </td>
                            <!-- <td>{{$index+1}}</td> -->
                            <td>{{list.name}}</td>
                            <td>{{list.category}}</td>
                            <td>{{list.primary_email}}</td>
                            <td>{{list.primary_mobile}}</td>
                            <td>{{list.description}}</td>
                            <td>
                              <img ng-src="{{list.profile_image}}" alt="image" style="height: 60px; width: 60px;">
                            </td>
                          </tr>
                        </tbody>
                      </table>
                      <p ng-if="listing == '' ">No record found.</p>
                    </div>
                   <!--  <div class="row">
                      <div class="col-sm-4">
                        <small class="text-muted inline m-t-sm m-b-sm">Showing {{start}} to {{end}} of {{total}} entries</small>
                      </div>
                      
                      <div class="col-sm-4">
                      </div>

                      <div class="col-sm-4 text-right" ng-if="total > parameters.limit">
                        <form class="form-inline" role="form">
                          <button type="button" class="btn btn-default btn-rounded" data-ng-click="changePage('down')" data-ng-disabled="parameters.page < 2"><i class="fa fa-angle-left"></i> Previous</button>
                          <input type="text" class="form-control" style="width: 20%;" id="zip" placeholder="Page No" data-ng-model="parameters.page" data-ng-blur="changePage(parameters.page)">
                          <button type="button" class="btn btn-default btn-rounded" data-ng-click="changePage('up')" data-ng-disabled="parameters.page == lastpage">Next <i class="fa fa-angle-right"></i></button>
                        </form>
                      </div>
                    </div> -->
                  </div>
                </div>
            </div>
          </div>
      </section>

      <!-- view Booking Modal -->
      <div class="modal fade" id="myModal" role="dialog">
        <div class="modal-dialog">
        
            <!-- Modal content-->
            <div class="modal-content">
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Assign Performer</h4>
              </div>
              <div class="modal-body">

                <div class="row" ng-repeat="book in listingBooking">
                  <div class="col-sm-3" style="margin-bottom: 15px;">
                    <p>Host</p>
                    <input type="text" ng-model="book.name" style="background-color: #9E9E9E;" readonly="">
                  </div>
                  <div class="col-sm-3" style="margin-bottom: 15px;">
                    <p>Start Time</p>
                    <input type="text" ng-model="book.show_time" style="background-color: #9E9E9E;" readonly="">
                  </div>
                  
                  <div class="col-sm-2" style="margin-bottom: 15px;">
                    <p>Duration</p>
                    <input type="text" ng-model="book.duration" style="background-color: #9E9E9E;" readonly="">
                  </div>
                 
                  <!-- <div class="col-sm-2" style="margin-bottom: 15px;">
                    <p>Role</p>
                    <input type="text" ng-model="book.role" style="background-color: #9E9E9E;" readonly="">
                  </div>
 -->
                  <br>
                </div>

                <br>
                <br>
                <br>

                <div class="row">
                  <div class="col-sm-3">
                    <p>Performer Name</p>
                  </div>
                  <!-- <div class="col-sm-1">
                  </div> -->
                  <div class="col-sm-3">
                    <p>Start Time</p>   
                  </div>
                  <!-- <div class="col-sm-1">
                  </div> -->
                  <div class="col-sm-2">
                    <p>Duration</p>
                  </div>
                  <div class="col-sm-2">
                    <p>Fee</p>
                  </div>
                  <div class="col-sm-2">
                    <p>Role</p>
                  </div>
                  <br>
                </div>

                <div class="row" ng-repeat="list in listingPerformer track by $index">
                  <div class="col-sm-3" style="margin-bottom: 15px;">
                    <input type="text" ng-model="list.name" style="background-color: #9E9E9E;" readonly="">
                  </div>
                 <!--  <div class="col-sm-1">
                  </div> -->
                  <div class="col-sm-3">
                    <div uib-timepicker ng-model="list.start_time" hour-step="hstep" minute-step="mstep" show-meridian="ismeridian"></div>
                  </div>
               <!--    <div class="col-sm-1">
                  </div> -->
                  <div class="col-sm-2">
                    
                    <select ng-model="list.duration">
                      <option ng-repeat="x in durationListing" value="{{x.value}}">{{x.label}}</option>
                    </select>
                  </div>
                  <div class="col-sm-2" style="margin-bottom: 15px;">
                    <input type="text" ng-model="list.fee" >
                  </div>
                  <div class="col-sm-2" style="margin-bottom: 15px;">
                    <!--input type="text" ng-model="list.role" -->
                    <ui-select multiple data-ng-model="list.role" theme="bootstrap" required>
                            <ui-select-match placeholder="Please Select Category.." ng-cloak>{{$item.category_name}}</ui-select-match>
                            <ui-select-choices repeat="type in categoryListing |   propsFilter: {category_name: $select.search}">
                            <small>
                              {{type.category_name}}
                            </small>
                            </ui-select-choices>
                          </ui-select>
                  </div>
                  <br>
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" ng-click="assignPerformer()">Save</button>
              </div>
            </div>
          
        </div>
      </div>


  </div>


</div>
<style>
/*.modal-dialog {
    width: 800px;
    margin: 30px auto;
}*/
</style>