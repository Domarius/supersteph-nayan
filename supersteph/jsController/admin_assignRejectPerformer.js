var app = angular.module("myApp", ['toaster']);

app.controller("assignRejectPerformerController", function($scope, $http, toaster, $location, $window, $timeout) { 
    
	$scope.result={};
	$scope.get={};

	var main_url = $location.absUrl();
	var url = $location.absUrl().split('/?')[0];
	var performer_id = $location.absUrl().split('/?')[1];
	var booking_id = $location.absUrl().split('/?')[2];
	
	$scope.rejectPerformer = function(){
		$scope.get.performer_id = performer_id;
		$scope.get.booking_id = booking_id;
		$http.post('', $scope.get).success(function(data){
			$scope.listing=data;
			if(data.status == 0){
				toaster.pop("success", data.message, "");
				$scope.get = "";
				$timeout(function() {  
	              	$window.location.href = angular_base_url+'welcome/sessionExpire'; 
	            }, 900); 
			}
			else if(data.status == 1){
				toaster.pop("danger", data.message, "");
				$scope.get = "";
				$timeout(function() {  
	              	$window.location.href = angular_base_url+'welcome/cancelEvent'; 
	            }, 900); 
			}
			else{
				toaster.pop("danger", data.message, "");
				$scope.get = "";
				$timeout(function() {  
	              	$window.location.href = angular_base_url+'welcome/thankEvent'; 
	            }, 900); 
			}
		});	
	}
	
});
