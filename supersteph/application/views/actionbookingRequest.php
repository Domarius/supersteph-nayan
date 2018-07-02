<div class="right_col" role="main" ng-app="myApp" ng-controller="ActionBookingRequestController" ng-cloak>

	<script src="<?php echo base_url() ?>jsController/admin_actionbookingRequest.js"></script>

	<!-- toaster directive --> 
        <toaster-container toaster-options="{'position-class': 'toast-top-right','time-out': 5000,'close-button':true}"></toaster-container> 
    <!-- / toaster directive -->

    <style>
    	.search-filter{
		    padding: 7px;
		    width: 165px;
		}
    </style>

	<div class="content-wrapper">
	    <section class="content-header">
	      	<h1>Action Booking Request -- {{booking_name}}</h1>
	      	<ol class="breadcrumb">
	        	<li><a href=""><i class="fa fa-dashboard"></i> Home</a></li>
	    		<li><a href="">Action Booking</a></li>
	        	<li class="active">View Action Booking</li>
	      	</ol>
	    </section>

	    <section class="content">
	      	<div class="row">
	        	<div class="col-xs-12">
	          		
		          	<div class="box">

		          		<div class="box-header">
			                <div class="col-sm-2"> 
			                	<a href="<?php echo base_url(); ?>welcome/bookingRequest/?{{parameters.page}}" class="btn btn-primary" >Back</a>
			                </div>
		          		</div>
		          		
			            <div class="box-body">

			            	<div class="table-responsive">          
								<table class="table table-striped table-bordered table-hover">
									<thead style="background: #009688;">
										<tr>
											<th>Assign Performers</th>
											<th>Booked Performers Details</th>
											<!-- <th>Chasing Paperwork</th> -->
											<!-- <th>Looking Forward</th>
											<th>Chasing Payments</th>
											<th>Performer Feedback</th> -->
											<th>View Booking</th>
											<th>Edit Booking</th>
											<th>Pdf</th>
										</tr>
									</thead>
									<tbody>
										<tr ng-repeat="list in listing">
											<td>
												<a href="<?php echo base_url() ?>welcome/bookPerformer/?{{list.id}}/?{{parameters.page}}">
													<button type="button" class="btn btn-sm btn-warning">Assign Performers</button>
												</a>
											</td>
											<td>
												<div ng-if="list.performer_count == '1'">
													<a href="<?php echo base_url() ?>welcome/viewperformer/?{{list.id}}/?{{parameters.page}}">
														<button type="button" class="btn btn-sm btn-primary">Assigned Performer List</button>
													</a>
												</div>
												<div ng-if="list.performer_count == '0'">
													<button type="button" class="btn btn-sm btn-danger">Assigned Performer List</button>
												</div>
											</td>
											<!-- <td>
												<div ng-if="list.assign_status == 'Unassign' && list.paperwork_count == '0'">
													<button type="button" class="btn btn-sm btn-danger">Paperwork Mail</button>
												</div>
												<div ng-if="list.assign_status == 'Assign' && list.paperwork_count == '0'" >
													<button type="button" class="btn btn-sm btn-success" ng-click="paperWork(list.id)">Paperwork Mail</button>
												</div>
												<div ng-if="list.assign_status == 'Assign' && list.paperwork_count == '1'">	
													<button type="button" class="btn btn-sm btn-danger">Paperwork Mail</button>
												</div>
											</td> -->
											<!-- <td>
												<div ng-if="list.looking_count == '1'">
													<button type="button" class="btn btn-sm btn-success" ng-click="lookingForward(list.id)">Looking Mail</button>
												</div>
												<div ng-if="list.looking_count == '0'">	
													<button type="button" class="btn btn-sm btn-danger">Looking Mail</button>
												</div>
											</td>

											<td>
												<div ng-if="list.remain_amount == '0'">	
													<button type="button" class="btn btn-sm btn-danger">Payments</button>
												</div>
												<div ng-if="list.remain_amount != '0'" >
													<button type="button" class="btn btn-sm btn-success" ng-click="payment(list.id)">Payments</button>
												</div>
											</td>
											<td>
												<div ng-if="list.currentDate > list.end_datetime">
													<button type="button" class="btn btn-sm btn-success" ng-click="feedback(list.id)">Feedback</button>
												</div>
												<div ng-if="list.currentDate <= list.end_datetime" >
													<button type="button" class="btn btn-sm btn-danger">Feedback</button>
												</div>
											</td> -->
											<td>
												<a class="btn btn-sm btn-info" href="<?php echo base_url() ?>welcome/viewbookingRequest/?{{list.id}}/?{{parameters.page}}">
													<i class="fa fa-eye" aria-hidden="true"></i> View Detail
												</a>
											</td>
											<td>
												<a class="btn btn-sm btn-primary" href="<?php echo base_url() ?>welcome/editbookingRequest/?{{list.id}}/?{{parameters.page}}">
													<i class="fa fa-edit" aria-hidden="true"></i> Edit
												</a>
											</td>	
											<td>
												<a class="btn btn-sm btn-warning" href="<?php echo base_url() ?>welcome/downloadPdf/{{list.id}}">
													<i class="fa fa-file-pdf-o" aria-hidden="true"></i> Pdf
												</a>
											</td>	

										</tr>
									</tbody>
								</table>
								
							</div>
							
			            </div>
	          		</div>
	        	</div>
	      	</div>
	    </section>


	    

	</div>

</div>