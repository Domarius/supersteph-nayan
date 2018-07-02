<div class="right_col" role="main" ng-app="myApp" ng-controller="emailTemplateController" ng-cloak>

	<script src="<?php echo base_url() ?>jsController/admin_emailTemplate.js"></script>

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
	      	<h1>Email Template</h1>
	      	<ol class="breadcrumb">
	        	<li><a href=""><i class="fa fa-dashboard"></i> Home</a></li>
	    		<li><a href="">Template</a></li>
	        	<li class="active">View Template</li>
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
			                <!--div class="col-sm-1 col-sm-offset-5"> 
			                	<a href="<?php echo base_url(); ?>welcome/addemailTemplate" class="btn btn-primary" >Add</a>
			                </div-->
			            </div>
	           
			            <div class="box-body">
			            	<div class="table-responsive">          
								<table class="table table-striped table-bordered table-hover">
									<thead style="background: #009688;">
										<tr>
											<!-- <th>S.No</th> -->
											<th>Email from</th>
											<th>Template Name</th>
											<th>Subject Name</th>
											<th>Template Html</th>
											<th>Action</th>
										</tr>
									</thead>
									<tbody>
										<tr ng-repeat="list in listing">
											<!-- <td>{{$index+1}}</td> -->
											<td>{{list.email_from}}</td>
											<td>{{list.email_template_name}}</td>
											<td>{{list.email_template_subject}}</td>
											<td>{{list.email_template_html}}</td>
											<td>
												<a href="<?php echo base_url() ?>welcome/editemailTemplate/?{{list.id}}">
													<button type="button" class="btn btn-sm btn-success"><i class="fa fa-edit" aria-hidden="true"></i></button>
												</a>
												&nbsp;&nbsp;
												<button type="button" class="btn btn-sm btn-danger" data-toggle="modal" data-target="#myModal" ng-click="templateData(list.id);">
													<i class="fa fa-eye" aria-hidden="true"></i>
												</button>
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

	<!-- Modal -->
	<div class="modal fade" id="myModal" role="dialog">
		<div class="modal-dialog">
		
		  <!-- Modal content-->
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h4 class="modal-title">Email Template</h4>
				</div>
				<div class="modal-body">
					{{view_template.email_template_html}}
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-warning" data-dismiss="modal" ng-click="cancel()">Close</button>
				</div>
			</div>
		  
		</div>
	</div>

</div>