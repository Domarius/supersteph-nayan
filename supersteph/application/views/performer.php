<div class="right_col" role="main" ng-app="myApp" ng-controller="PerformerController" ng-cloak>

	<script src="<?php echo base_url() ?>jsController/admin_performer.js"></script>

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
			            	<div class="col-sm-6"> 
			                	<input type="text" class="search-filter" placeholder="Search by name" ng-model="searchData" ng-change="searchFunction(searchData)">
			                </div>
			                <div class="col-sm-1 col-sm-offset-5"> 
			                	<a href="<?php echo base_url(); ?>welcome/addperformer" class="btn btn-primary" >Add</a>
			                </div>
			            </div>
	           
			            <div class="box-body">
			            	<div class="table-responsive">          
								<table class="table table-striped table-bordered table-hover">
									<thead style="background: #009688;">
										<tr>
											<!-- <th>S.No</th> -->
											<th>Name</th>
											<th>Category</th>
											<th>Primary Email</th>
											<th>Secondary Email</th>
											<th>Primary Mobile</th>
											<th>Secondary Mobile</th>
											<th>Description</th>
											<th>Image</th>
											<!--th>Booked Status</th-->
											<th>Block Status</th>
											<th>Action</th>
										</tr>
									</thead>
									<tbody>
										<tr ng-repeat="list in listing">
											<!-- <td>{{$index+1}}</td> -->
											<td>{{list.name}}</td>
											<td>{{list.category}}</td>
											<td>{{list.primary_email}}</td>
											<td>{{list.secondary_email}}</td>
											<td>{{list.primary_mobile}}</td>
											<td>{{list.secondary_mobile}}</td>
											<td>{{list.description}}</td>
											<td>
												<img ng-src="{{list.profile_image}}" alt="image" style="height: 60px; width: 60px;">
											</td>
											<!--td>
												<div ng-if="list.booked_status == '0'">
													<button type="button" class="btn btn-sm btn-success">Not Booked</button>
												</div>
												<div ng-if="list.booked_status == '1'">
													<button type="button" class="btn btn-sm btn-danger">Booked</button>
												</div>
											</td-->
											<td>
												<div ng-if="list.block_status == '0'">
													<button type="button" class="btn btn-sm btn-success" ng-click="blockStatus(list.id, '1');">Unblock</button>
												</div>
												<div ng-if="list.block_status == '1'">
													<button type="button" class="btn btn-sm btn-danger" ng-click="blockStatus(list.id, '0');">Block</button>
												</div>
											</td>
											<td>
												<a href="<?php echo base_url() ?>welcome/editperformer/?{{list.id}}">
													<button type="button" class="btn btn-sm btn-success"><i class="fa fa-edit" aria-hidden="true"></i></button>
												</a>
												&nbsp;&nbsp;
												<a href="<?php echo base_url() ?>welcome/viewbookingPerformer/?{{list.id}}">
													<button type="button" class="btn btn-sm btn-info"><i class="fa fa-eye" aria-hidden="true"></i></button>
												</a>
												&nbsp;&nbsp;
												<a href="" ng-click="deletePerformer(list.id);">
													<button type="button" class="btn btn-sm btn-danger"><i class="fa fa-trash-o" aria-hidden="true"></i></button>
												</a>
												&nbsp;&nbsp;
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