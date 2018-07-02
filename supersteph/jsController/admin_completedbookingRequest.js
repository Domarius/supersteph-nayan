var app = angular.module("myApp", ['toaster','ngMaterial','ngMessages','angularjs-datetime-picker']);

app.controller("CompletedBookingRequestController", function($scope, $http, toaster, $location, $window, $timeout) { 
    
	$scope.result={};
	$scope.get={};
	$scope.listing={};
	$scope.parameters={};							//initialize parameters to be send
	$scope.parameters.limit=10;						//initialize pagination default limit to 10
	$scope.parameters.page=1;						//initialize first page value to 1 for pagination
	$scope.start=$scope.end=$scope.total=0;			//initialize start to 0 for first result
	
	$scope.result.status=-1;

	var main_url = $location.absUrl();
	var url = $location.absUrl().split('/?')[0];
	var page_id = $location.absUrl().split('/?')[1];

	if(page_id){
		$scope.parameters.page=page_id;
	}
	else{
		$scope.parameters.page=1;
	}



	//Start Datepicker
	//$scope.myDate = new Date();
	//End Datepicker

	get_booking();
	function get_booking(){
		$http.post('completedbookingRequest', $scope.parameters).success(function(data){
			update(data.response.pop().total);
			$scope.listing=data.response;
		});
	}

	$scope.searchFunction = function(searchtype){

		$scope.parameters.searchtype = searchtype;
		$scope.parameters.start_date='';
		$scope.parameters.end_date='';
		$scope.parameters.filter= "";
		$scope.parameters.page=1;
		get_booking();

    };

    //date function
	$scope.datefun=function(fromdate,todate){
		
		if(fromdate!=='' && fromdate && todate && todate!=''){
			$scope.parameters.start_date=fromdate;
			$scope.parameters.end_date=todate;
			$scope.parameters.searchtype = "";
			$scope.parameters.filter= "";
			$scope.parameters.page=1;
			get_booking();			
		}
	};

	//reset date function
	$scope.reset=function(){
		$scope.parameters.start_date="";
		$scope.parameters.end_date="";
		$scope.parameters.page=1;
		get_booking();			
	};

    $scope.filterRecords = function (record) {
		$scope.parameters.filter= record;
		$scope.parameters.searchtype = "";
		$scope.parameters.start_date='';
		$scope.parameters.end_date='';
		$scope.parameters.page=1;
		get_booking();
	}


	$scope.AmountID = function(id){
    	$scope.booking_id = id;
	};

	$scope.addAmount = function(){
		$scope.get.booking_request_id = $scope.booking_id;
  
		$http.post('../addamountBooking', $scope.get).success(function(data){
			$scope.result=data;
			if(data.status == 0){
				toaster.pop("success", data.message, "");
				$window.location.href = base_url+'welcome/completedbookingRequest/?'+$scope.parameters.page; 
			}
			else{
				toaster.pop("success", data.message, "");
			}
		});
    };

    $scope.PaidID = function(id, event_amount, remain_amount){
    	$scope.booking_id = id;
    	$scope.event_amount = event_amount;
    	$scope.remain_amount = remain_amount;
	};

	$scope.paidAmount = function(){
		$scope.get.booking_request_id = $scope.booking_id;
		$scope.get.event_amount = $scope.event_amount;
  		$scope.get.remain_amount = $scope.remain_amount;
  		
		$http.post('../paidamountBooking', $scope.get).success(function(data){
			$scope.result=data;
			if(data.status == 0){
				toaster.pop("success", data.message, "");
				$window.location.href = base_url+'welcome/completedbookingRequest/?'+$scope.parameters.page; 
			}
			else{
				toaster.pop("success", data.message, "");
			}
		});
		
    };

    $scope.editShowID = function(id, event_amount){
    	$scope.booking_id = id;
    	$scope.get.edit_event_amount = event_amount;
	};

	$scope.editShowAmount = function(){
		$scope.get.booking_request_id = $scope.booking_id;
		$scope.get.event_amount = $scope.get.edit_event_amount;
  		
		$http.post('../editshowamountBooking', $scope.get).success(function(data){
			$scope.result=data;
			if(data.status == 0){
				toaster.pop("success", data.message, "");
				$window.location.href = base_url+'welcome/bookingRequest/?'+$scope.parameters.page; 
			}
			else{
				toaster.pop("success", data.message, "");
			}
		});
		
    };

   
    $scope.BookingID = function(id){
    	$scope.booking_id = id;
	};

	$scope.cancelBooking = function(){
		$scope.get.booking_request_id = $scope.booking_id;
  
		var isConfirmed = confirm("Are you sure to delete this record ?");
		if(isConfirmed){
			$http.post('../cancelbookingRequest', $scope.get).success(function(data){
				$scope.result=data;
				if(data.status == 0){
					toaster.pop("success", data.message, "");
					$window.location.href = base_url+'welcome/bookingRequest/?'+$scope.parameters.page; 
				}
				else{
					toaster.pop("success", data.message, "");
				}
			});
		}
    };

    $scope.reactive = function(id){
		
		var isConfirmed = confirm("Are you sure to active this booking ?");
		if(isConfirmed){
			$http.post('../reactivebookingRequest', {'id': id}).success(function(data){
				$scope.result=data;
				if(data.status == 0){
					toaster.pop("success", data.message, "");
					$window.location.href = base_url+'welcome/bookingRequest/?'+$scope.parameters.page; 
				}
				else{
					toaster.pop("success", data.message, "");
				}
			});
		}
    };


    $scope.detail = function(id){
		$http.post('../completeFormMail', {'id': id}).success(function(data){
			$scope.result=data;
			if(data.status == 0){
				toaster.pop("success", data.message, "");
				get_booking();
			}
			else{
				toaster.pop("success", data.message, "");
			}
		});
    };

    function update(total){
    	
		$scope.total=total;
		$scope.lastpage=Math.ceil(total/$scope.parameters.limit);
		if(total>0){
			$scope.start=((parseInt($scope.parameters.page)-1)*$scope.parameters.limit)+1;
			
			if(total<=(parseInt($scope.parameters.page)*$scope.parameters.limit)){
				$scope.end=total;
			}
			else{
				$scope.end=parseInt($scope.parameters.page)*$scope.parameters.limit;
			}
		}
		else{
			$scope.start=$scope.end=$scope.parameters.page=0;
		}
	}
	
	$scope.changePage=function(page){
		if(page=='down'){
			if(parseInt($scope.parameters.page) > 1){
				$scope.parameters.page=parseInt($scope.parameters.page)-1;
			}
		}
		else if(page=='up'){
			if(parseInt($scope.parameters.page) < $scope.lastpage){
				$scope.parameters.page=parseInt($scope.parameters.page)+1;
			}
		}
		else{
			if(page<1)
			$scope.parameters.page=1;
			else if(page>$scope.lastpage)
			$scope.parameters.page=$scope.lastpage;
			else if(page>=1 && page<=$scope.lastpage)
			$scope.parameters.page=$scope.parameters.page;
			else
			$scope.parameters.page=1;
		}
		$scope.result.status=-1;						//Hide the alert box
		get_booking();
	};
	
});
