var app = angular.module("myApp", ['toaster']);

app.controller("adminLoginController", function($scope, $http, toaster, $location, $window,$timeout) { 
    
	$scope.result={}; 
	$scope.get={};
	
	$scope.admin_login =function(){

		$http.post('login', $scope.get).success(function(data){
			$scope.result=data;
			if(data.status == 0){
				toaster.pop('success', data.message, "");
				$scope.get={};	
				$timeout(function() {  
                  $window.location.href = base_url+'welcome/bookingRequest/?1';  
                }, 900);  
			} 
		 	else {
			 	toaster.pop('error', data.message, ""); 
			}   
		});
	} 
});
