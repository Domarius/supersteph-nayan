var app = angular.module("myApp", ['toaster','ngFileUpload']);

app.controller("AddClientController", function($scope, $http, toaster, $location, $window, $timeout, Upload) { 
    
	$scope.result={};
	$scope.get={};
	
	$scope.addClient = function(){

		$http.post('addclient', $scope.get).success(function(data){
			$scope.listing=data;
			if(data.status == 0){
				toaster.pop("success", data.message, "");
				$scope.get = "";
				$timeout(function() {  
	              	$window.location.href = angular_base_url+'admin/client'; 
	            }, 900); 
			}
			else{
				toaster.pop("error", data.message, "");
				$timeout(function() {  
	              	$window.location.href = angular_base_url+'admin/addclient'; 
	            }, 900);  
			}
		});	
	}

    
});
