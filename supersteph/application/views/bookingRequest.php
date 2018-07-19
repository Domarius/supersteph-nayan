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
		.glyphicon {
    display:none !important;
  }
  .btn-warning {
    background-color: #248EC7 !important;
    border-color: #6593CA;
}
.btn-danger {
    background-color: #248EC7 !important;
    border-color: #d73925;
}
.btn-danger.active.focus, .btn-danger.active:focus, .btn-danger.active:hover, .btn-danger:active.focus, .btn-danger:active:focus, .btn-danger:active:hover, .open>.dropdown-toggle.btn-danger.focus, .open>.dropdown-toggle.btn-danger:focus, .open>.dropdown-toggle.btn-danger:hover {
    color: #fff;
    background-color: #6593CA !important;
    border-color: #6593CA !important;
}
    </style>

	<div class="content-wrapper">
	    <section class="content-header">
	      	<h1>Bookings</h1>
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
		          		 <div class="col-sm-1"> 
			                	<a href="<?php echo base_url(); ?>welcome/addbookingRequest" class="btn btn-primary" >Create Booking</a>
			                </div>
		          			<!-- <div class="col-sm-2 col-sm-offset-10"> 
			                	<div class="dropdown">
							    	<button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown">Upcoming Word Gig
							    	<span class="caret"></span></button>
							    	<ul class="dropdown-menu">
							      		<li>
							      			<a href="<?php echo base_url() ?>welcome/alldownloadWord/1">1 Day</a>
							      		</li>
							      		<li>
							      			<a href="<?php echo base_url() ?>welcome/alldownloadWord/3">3 Day</a>
							      		</li>
							      		<li>
							      			<a href="<?php echo base_url() ?>welcome/alldownloadWord/7">7 Day</a>
							      		</li>
							      		<li>
							      			<a href="<?php echo base_url() ?>welcome/alldownloadWord/0">All</a>
							      		</li>
						    		</ul>
							  	</div>
			                </div> -->
		          		</div>
			            <div class="box-header">
			            	<div class="col-sm-3"> 
			                	<input type="text" class="form-control" placeholder="Search " ng-model="parameters.searchtype" ng-change="searchFunction(parameters.searchtype)">
			                </div>
			                
			                <div class="col-sm-3">  
			                	<input type="text" class="form-control" ng-model="parameters.start_date" datetime-picker date-format="dd/MM/yyyy hh:mm" close-on-select="false" placeholder="Enter start date" size="30" ng-click="parameters.end_date='';" style="padding-bottom: 12px;" />
			                </div>

			                <div class="col-sm-3"> 
			                	<input type="text" class="form-control"  ng-model="parameters.end_date" datetime-picker date-format="dd/MM/yyyy hh:mm"  close-on-select="false" placeholder="Enter end date" size="30" style="padding-bottom: 12px;"/>
			                </div> 

			                <div class="col-sm-3"> 
			                	<a class="btn btn-primary" class="btn btn-submit"  ng-click="datefun(parameters.start_date, parameters.end_date);">Submit</a>

			                	<a class="btn btn-danger" class="btn btn-submit"  ng-click="reset();">Reset</a>
			                </div> 
			               <!--  <div class="col-sm-2"> search-filter
			                	<div class="dropdown">
							    	<button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown">Upcoming Gig
							    	<span class="caret"></span></button>
							    	<ul class="dropdown-menu">
							      		<li><a href="" ng-click="filterRecords('1')">1 Day</a></li>
							      		<li><a href="" ng-click="filterRecords('3')">3 Day</a></li>
							      		<li><a href="" ng-click="filterRecords('7')">7 Day</a></li>
						    		</ul>
							  	</div>
			                </div> -->
			                
			               
			            </div>
			            <div class="box-body">
	           			<div class="col-sm-2"> 
			                	<div class="dropdown">
							    	<button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown">Show Gigs 
							    	<span class="caret"></span></button>
							    	<ul class="dropdown-menu">
							      		<li><a href="" ng-click="filterRecords('1')">1 Day</a></li>
							      		<li><a href="" ng-click="filterRecords('3')">3 Day</a></li>
							      		<li><a href="" ng-click="filterRecords('7')">7 Day</a></li>
						    		</ul>
							  	</div>
			                </div>
			                </div>
			            <div class="box-body">
			            	<div class="table-responsive">          
								<table class="table table-striped table-bordered table-hover">
									<thead style="background: #009688;">
										<tr>
											<!-- <th>ID</th> -->
											<th>Name</th>
											<th>Email</th>
											<th>Mobile</th>
											<th>Show Date</th>
											<th>Show type</th>
											<th> Start </th>
											<th>Duration</th>
											
											<th>Fee</th><!-- 
											<th>Paid Amount</th>
											<th>Remain Amount</th> -->
											<!-- <th>Add Amount</th> -->
											<!-- <th>Add Paid</th> -->
											<th> Status</th>

											<th>Action</th>
										</tr>
									</thead>
									<tbody>
										<tr ng-repeat="list in listing">
											<!-- <td>{{$index+1}}</td> -->
											<td>{{list.name}}</td>
											<td>{{list.email}}</td>
											<td>{{list.mobile_number}}</td>
											<td>{{list.event_date}}</td>
											<td>{{list.show_type}}</td>
											<td>{{list.show_time}} </td>
											<td>{{list.duration}} </td>
											
											<td>Total= {{list.event_amount}} <br>Paid = {{list.paid_amount}}<br>Remain ={{list.remain_amount}}</td> 
											<!-- <td>Paid = {{list.paid_amount}}</td>
											<td>Remain ={{list.remain_amount}}</td> -->
											<!-- <td>
												<div ng-if="list.add_amount_count == '0'">
													<button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#addAmountModal" ng-click="AmountID(list.id)">Add Amount</button>
												</div>
												<div ng-if="list.add_amount_count == '1'">
													<button type="button" class="btn btn-sm btn-danger">Add</button>
												</div>
											</td> -->
											<!-- <td>
												<button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#paidAmountModal" ng-click="PaidID(list.id, list.event_amount, list.remain_amount)">Pay Amount</button>
											</td> -->
											<td>
												<!-- <div ng-if="list.booking_status == 'Pending'">
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
												</div> -->
												<select ng-model="list.booking_status" ng-change="changeBooking(list.booking_status, list.id)">
							                      	<option ng-repeat="x in statusListing" value="{{x.value}}">{{x.label}}</option>
							                    </select>
											</td>
											
											
											<td>
												<div style="margin-bottom: 5px;">
													<!-- <a class="btn btn-sm btn-success" href="<?php echo base_url() ?>welcome/actionbookingRequest/?{{list.id}}/?{{parameters.page}}">
														<i class="fa fa-edit" aria-hidden="true"></i> View Action
													</a> -->
													<a class="btn btn-sm btn-primary" href="<?php echo base_url() ?>welcome/editbookingRequest/?{{list.id}}/?{{parameters.page}}">
													<i class="fa fa-edit" aria-hidden="true"></i> Edit Booking
												</a>

												</div>

												<!-- <div style="margin-bottom: 5px;">
													<a href="<?php echo base_url() ?>welcome/bookPerformer/?{{list.id}}/?{{parameters.page}}">
														<button type="button" class="btn btn-sm btn-warning">Assign Performer List</button>
													</a>
												</div> -->

												<div style="margin-bottom: 5px;">
												<div ng-if="list.booking_status == 'Ready Print'">
													<a href="<?php echo base_url() ?>welcome/downloadWord/{{list.id}}" class="btn btn-sm btn-warning">
														<i class="fa fa-file-word-o" aria-hidden="true"></i> Print
													</a>
													</div>
													<div ng-if="list.booking_status != 'Ready Print'">
													<button class="btn btn-sm btn-danger" id="notreadytoprint" onclick="readytoprint();">
														<i class="fa fa-file-word-o" aria-hidden="true"></i> Print
													</button>
													</div>
												</div> 
												<!--div style="margin-bottom: 5px;">
													 <div ng-if="list.booking_status == 'Ready Print'">
														<div ng-if="list.print_count == '0'">
															<a href="<?php echo base_url() ?>welcome/downloadWord/{{list.id}}" class="btn btn-sm btn-success">
																<i class="fa fa-file-pdf-o" aria-hidden="true"></i>Print {{list.print_count}}
															</a> 
														</div>
														<div ng-if="list.print_count == '1'">
															<!-- <a href="<?php echo base_url() ?>welcome/downloadPdf/{{list.id}}" class="btn btn-sm btn-warning">
																<i class="fa fa-file-pdf-o" aria-hidden="true"></i>Print
															</a> -->
															<!--button type="button" class="btn btn-sm btn-danger">
															<i class="fa fa-file-pdf-o" aria-hidden="true"></i>Print {{list.print_count}}</button>
														</div>
													</div>
													<!-- <div ng-if="list.booking_status == 'Cancelled' || list.booking_status == 'Pending' " >
														<button type="button" class="btn btn-sm btn-danger">
															<i class="fa fa-file-pdf-o" aria-hidden="true"></i>
															Print
														</button>
													</div> -->
												</div>

												<div style="margin-bottom: 5px;">
													<!-- <div ng-if="list.assign_status == 'Unassign' && list.mail_count == '0'">
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
													</div> -->
													<!-- <button type="button" class="btn btn-sm btn-success" ng-click="detail(list.id)">Detail Form request</button>
												</div> -->


												<!-- <div style="margin-bottom: 5px;">
													<a class="btn btn-sm btn-danger" data-toggle="modal" data-target="#myModal" ng-click="BookingID(list.id)">
														<i class="fa fa-trash-o" aria-hidden="true"></i> Delete
													</a>
												</div>

												<div style="margin-bottom: 5px;" ng-if="list.booking_status == 'Cancelled'">
													<button type="button" class="btn btn-sm btn-info" ng-click="reactive(list.id);">Re-Active</button>
												</div>

												<div style="margin-bottom: 5px;">
													<button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#editShowAmountModal" ng-click="editShowID(list.id, list.event_amount)">Add/Edit Amount</button>
												</div>
												<button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#paidAmountModal" ng-click="PaidID(list.id, list.event_amount, list.remain_amount)">Pay Amount</button> -->
												 <!-- <div style="margin-bottom: 5px;">
													<a href="<?php echo base_url() ?>welcome/downloadWord/{{list.id}}" class="btn btn-sm btn-warning">
														<i class="fa fa-file-word-o" aria-hidden="true"></i> Print
													</a>
												</div>  -->

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
			        		<div class="col-sm-2">
			        			<label>Reason</label>
			        		</div>
			        		<div class="col-sm-10">
			        			<textarea rows="4" cols="50" placeholder="Enter Reason" ng-model="get.reason"></textarea>
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

<script> function readytoprint(){ alert('Booking is not ready to print'); } </script>
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
	  	<div class="modal fade" id="editShowAmountModal" role="dialog" style="">
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
			          	<!-- <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#paidAmountModal" ng-click="PaidID(list.id, list.event_amount, list.remain_amount)">Pay Amount</button> -->
			        </div>
		      	</div>
		      
		    </div>
	  	</div>

	</div>

</div>