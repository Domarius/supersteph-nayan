<div class="right_col" role="main" ng-app="myApp" ng-controller="AddPerformerController" ng-cloak>

  <script src="<?php echo base_url() ?>jsController/admin_addperformer.js"></script>

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
      <h1>Add Performer</h1>
      <ol class="breadcrumb">
        <li><a href=""><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="">Performer</a></li>
        <li class="active">Add Performer</li>
      </ol>
    </section>

    <section class="content">
      <div class="row">
        <div class="col-md-12">

          <div class="box box-primary">
            <div class="box-header with-border">
              
                <form name="addStep">

                  <div class="box-body"> 

                    <div class="col-md-12">
                      <div class="col-md-2">
                        <div class="form-group">
                          <label>Name <font color="red">*</font></label>
                        </div>
                      </div>

                      <div class="col-md-5">
                        <div class="form-group">
                          <input type="text"  name="name" class="form-control" placeholder="Enter name" ng-model="get.name" required>
                        </div>
                      </div>
                    </div>

                    <div class="col-md-12">
                      <div class="col-md-2">
                        <div class="form-group">
                          <label>Category Name</label>
                        </div>
                      </div>

                      <div class="col-md-5">
                        <div class="form-group">
                          <!-- <input type="text"  name="category_name" class="form-control" placeholder="Role" ng-model="get.category_name" required> -->
                    
                         <ui-select multiple data-ng-model="get.category_name" theme="bootstrap" required>
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
                          <label>Primary Email <font color="red">*</font></label>
                        </div>
                      </div>

                      <div class="col-md-5">
                        <div class="form-group">
                          <input type="text" name="primary_email" class="form-control" placeholder="Enter primary email" ng-model="get.primary_email" ng-pattern="/^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/" required>
                          <span style="color:Red" ng-show="addStep.primary_email.$dirty&&addStep.primary_email.$error.pattern">Please Enter Valid Email</span>
                        </div>
                      </div>
                    </div>

                    <div class="col-md-12">
                      <div class="col-md-2">
                        <div class="form-group">
                          <label>Secondary Email</label>
                        </div>
                      </div>

                      <div class="col-md-5">
                        <div class="form-group">
                          <input type="text" name="secondary_email" class="form-control" placeholder="Enter secondary email" ng-model="get.secondary_email" >
                          <!-- <span style="color:Red" ng-show="addStep.secondary_email.$dirty&&addStep.secondary_email.$error.pattern">Please Enter Valid Email</span> ng-pattern="/^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/"-->
                        </div>
                      </div>
                    </div>


                    <div class="col-md-12">
                      <div class="col-md-2">
                        <div class="form-group">
                          <label>Primary Mobile <font color="red">*</font></label>
                        </div>
                      </div>

                      <div class="col-md-5">
                        <div class="form-group">
                          <input type="text" name="primary_mobile" class="form-control" placeholder="Enter primary mobile" ng-model="get.primary_mobile">
                          <!-- <span style="color:Red" ng-show="addStep.primary_mobile.$dirty&&addStep.primary_mobile.$error.pattern">Only Numbers Allowed, Maximum 10 Characters</span>  ng-pattern="/^[0-9]{10,10}$/"  -->
                        </div>
                      </div>
                    </div>

                    <div class="col-md-12">
                      <div class="col-md-2">
                        <div class="form-group">
                          <label>Secondary Mobile</label>
                        </div>
                      </div>

                      <div class="col-md-5">
                        <div class="form-group">
                          <input type="text" name="secondary_mobile" class="form-control" placeholder="Enter secondary mobile" ng-model="get.secondary_mobile" >
                         <!--  <span style="color:Red" ng-show="addStep.secondary_mobile.$dirty&&addStep.secondary_mobile.$error.pattern">Only Numbers Allowed, Maximum 10 Characters</span>   ng-pattern="/^[0-9]{10,10}$/">-->
                        </div>
                      </div>
                    </div>

                    <div class="col-md-12">
                      <div class="col-md-2">
                        <div class="form-group">
                          <label>Description</label>
                        </div>
                      </div>

                      <div class="col-md-5">
                        <div class="form-group">
                          <input type="text" name="description" class="form-control" placeholder="Enter description" ng-model="get.description" >
                        </div>
                      </div>
                    </div>

                    <div class="col-md-12">
                      <div class="col-md-2">
                        <div class="form-group">
                          <label>Image</label>
                        </div>
                      </div>

                      <div class="col-md-5">
                        <div class="form-group">
                          <a href="" ngf-select ng-model="get.performer_image" name="file" ngf-pattern="'image/*'" ngf-accept="'image/*'" >
              							<img ngf-thumbnail="get.performer_image || '<?php echo base_url() ?>image/dummy.png'" style="height: 200px; width: 375px;">
              						</a>
                        </div>
                      </div>
                    </div>



                    <div class="col-md-12">
                      <div class="col-md-2">
                        <div class="form-group">
                          
                        </div>
                      </div>

                      <div class="col-md-4">
                        <div class="form-group">
                          <a href="<?php echo base_url() ?>welcome/performer">
                            <button type="button" class="btn btn-warning">Cancel</button>
                          </a><!--  -->
                          <button type="submit" class="btn btn-success" ng-disabled="addStep.$invalid" ng-click="addPerformer()">Save</button>
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