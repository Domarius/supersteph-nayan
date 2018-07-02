<div class="right_col" role="main" ng-app="myApp" ng-controller="addCategoryController" ng-cloak>

  <script src="<?php echo base_url() ?>jsController/admin_addcategory.js"></script>

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
      <h1>Edit Category</h1>
      <ol class="breadcrumb">
        <li><a href=""><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="">Category</a></li>
        <li class="active">Add Category</li>
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
                          <label>Category Name</label>
                        </div>
                      </div>

                      <div class="col-md-4">
                        <div class="form-group">
                          <input type="text"  name="category_name" class="form-control" placeholder="Category Name" ng-model="get.category_name" required>
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
                          <a href="<?php echo base_url() ?>welcome/category">
                            <button type="button" class="btn btn-warning">Cancel</button>
                          </a>

                          <button type="submit" class="btn btn-success" ng-disabled="editStep.$invalid" ng-click="addCategory()">Save</button>
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