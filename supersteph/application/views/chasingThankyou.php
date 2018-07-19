<div class="right_col" role="main" ng-app="myApp" ng-controller="ChasingPaperworkController" ng-cloak>

  	<script src="<?php echo base_url();  ?>jsController/admin_chasingThankyou.js"></script>

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
          	<h1>Thank you Email</h1>
          	<ol class="breadcrumb">
            	<li><a href=""><i class="fa fa-dashboard"></i> Home</a></li>
          		<li><a href="">Chasing Paperwork</a></li>
            	<li class="active">View Paperwork</li>
          	</ol>
      	</section>

      	<section class="content">
          	<div class="row">
            	<div class="col-xs-12">
            
	                <div class="box">
	                	<div class="box-header">
	                    	<div class="col-sm-11">
	                    		<h3>Compose Email</h3>
	                    	</div>
	                    	<!-- <div class="col-sm-2"> 
			                	<div class="dropdown">
							    	<button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown">Upcoming Gig
							    	<span class="caret"></span></button>
							    	<ul class="dropdown-menu">
							      		<li><a href="" ng-click="filterRecords('1')">1 Day</a></li>
							      		<li><a href="" ng-click="filterRecords('3')">3 Day</a></li>
							      		<li><a href="" ng-click="filterRecords('7')">7 Day</a></li>
							      		<li><a href="" ng-click="filterRecords('10')">10 Day</a></li>
						    		</ul>
							  	</div>
			                </div> -->
	                    	<div class="col-sm-4"> 
	                    	<a href="<?php echo base_url() ?>welcome/editemailTemplate/?24" class="btn btn-success"> Edit Template </a>
	                  <!--  ng-if="delete_count > 0" -->
	                      		<button type="button" class="btn btn-primary"  ng-disabled="addStep.$invalid" ng-click="assignEmail();">Send</button>
	                      		<button type="button" class="btn btn-primary"  ng-disabled="addStep.$invalid"  ng-click="ignoreEmail();">Ignore</button>
	                      		
	                    	</div>
	                  	</div>
	                  	<div class="box-body">
                  			<div class="row">
                  				<div class="col-sm-1">
                  					<label>From:</label>
              					</div>
	                            <div class="col-sm-11">
	                            	<input type="text" class="form-control" ng-model="get.email_from">
	                            </div>
	                        </div>
	                       	<br>
	                        <div class="row">
                  				<div class="col-sm-1">
                  					<label>Subject:</label>
              					</div>
	                            <div class="col-sm-11">
	                            	<input type="text" class="form-control" ng-model="get.email_template_subject">
	                            </div>
	                        </div>
	                        <br>
	                        <div class="row">
                  				<div class="col-sm-1">
                  					<label>Text:</label>
              					</div>
	                            <div class="col-sm-11">
	                            	<textarea rows="15" class="form-control" placeholder="Enter Message" ng-model="get.email_template_html"></textarea>
	                            </div>
	                        </div>
	                         
	                  	</div>

	                  	<div class="box-body">
	                    	<div class="table-responsive">          
	                      		<table class="table table-striped table-bordered table-hover">
	                        		<thead style="background: #009688;">
	                          			<tr>
				                            <th>
				                              <input type="checkbox" ng-click="selectCheck('All', get.checkalltype)" data-ng-model="get.checkalltype" style="opacity: 1 !important;" /></span>
				                            </th> 
				                            <!-- <th>S.No</th> -->
				                            <th>Name</th>
				                            <th>Email</th>
				                            <th>Mobile</th>
				                            <th>Show Type</th>
				                           	<th>Show Date</th>
				                           	<th>Show Time</th>
				                           	
	                          			</tr>
	                        		</thead>
	                        		<tbody>
	                          			<tr ng-repeat="list in listing">
				                            <td>
				                              <input type="checkbox" ng-click="selectCheck(list.id, false)" data-ng-checked="SelectID.indexOf(list.id) > -1" style="opacity: 1 !important;" required>
				                            </td>
				                            <!-- <td>{{$index+1}}</td> -->
				                            <td>{{list.name}}</td>
				                            <td>{{list.email}}</td>
				                            <td>{{list.mobile_number}}</td>
				                            <td>{{list.show_type}}</td>
				                            <td>{{list.event_date}}</td>
				                            <td>{{list.show_time}} - {{list.show_end_time}}</td>
				                            
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