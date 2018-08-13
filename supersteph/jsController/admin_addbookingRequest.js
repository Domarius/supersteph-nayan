var app = angular.module("myApp", ['toaster','ngFileUpload', 'ngMaterial','ngMessages','ui.select', 'ngAnimate', 'ui.bootstrap','angularMoment']);

app.config(function($mdDateLocaleProvider) {
    $mdDateLocaleProvider.formatDate = function(date) {
       return moment(date).format('DD-MM-YYYY');
    };
});

app.controller("addBookingRequestController", function($scope, $http, toaster, $location, $window, $timeout, Upload) { 
    
	$scope.result={};
	$scope.get={};
	$scope.categoryListing=[];

	get_category();
	function get_category(){
		$http.post('categoryList').success(function(data){ 
			$scope.categoryListing=data.response;
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
	//$scope.get.show_time = new Date();
	$scope.hstep = 1;
  	$scope.mstep = 1;
  	//$scope.ismeridian = true;
	//End Timepicker

	//Start Datepicker
	$scope.myDate = new Date();
	//End Datepicker
	$scope.clear = function($event) {
	   $event.stopPropagation(); 
	   $scope.country.selected = undefined;
	};
	
	$scope.addBookingRequest = function(){
		
		$http.post('addbookingRequest', $scope.get).success(function(data){
			$scope.listing=data;

			if(data.status == 0){
				console.log(data);				
				console.log('Bookin id = '+data.booking_id);
				$scope.get = "";
				toaster.pop("success", data.message, "");
				$timeout(function() {  
                  	$window.location.href = base_url+'welcome/bookPerformer/?' + data.booking_id +'/?1';  
                }, 900);  
			}
			else{
				toaster.pop("error", data.message, "");
			}
		});
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
