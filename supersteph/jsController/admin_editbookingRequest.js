var app = angular.module("myApp", ['toaster','ngFileUpload','ngMaterial','ngMessages', 'ui.select', 'ngAnimate', 'ui.bootstrap','angular-page-loader','angularMoment']);

app.config(function($mdDateLocaleProvider) {
    $mdDateLocaleProvider.formatDate = function(date) {
       return moment(date).format('DD-MM-YYYY');
    };
});
app.config(function(uiSelectConfig) {
  uiSelectConfig.removeSelected = true;
});
app.controller("editBookingRequestController", function($scope, $http, toaster, $location, $window, $timeout, Upload) { 
    
	$scope.result={};
	$scope.get={};
	$scope.parameters={};
	$scope.categoryListing=[];
	$scope.durationListing=[];


	var main_url = $location.absUrl();
	var url = $location.absUrl().split('/?')[0];
	var id = $location.absUrl().split('/?')[1];
	var page_id = $location.absUrl().split('/?')[2];

	$scope.id = $location.absUrl().split('/?')[1];
	$scope.isDisabled = false;
	if(page_id){
		$scope.parameters.page=page_id;
	}
	else{
		$scope.parameters.page=1;
	}

	init();
	function init(){
		$http.post('', {'id' : id}).success(function(data){
			$scope.get=data.response[0];
			$scope.get.paid="";
			
			$scope.get.event_date = new Date($scope.get.event_date1);
			get_category();
		});
	}
    
    get_bookingperformer();
	function get_bookingperformer(){
		$http.post('../editViewperformer', {'id' : id}).success(function(data){
			$scope.listing1=data.response;
			console.log($scope.listing1);
		});
	}
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
				$window.location.href = base_url+'welcome/bookingRequest/?'+$scope.parameters.page; 
			}
			else{
				toaster.pop("success", data.message, "");
			}
		});
		
    };



    	$scope.editAssignPerformer = function(){
		
		$http.post('../saveEditAssignPerformer', $scope.get).success(function(data){
			$scope.listing=data;
			//alert('ok');
			if(data.status == 0){
				toaster.pop("success", data.message, "");
				$timeout(function() {  
                  	$window.location.href = base_url+'welcome/editbookingRequest/?'+$scope.get.booking_id+'/?'+page_id;  
                }, 900);  
			}
			else{
				toaster.pop("error", data.message, "");
			}
		});
	}


		$scope.editBookAssignPerformer = function(){		
		//console.log($scope.get);
		$http.post('../saveEditAssignPerformer', $scope.get).success(function(data){
			
			$scope.listing=data;
			//alert($scope.get.id);
			//alert('ok');
			if(data.status == 0){
				toaster.pop("success", data.message, "");
				$timeout(function() {  
                  	$window.location.href = base_url+'welcome/editbookingRequest/?'+$scope.get.booking_id+'/?'+page_id;  
                }, 900);  
			}
			else{
				toaster.pop("error", data.message, "");
			} 
		});
	
	}
//Cancel Booking Request

$scope.BookingID = function(id){
    	$scope.booking_id = id;
	};
	$scope.evnt = function(id){
    	$scope.get.paid = id;

    	console.log($scope.paid);
	};

	$scope.cancelBooking = function(){
		$scope.get.booking_request_id = $scope.booking_id;
  		console.log($scope.booking_id);
		var isConfirmed = confirm("Are you sure you want to cancel this booking? (Emails will be sent to client and performers)");
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

//End cancel Booking request

//get_category();
	function get_category(){
		$http.post('../categoryList').success(function(data){
			$scope.categoryListing=data.response;

			$scope.user_type = [];
		 	angular.forEach($scope.get.category_name,function(value,index){
				var index = $scope.categoryListing.findIndex(x=>x.id === value.id);
		        $scope.user_type.push($scope.categoryListing[index]);
			});

			$scope.get.show_type = $scope.user_type;
			//alert($scope.user_type);
		});
	}

	$scope.durationListing=[
			{"label":"15 Min", "value":"15 Min"},
		{"label":"30 Min", "value":"30 Min"},
		{"label":"45 Min", "value":"45 Min"},
		{"label":"1 Hours", "value":"1 Hours"},
		{"label":"1 Hours 15 Minute", "value":"1 Hours 15 Minute"},
		{"label":"1 Hours 30 Minute", "value":"1 Hours 30 Minute"},
		{"label":"1 Hours 45 Minute", "value":"1 Hours 45 Minute"},
		{"label":"2 Hours", "value":"2 Hours"},		
		{"label":"2 Hours 15 Minute", "value":"2 Hours 15 Minute"},
		{"label":"2 Hours 30 Minute", "value":"2 Hours 30 Minute"},
		{"label":"2 Hours 45 Minute", "value":"2 Hours 45 Minute"},
		{"label":"3 Hours", "value":"3 Hours"},		
		{"label":"3 Hours 15 Minute", "value":"3 Hours 15 Minute"},
		{"label":"3 Hours 30 Minute", "value":"3 Hours 30 Minute"},
		{"label":"3 Hours 45 Minute", "value":"3 Hours 45 Minute"},
		{"label":"4 Hours", "value":"4 Hours"},		
		{"label":"4 Hours 30 Minute", "value":"4 Hours 30 Minute"},
		{"label":"5 Hours", "value":"5 Hours"},		
		{"label":"5 Hours 30 Minute", "value":"5 Hours 30 Minute"},
		{"label":"6 Hours", "value":"6 Hours"},		
		{"label":"6 Hours 30 Minute", "value":"6 Hours 30 Minute"},
		{"label":"7 Hours", "value":"7 Hours"},		
		{"label":"7 Hours 30 Minute", "value":"7 Hours 30 Minute"},
		{"label":"8 Hours", "value":"8 Hours"},	
	];

	//Start Timepicker
	$scope.date = new Date();
	$scope.hstep = 1;
  	$scope.mstep = 1;
  	$scope.ismeridian = true;
	//End Timepicker

	//Start Datepicker
	$scope.myDate = new Date();
	//End Datepicker
  $scope.isDisabled = false;
    $scope.disableClick = function() {
        // alert("Clicked!");
        $scope.isDisabled = true;
        return false;
    }
	$scope.editBookingRequest = function(){
		console.log($scope.get);
$scope.isDisabled = true;
		//if(parseInt($scope.get.pay_amount) <= parseInt($scope.get.remain_amount) || $scope.get.paid != "") {
				$http.post('../saveEditBookingRequest', $scope.get).success(function(data){
					$scope.listing=data;
					//alert("Okay");
					
					//alert(data);
					//console.log($scope.listing)
					if(data.status == 0){

						$scope.get = "";
						toaster.pop("success", data.message, "");
						//var isConfirmed = confirm("Update performer schedule?");
						//if(isConfirmed){
							$timeout(function() {  
			                  	$window.location.href = base_url+'welcome/editbookingRequest/?'+id+'/?'+page_id;  
			                }, 100);  
			                 
						// }
						// else{
						//  	$timeout(function() {  
			   //                	$window.location.href = base_url+'welcome/bookingRequest/?'+page_id;  
			   //               }, 100);  
						//  }
					} 
					else{
						toaster.pop("error", data.message, "");
					}
				});
		// }
		// else {
		// 	alert("System cant accept more the the show amount . Please try again....");
		// }
	}

    
});


  


app.filter('propsFilter', function() {
	return function(items, props) {
		var out = [];
		if (angular.isArray(items)) {
			var keys = Object.keys(props);

			items.forEach(function(item) {
				var itemMatches = false;

				for (var i = 0; i < keys.length; i++) {
					var prop = keys[i];
					var text = props[prop].toLowerCase();
					if (item[prop].toString().toLowerCase().indexOf(text) !== -1) {
						itemMatches = true;
						break;
					}
				}
				if (itemMatches) {
					out.push(item);
				}
			});
		} 
		else {
			out = items;
		}
		return out;
	};
});
