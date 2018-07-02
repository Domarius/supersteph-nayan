<div class="right_col" role="main" ng-app="myApp" ng-controller="CompletedBookingRequestController" ng-cloak>

	<script src="<?php echo base_url() ?>jsController/admin_completedbookingRequest.js"></script>

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
	      	<h1>Completed Bookings</h1>
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
		          		<div class="box-header"><a href="<?php echo base_url() ?>welcome/archiveManager"><button class="btn btn-success">Back</button></a>
			                
		          			<div class="col-sm-2 col-sm-offset-10"> 
		          				<!-- <div class="dropdown">
							    	<button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown">Upcoming Pdf Gig
							    	<span class="caret"></span></button>
							    	<ul class="dropdown-menu">
							      		<li>
							      			<a href="<?php echo base_url() ?>welcome/blockalldownloadPdf/1">1 Day</a>
							      		</li>
							      		<li>
							      			<a href="<?php echo base_url() ?>welcome/blockalldownloadPdf/3">3 Day</a>
							      		</li>
							      		<li>
							      			<a href="<?php echo base_url() ?>welcome/blockalldownloadPdf/7">7 Day</a>
							      		</li>
							      		<li>
							      			<a href="<?php echo base_url() ?>welcome/blockalldownloadPdf/0">All</a>
							      		</li>
						    		</ul>
							  	</div> -->
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
			                	<input type="text" ng-model="parameters.start_date" datetime-picker date-format="dd/MM/yyyy hh:mm a" close-on-select="false" placeholder="Enter start date" size="30" ng-click="parameters.end_date='';" style="padding-bottom: 12px;" />
			                </div>

			                <div class="col-sm-3"> 
			                	<input type="text" ng-model="parameters.end_date" datetime-picker date-format="dd/MM/yyyy hh:mm a" close-on-select="false" placeholder="Enter end date" size="30" style="padding-bottom: 12px;"/>
			                </div> 

			                <div class="col-sm-3"> 
			                	<a class="btn btn-primary" ng-click="datefun(parameters.start_date, parameters.end_date);">Submit</a>

			                	<a class="btn btn-danger" ng-click="reset();">Reset</a>
			                </div> 
			                <!-- <div class="col-sm-2"> 
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
			            	<div class="table-responsive">          
								<table class="table table-striped table-bordered table-hover">
									<thead style="background: #009688;">
										<tr>
											<!-- <th>S.No</th> -->
											<th>Name</th>
											<th>Email</th>
											<th>Mobile</th>
											<th>Event Date</th>
											<th>Show type</th>
											<th>Show Start time</th>
											<th>Duration</th>
											
											<!-- <th>Show Amount</th>
											<th>Paid Amount</th>
											<th>Remain Amount</th>
											<th>Add Amount</th>
											<th>Add Paid</th> -->
											<th>Booking Status</th>

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
											<td>{{list.show_time}}</td>
											<td>{{list.duration}} hour</td>
											
											<!-- <td>{{list.event_amount}}</td> 
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
											</td> -->
											<td>
												Completed
												<!-- <div ng-if="list.booking_status == 'Cancelled'">
													<button type="button" class="btn btn-sm btn-danger">Completed</button>
												</div>  -->
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