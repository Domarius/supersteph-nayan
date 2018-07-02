var app = angular.module("myApp", ['toaster']);

app.controller("ViewBookingRequestController", function($scope, $http, toaster, $location, $window, $timeout) { 
    
	$scope.result={};
	$scope.get={};
	$scope.parameters={};

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

	init();
	function init(){
		$http.post('', {'id' : id}).success(function(data){
			$scope.get=data.response[0];
		});
	}

	
});

