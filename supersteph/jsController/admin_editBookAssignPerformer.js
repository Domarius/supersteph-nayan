//var app = angular.module("myApp", ['toaster', 'ui.bootstrap']);
var app = angular.module("myApp", ['toaster','ngFileUpload','ngMaterial','ngMessages', 'ui.select', 'ngAnimate', 'ui.bootstrap','angularMoment']);
//var app = angular.module("myApp", ['toaster','ngFileUpload', 'ui.select'])
app.config(function(uiSelectConfig) {
  uiSelectConfig.removeSelected = false;
});
app.controller("editBookAssignPerformerController", function($scope, $http, toaster, $location, $window, $timeout, Upload) { 
    
	$scope.result={};
	$scope.get={};
	$scope.parameters={};
	$scope.listing=[];
	$scope.categoryListing=[];
	
	var main_url = $location.absUrl();
	var url = $location.absUrl().split('/?')[0];
	var id = $location.absUrl().split('/?')[1];
	var page_id = $location.absUrl().split('/?')[2];

	$scope.id = $location.absUrl().split('/?')[1];

	if(page_id){
		$scope.parameters.page=page_id;
	}
	else{
		$scope.parameters.page=1;
	}

	$scope.hstep = 1;
  	$scope.mstep = 1;
  	//$scope.ismeridian = true;

  	$scope.durationListing=[];
	

	get_category();
	function get_category(){
		$http.post('../categoryList').success(function(data){
			$scope.categoryListing=data.response;
			console.log($scope.categoryListing)

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

	init();
	function init(){
		$http.post('../editBookAssignBookingName', {'booking_id' : id}).success(function(data){
			$scope.listingBooking=data.response;
			get_category();
			
		});
	}

	initPerformar();
	function initPerformar(){
		$http.post('', {'booking_id' : id}).success(function(data){
			$scope.listingPerformer=data.response;
			console.log($scope.listingPerformer);
			//alert("Do you Really Want to edit ");
			// if($scope.listingPerformer){
			// 	$scope.listingPerformer=data.response; 
				
				  
			// }else{
			// 	$window.location.href = base_url+'welcome/actionbookingRequest/?'+id+'/?'+page_id; 
			// 					}
			//alert($scope.listingPerformer);
			//console.log(data.response);
		});
	}

	$scope.editAssignPerformer = function(list){
		$scope.get = list;
		console.log($scope.get); 
		$http.post('../saveEditAssignPerformer', $scope.get).success(function(data){
			$scope.listing=data;
			if(data.status == 0){
				toaster.pop("success", data.message, "");
				// $timeout(function() {  
    //               	//$window.location.href = base_url+'welcome/editBookAssignPerformer/?'+$scope.get.booking_id+'/?'+page_id;  
    //             		$window.location.href = base_url+'welcome/editBookAssignPerformer/?'+$scope.get.booking_id+'/?'+page_id;
    //             }, 900);  
			}
			else{
				toaster.pop("error", data.message, "");
			}
		});
	}

	$scope.emailAssignPerformer = function(list){
		$scope.get = list;
		console.log($scope.get); 
		$http.post('../emailEditAssignPerformer', $scope.get).success(function(data){
			$scope.listing=data;
			if(data.status == 0){
				toaster.pop("success", data.message, "");
				// $timeout(function() {  
    //               	//$window.location.href = base_url+'welcome/editBookAssignPerformer/?'+$scope.get.booking_id+'/?'+page_id;  
    //             		$window.location.href = base_url+'welcome/editBookAssignPerformer/?'+$scope.get.booking_id+'/?'+page_id;
    //             }, 900);  
			}
			else{
				toaster.pop("error", data.message, "");
			}
		});
	}
	 $scope.isDisabled = false;
    $scope.disableClick = function() {
        // alert("Clicked!");
        $scope.isDisabled = true;
        return false;
    }

	$scope.editBookAssignPerformer = function(list){
		//console.log(list); 
$scope.isDisabled = true;
		//if($scope.listingPerformer.length > 1){

			//if(confirm("Do you want to assign performar?")){
				$http.post('../editBookPerformer', {'performer_id': $scope.listingPerformer, 'booking_request_id': id}).success(function(data){
					$scope.result = data;
					if(data.status == 0){
						toaster.pop("success", data.message, "");
						$timeout(function() {  
		                  	$window.location.href = base_url+'welcome/editbookingRequest/?'+id+'/?'+page_id; 
		                }, 900); 
					}
					else{
						toaster.pop("error", data.message, "");

					}
				});
			//}
		// }
		// else{
		// 	toaster.pop("error", "Please assign performar?", "");
		// }
	}


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

    
});

// app.filter('propsFilter', function() {
// 	return function(items, props) {
// 		var out = [];
// 		if (angular.isArray(items)) {
// 			var keys = Object.keys(props);

// 			items.forEach(function(item) {
// 				var itemMatches = false;

// 				for (var i = 0; i < keys.length; i++) {
// 					var prop = keys[i];
// 					var text = props[prop].toLowerCase();
// 					if (item[prop].toString().toLowerCase().indexOf(text) !== -1) {
// 						itemMatches = true;
// 						break;
// 					}
// 				}
// 				if (itemMatches) {
// 					out.push(item);
// 				}
// 			});
// 		} 
// 		else {
// 			out = items;
// 		}
// 		return out;
// 	}; 
// });