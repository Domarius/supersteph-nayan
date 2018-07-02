<div class="right_col" role="main" ng-app="myApp" ng-controller="BookingRequestController" ng-cloak>

	<script src="<?php echo base_url() ?>jsController/admin_bookingRequest.js"></script>

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
	      	<h1>Booking Request</h1>
	      	<ol class="breadcrumb">
	        	<li><a href=""><i class="fa fa-dashboard"></i> Home</a></li>
	    		<li><a href="">Booking</a></li>
	        	<li class="active">View Booking</li>
	      	</ol>
	    </section>

	    <section class="content">
	      	<div class="row">
	        	<div class="col-xs-12">
	          		
		          	<div class="box">
		          		<div class="box-header">
		          			<div class="col-sm-2 col-sm-offset-8"> 
			                	<div class="dropdown">
							    	<button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown">Upcoming Pdf Gig
							    	<span class="caret"></span></button>
							    	<ul class="dropdown-menu">
							      		<li>
							      			<a href="<?php echo base_url() ?>welcome/alldownloadPdf/1">1 Day</a>
							      		</li>
							      		<li>
							      			<a href="<?php echo base_url() ?>welcome/alldownloadPdf/3">3 Day</a>
							      		</li>
							      		<li>
							      			<a href="<?php echo base_url() ?>welcome/alldownloadPdf/5">5 Day</a>
							      		</li>
							      		<li>
							      			<a href="<?php echo base_url() ?>welcome/alldownloadPdf/10">10 Day</a>
							      		</li>
							      		<li>
							      			<a href="<?php echo base_url() ?>welcome/alldownloadPdf/0">All</a>
							      		</li>
						    		</ul>
							  	</div>
			                </div>
			                <div class="col-sm-2"> 
			                	<a href="<?php echo base_url(); ?>welcome/blockedbookingRequest" class="btn btn-primary" >Blocked Request</a>
			                </div>
		          		</div>
			            <div class="box-header">
			            	<div class="col-sm-2"> 
			                	<input type="text" class="search-filter" placeholder="Search by name" ng-model="parameters.searchtype" ng-change="searchFunction(parameters.searchtype)">
			                </div>
			                <!-- <div class="col-sm-3"> 
			                	<md-content>
		                          	<md-datepicker ng-model ="searchDate" ng-change="searchFunction(searchData, searchDate)" md-placeholder="dd/mm/yyyy"></md-datepicker>
		                        </md-content>
			                </div> -->
			                <div class="col-sm-3">  
			                	<input type="text" ng-model="parameters.start_date" datetime-picker date-format="yyyy-MM-dd HH:mm:ss" close-on-select="false" placeholder="Enter start date" size="30" ng-click="parameters.end_date='';" style="padding-bottom: 12px;" />
			                </div>

			                <div class="col-sm-3"> 
			                	<input type="text" ng-model="parameters.end_date" datetime-picker date-format="yyyy-MM-dd HH:mm:ss" close-on-select="false" placeholder="Enter end date" size="30" style="padding-bottom: 12px;"/>
			                </div> 

			                <div class="col-sm-1"> 
			                	<a class="btn btn-primary" ng-click="datefun(parameters.start_date, parameters.end_date);">Submit</a>

			                	<a class="btn btn-danger" ng-click="reset();">Reset</a>
			                </div> 
			                <div class="col-sm-2"> 
			                	<div class="dropdown">
							    	<button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown">Upcoming Gig
							    	<span class="caret"></span></button>
							    	<ul class="dropdown-menu">
							      		<li><a href="" ng-click="filterRecords('1')">1 Day</a></li>
							      		<li><a href="" ng-click="filterRecords('3')">3 Day</a></li>
							      		<li><a href="" ng-click="filterRecords('5')">5 Day</a></li>
							      		<li><a href="" ng-click="filterRecords('10')">10 Day</a></li>
						    		</ul>
							  	</div>
			                </div>
			                <div class="col-sm-1"> 
			                	<a href="<?php echo base_url(); ?>welcome/addbookingRequest" class="btn btn-primary" >Add</a>
			                </div>
			            </div>
	           
			            <div class="box-body">
			            	<div class="table-responsive">          
								<table class="table table-striped table-bordered table-hover">
									<thead style="background: #009688;">
										<tr>
											<th>S.No</th>
											<th>Name</th>
											<th>Email</th>
											<th>Mobile</th>
											<th>Event Date</th>
											<th>Show type</th>
											<th>Show Start time</th>
											<th>Duration</th>
											
											<th>Show Amount</th>
											<th>Paid Amount</th>
											<th>Remain Amount</th>
											<th>Add Amount</th>
											<th>Add Paid</th>
											<th>Booking Status</th>
											<!-- <th>Assign Status</th> -->
											<!-- <th>Booking Performer Details</th> -->
											<!-- <th>Detail Form Request</th> -->
											<!-- <th>Chasing Paperwork</th>
											<th>Looking Forward</th>
											<th>Chasing Payments</th>
											<th>Performer Feedback</th> -->
											<!-- <th>Print</th> -->
											<th>Action</th>
										</tr>
									</thead>
									<tbody>
										<tr ng-repeat="list in listing">
											<td>{{$index+1}}</td>
											<td>{{list.name}}</td>
											<td>{{list.email}}</td>
											<td>{{list.mobile_number}}</td>
											<td>{{list.event_date}}</td>
											<td>{{list.show_type}}</td>
											<td>{{list.show_time}}</td>
											<td>{{list.duration}}</td>
											
											<td>{{list.event_amount}}</td> 
											<td>{{list.paid_amount}}</td>
											<td>{{list.remain_amount}}</td>
											<td>
												<div ng-if="list.add_amount_count == '0'">
													<button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#addAmountModal" ng-click="AmountID(list.id)"">Add</button>
												</div>
												<div ng-if="list.add_amount_count == '1'">
													<button type="button" class="btn btn-sm btn-danger">Add</button>
												</div>
											</td>
											<td>
												<button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#paidAmountModal" ng-click="PaidID(list.id, list.event_amount, list.remain_amount)"">Paid</button>
											</td>
											<td>
												<div ng-if="list.booking_status == 'Pending'">
													<button type="button" class="btn btn-sm btn-primary">{{list.booking_status}}</button>
												</div>
												<div ng-if="list.booking_status == 'Accepted'">
													<button type="button" class="btn btn-sm btn-success">{{list.booking_status}}</button>
												</div>
												<div ng-if="list.booking_status == 'Ready Print'">
													<button type="button" class="btn btn-sm btn-info">{{list.booking_status}}</button>
												</div>
												<div ng-if="list.booking_status == 'Cancelled'">
													<button type="button" class="btn btn-sm btn-danger">{{list.booking_status}}</button>
												</div>
											</td>
											<!-- <td>
												<a href="<?php echo base_url() ?>welcome/bookPerformer/?{{list.id}}">
													<button type="button" class="btn btn-sm btn-warning">Assign</button>
												</a>
											</td>
											<td>
												<div ng-if="list.performer_count == '1'">
													<a href="<?php echo base_url() ?>welcome/viewperformer/?{{list.id}}">
														<button type="button" class="btn btn-sm btn-primary">Performer</button>
													</a>
												</div>
												<div ng-if="list.performer_count == '0'">
													<button type="button" class="btn btn-sm btn-danger">Performer</button>
												</div>
											</td> -->
										
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
											</td>
											<td>
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
												<!-- <div style="margin-bottom: 5px;">
													<a class="btn btn-sm btn-info" href="<?php echo base_url() ?>welcome/viewbookingRequest/?{{list.id}}">
														<i class="fa fa-eye" aria-hidden="true"></i>
													</a>
												</div> -->
												<a href="<?php echo base_url() ?>welcome/bookPerformer/?{{list.id}}">
													<button type="button" class="btn btn-sm btn-warning">Assign Performer</button>
												</a>
												<div ng-if="list.booking_status == 'Ready Print'">
													<div ng-if="list.print_count == '0'">
														<a href="<?php echo base_url() ?>welcome/downloadPdf/{{list.id}}" class="btn btn-sm btn-success">
															<i class="fa fa-file-pdf-o" aria-hidden="true"></i>Print
														</a>
													</div>
													<div ng-if="list.print_count == '1'">
														<a href="<?php echo base_url() ?>welcome/downloadPdf/{{list.id}}" class="btn btn-sm btn-warning">
															<i class="fa fa-file-pdf-o" aria-hidden="true"></i>Print
														</a>
													</div>
												</div>
												<div ng-if="list.booking_status == 'Cancelled' || list.booking_status == 'Pending' " >
													<button type="button" class="btn btn-sm btn-danger"><i class="fa fa-file-pdf-o" aria-hidden="true"></i>Print</button>
												</div>


												<div ng-if="list.assign_status == 'Unassign' && list.mail_count == '0'">
													<button type="button" class="btn btn-sm btn-danger">Detail Form request </button>
												</div>
												<div ng-if="list.assign_status == 'Unassign' && list.mail_count == '1'">
													<button type="button" class="btn btn-sm btn-danger">Detail Form request</button>
												</div>
												<div ng-if="list.assign_status == 'Assign' && list.mail_count == '0'">
													<button type="button" class="btn btn-sm btn-success" ng-click="detail(list.id)">Detail Form request</button>
												</div>
												<div ng-if="list.assign_status == 'Assign' &&  list.mail_count == '1'">
													<button type="button" class="btn btn-sm btn-danger">Detail Form request</button>
												</div>




												<div style="margin-bottom: 5px;">
													<!-- <a class="btn btn-sm btn-primary" href="<?php echo base_url() ?>welcome/editbookingRequest/?{{list.id}}/?{{parameters.page}}">
														<i class="fa fa-edit" aria-hidden="true"></i> Edit
													</a> -->
													<a class="btn btn-sm btn-primary" href="<?php echo base_url() ?>welcome/actions?id={{list.id}}&p={{parameters.page}}">
														<i class="fa fa-edit" aria-hidden="true"></i> Edit
													</a>
												</div>
												<div style="margin-bottom: 5px;">
													<a class="btn btn-sm btn-danger" data-toggle="modal" data-target="#myModal" ng-click="BookingID(list.id)">
														<i class="fa fa-trash-o" aria-hidden="true"></i> Block
													</a>
												</div>
												<div style="margin-bottom: 5px;" ng-if="list.booking_status == 'Cancelled'">
													<button type="button" class="btn btn-sm btn-info" ng-click="reactive(list.id);">Re-Active</button>
												</div>
												<div style="margin-bottom: 5px;">
													<button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#editShowAmountModal" ng-click="editShowID(list.id, list.event_amount)">Edit Amt</button>
												</div>

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


	    <!-- view Booking Modal -->
	  	<div class="modal fade" id="myModal" role="dialog">
		    <div class="modal-dialog">
		    
		      	<!-- Modal content-->
		      	<div class="modal-content">
			        <div class="modal-header">
			          	<button type="button" class="close" data-dismiss="modal">&times;</button>
			          	<h4 class="modal-title">Cancel Booking</h4>
			        </div>
			        <div class="modal-body">
			        	<div class="row">
			        		<div class="col-sm-3">
			        			<label>Reason</label>
			        		</div>
			        		<div class="col-sm-9">
			        			<textarea rows="4" cols="60" placeholder="Enter Reason" ng-model="get.reason"></textarea>
			        		</div>
			        	</div>
			        </div>
			        <div class="modal-footer">
			          	<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
			          	<button type="button" class="btn btn-primary" ng-click="cancelBooking()">Save</button>
			        </div>
		      	</div>
		      
		    </div>
	  	</div>


	  	<!-- add Amount Modal -->
	  	<div class="modal fade" id="addAmountModal" role="dialog">
		    <div class="modal-dialog">
		    
		      	<!-- Modal content-->
		      	<div class="modal-content">
			        <div class="modal-header">
			          	<button type="button" class="close" data-dismiss="modal">&times;</button>
			          	<h4 class="modal-title">Add Amount</h4>
			        </div>
			        <div class="modal-body">
			        	<div class="row">
			        		<div class="col-sm-3">
			        			<label>Amount</label>
			        		</div>
			        		<div class="col-sm-7">
			        			<input type="text" name="amount" class="form-control" placeholder="Enter Amount" ng-model="get.event_amount" required>
			        		</div>
			        	</div>
			        </div>
			        <div class="modal-footer">
			          	<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
			          	<button type="button" class="btn btn-primary" ng-click="addAmount()">Save</button>
			        </div>
		      	</div>
		      
		    </div>
	  	</div>

	  	<!-- paid Amount Modal -->
	  	<div class="modal fade" id="paidAmountModal" role="dialog">
		    <div class="modal-dialog">
		    
		      	<!-- Modal content-->
		      	<div class="modal-content">
			        <div class="modal-header">
			          	<button type="button" class="close" data-dismiss="modal">&times;</button>
			          	<h4 class="modal-title">Paid Amount</h4>
			        </div>
			        <div class="modal-body">
			        	<div class="row">
			        		<div class="col-sm-3">
			        			<label>Event Amount</label>
			        		</div>
			        		<div class="col-sm-7">
			        			<input type="text" name="event_amount" class="form-control" placeholder="Event Amount" ng-model="event_amount" readonly="">
			        		</div>
			        	</div>
			        	<br>
			        	<div class="row">
			        		<div class="col-sm-3">
			        			<label>Remain Amount</label>
			        		</div>
			        		<div class="col-sm-7">
			        			<input type="text" name="remain_amount" class="form-control" placeholder="Event Amount" ng-model="remain_amount" readonly="">
			        		</div>
			        	</div>
			        	<br>
			        	<div class="row">
			        		<div class="col-sm-3">
			        			<label>Paid Amount</label>
			        		</div>
			        		<div class="col-sm-7">
			        			<input type="text" name="paid_amount" class="form-control" placeholder="Paid Amount" ng-model="get.paid_amount" required>
			        		</div>
			        	</div>
			        </div>
			        <div class="modal-footer">
			          	<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
			          	<button type="button" class="btn btn-primary" ng-click="paidAmount()">Save</button>
			        </div>
		      	</div>
		      
		    </div>
	  	</div>

	  	<!-- edit Show Amount Modal -->
	  	<div class="modal fade" id="editShowAmountModal" role="dialog">
		    <div class="modal-dialog">
		    
		      	<!-- Modal content-->
		      	<div class="modal-content">
			        <div class="modal-header">
			          	<button type="button" class="close" data-dismiss="modal">&times;</button>
			          	<h4 class="modal-title">Show Amount</h4>
			        </div>
			        <div class="modal-body">
			        	<div class="row">
			        		<div class="col-sm-3">
			        			<label>Event Amount</label>
			        		</div>
			        		<div class="col-sm-7">
			        			<input type="text" name="edit_event_amount" class="form-control" placeholder="Event Amount" ng-model="get.edit_event_amount" required>
			        		</div>
			        	</div>
			        </div>
			        <div class="modal-footer">
			          	<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
			          	<button type="button" class="btn btn-primary" ng-click="editShowAmount()">Save</button>
			        </div>
		      	</div>
		      
		    </div>
	  	</div>

	</div>

</div>