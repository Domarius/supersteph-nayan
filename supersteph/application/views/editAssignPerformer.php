<div class="right_col" role="main" ng-app="myApp" ng-controller="editAssignPerformerController" ng-cloak>

  <script src="<?php echo base_url() ?>jsController/admin_editAssignPerformer.js"></script>

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
  </style>
  <div class="content-wrapper">
    
    <section class="content-header">
      <h1>Edit Assign Performer</h1>
      <ol class="breadcrumb">
        <li><a href=""><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="">Assign Performer</a></li>
        <li class="active">Edit Assign Performer</li>
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
                        <label>Start Time</label>
                      </div>
                    </div>

                    <div class="col-md-5">
                      <div class="form-group">
                        <div uib-timepicker ng-model="get.start_time" hour-step="hstep" minute-step="mstep" show-meridian="ismeridian"></div>
                       
                      </div>
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

                  <!-- <div class="col-md-12">
                    <div class="col-md-2">
                      <div class="form-group">
                        <label>End Time</label>
                      </div>
                    </div>

                    <div class="col-md-5">
                      <div class="form-group">
                        <div uib-timepicker ng-model="get.end_time" hour-step="hstep" minute-step="mstep" show-meridian="ismeridian"></div>
                      </div>
                    </div>
                  </div> -->

                  <div class="col-md-12">
                    <div class="col-md-2">
                      <div class="form-group">
                        
                      </div>
                    </div>

                    <div class="col-md-5">
                      <div class="form-group">
                        <a href="<?php echo base_url() ?>welcome/editbookingRequest/?{{get.booking_id}}/?{{parameters.page}}">
                          <button type="button" class="btn btn-warning">Cancel</button>
                        </a>

                        <button type="submit" class="btn btn-success" ng-click="editAssignPerformer()">Save</button>
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