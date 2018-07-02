var app = angular.module("myApp", ['toaster']);

app.controller("adminForgotPasswordController", function($scope, $http, toaster, $location, $window,$timeout) { 
    
	$scope.result={}; 
	$scope.get={};
	
	$scope.admin_forgotpassword =function(){

		$http.post('forgotpassword', $scope.get).success(function(data){
			$scope.result=data;
			if(data.status == 0){
				toaster.pop('success', data.message, "");
				$scope.get={};	 
				$timeout(function() {  
                  	$window.location.href = base_url+'welcome/login';  
                }, 900); 
			}
		 	else {
			 	toaster.pop('error', data.message, ""); 
			}   
		});
	} 
});
