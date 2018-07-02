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
		{"label":"1 Hours", "value":"1"},
		{"label":"1.5 Hours", "value":"1.5"},
		{"label":"2 Hours", "value":"2"},
		{"label":"2.5 Hours", "value":"2.5"}
	];

	//Start Timepicker
	$scope.get.show_time = new Date();
	$scope.hstep = 1;
  	$scope.mstep = 1;
  	$scope.ismeridian = true;
	//End Timepicker

	//Start Datepicker
	$scope.myDate = new Date();
	//End Datepicker

	$scope.addBookingRequest = function(){
		
		$http.post('addbookingRequest', $scope.get).success(function(data){
			$scope.listing=data;
			if(data.status == 0){
				$scope.get = "";
				toaster.pop("success", data.message, "");
				$timeout(function() {  
                  	$window.location.href = base_url+'welcome/bookingRequest';  
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
