<div class="right_col" role="main" ng-app="myApp" ng-controller="categoryController" ng-cloak>

	<script src="<?php echo base_url() ?>jsController/admin_category.js"></script>

	<!-- toaster directive --> 
        <toaster-container toaster-options="{'position-class': 'toast-top-right','time-out': 5000,'close-button':true}"></toaster-container> 
    <!-- / toaster directive -->

    <style>
    	.search-filter{
		    padding: 7px;
		    width: 400px;
		}
    </style>

	<div class="content-wrapper">
	    <section class="content-header">
	      	<h1>Role</h1>
	      	<ol class="breadcrumb">
	        	<li><a href=""><i class="fa fa-dashboard"></i> Home</a></li>
	    		<li><a href="">Category</a></li>
	        	<li class="active">View Role</li>
	      	</ol>
	    </section>

	    <section class="content">
	      	<div class="row">
	        	<div class="col-xs-12">
	          
		          	<div class="box">
			            <div class="box-header">
			            	<div class="col-sm-6"> 
			                	<input type="text" class="search-filter" placeholder="Search..." ng-model="searchData" ng-change="searchFunction(searchData)">
			                </div>
			                <div class="col-sm-1 col-sm-offset-5"> 
			                	<a href="<?php echo base_url(); ?>welcome/addcategory" class="btn btn-primary" >Add</a>
			                </div>
			            </div>
	           
			            <div class="box-body">
			            	<div class="table-responsive">          
								<table class="table table-striped table-bordered table-hover">
									<thead style="background: #009688;">
										<tr>
											<!-- <th>S.No</th> -->
											<th>Category Name</th>
											<th>Action</th>
										</tr>
									</thead>
									<tbody>
										<tr ng-repeat="list in listing">
											<!-- <td>{{$index+1}}</td> -->
											<td>{{list.category_name}}</td>
											<td>
												<a href="<?php echo base_url() ?>welcome/editcategory/?{{list.id}}">
													<button type="button" class="btn btn-sm btn-success"><i class="fa fa-edit" aria-hidden="true"></i></button>
												</a>
												&nbsp;&nbsp;
												<!--button type="button" class="btn btn-sm btn-danger" ng-click="templateData(list.id);">
													<i class="fa fa-eye" aria-hidden="true"></i>
												</button-->
												<a href=""  ng-click="deleteCategory(list.id);">
													<button type="button" class="btn btn-sm btn-danger"><i class="fa fa-trash-o" aria-hidden="true"></i></button>
												</a>
											</td>
										</tr>
									</tbody>
								</table>
								<p ng-if="listing == '' ">No record found.</p>
							</div>
							<div class="row">
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
							</div>
			            </div>
	          		</div>
	        	</div>
	      	</div>
	    </section>
	</div>

</div>