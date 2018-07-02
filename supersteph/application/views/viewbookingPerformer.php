<div class="right_col" role="main" ng-app="myApp" ng-controller="viewBookingPerformerController" ng-cloak>

	<script src="<?php echo base_url() ?>jsController/admin_viewbookingPerformer.js"></script>

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
	      	<h1>{{performer_name}}</h1>
	      	<ol class="breadcrumb">
	        	<li><a href=""><i class="fa fa-dashboard"></i> Home</a></li>
	    		<li><a href="">Performer</a></li>
	        	<li class="active">View Booking Performer</li>
	      	</ol>
	    </section>

	    <section class="content">
	      	<div class="row">
	        	<div class="col-xs-12">
	          
		          	<div class="box">
			            <div class="box-header">
			            </div>
	           
			            <div class="box-body">
			            	<div class="table-responsive">          
								<table class="table table-striped table-bordered table-hover">
									<thead style="background: #009688;">
										<tr>
											<th>S.No</th>
											<th>Booking Name</th>
											<th>Booking Date</th>
											<th>Booking Status</th>
											<th>Booking Reason</th>
										</tr>
									</thead>
									<tbody>
										<tr ng-repeat="list in listing">
											<td>{{$index+1}}</td>
											<td>{{list.name}}</td>
											<td>{{list.assign_date}}</td>
											<td>{{list.status}}</td>
											<td>{{list.reason}}</td>
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