<div class="right_col" role="main" ng-app="myApp" ng-controller="EditPerformerController" ng-cloak>

  <script src="<?php echo base_url() ?>jsController/admin_editperformer.js"></script>

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
      <h1>Edit Performer</h1>
      <ol class="breadcrumb">
        <li><a href=""><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="">Performer</a></li>
        <li class="active">Edit Performer</li>
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
                          <label>Category Name</label>
                        </div>
                      </div>

                      <div class="col-md-5">
                        <div class="form-group">
                        	
                          <ui-select multiple data-ng-model="get.category" theme="bootstrap" required>
                            <ui-select-match placeholder="Please Select Category.." ng-cloak>{{$item.category_name}}</ui-select-match>
                            <ui-select-choices repeat="type in categoryListing |   propsFilter: {category_name: $select.search}">
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
                          <label>Primary Email</label>
                        </div>
                      </div>

                      <div class="col-md-5">
                        <div class="form-group">
                          <input type="text" name="primary_email" class="form-control" placeholder="Primary Email" ng-model="get.primary_email" required>
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
                          <input type="text" name="secondary_email" class="form-control" placeholder="Primary Email" ng-model="get.secondary_email" required>
                        </div>
                      </div>
                    </div>


                    <div class="col-md-12">
                      <div class="col-md-2">
                        <div class="form-group">
                          <label>Primary Mobile</label>
                        </div>
                      </div>

                      <div class="col-md-5">
                        <div class="form-group">
                          <input type="text" name="primary_mobile" class="form-control" placeholder="Primary Mobile" ng-model="get.primary_mobile" required>
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
                          <input type="text" name="secondary_mobile" class="form-control" placeholder="Primary Mobile" ng-model="get.secondary_mobile" required>
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
                          <input type="text" name="description" class="form-control" placeholder="Primary Mobile" ng-model="get.description" required>
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
                          <a href="" ngf-select ng-model="get.performer_image" name="file" ngf-pattern="'image/*'" ngf-accept="'image/*'" required>
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
                          </a>

                          <button type="submit" class="btn btn-success" ng-disabled="addStep.$invalid" ng-click="editPerformer()">Save</button>
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