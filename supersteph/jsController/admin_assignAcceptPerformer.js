var app = angular.module("myApp", ['toaster']);

app.controller("assignAcceptPerformerController", function($scope, $http, toaster, $location, $window, $timeout) { 
    
	$scope.result={};
	$scope.get={};
	// $scope.show=1;

	var main_url = $location.absUrl();
	var url = $location.absUrl().split('/?')[0];
	var performer_id = $location.absUrl().split('/?')[1];
	var booking_id = $location.absUrl().split('/?')[2];

	init();
	function init(){
		$http.post('', {'performer_id' : performer_id, 'booking_id' : booking_id}).success(function(data){
			$scope.listing=data;
			if(data.status == 0){
				$scope.show=1;
				toaster.pop("success", data.message, "");
				$timeout(function() {  
	              	$window.location.href = angular_base_url+'welcome/thankYou'; 
	            }, 900); 
			}
			else if(data.status == 1){
				toaster.pop("danger", data.message, "");
				$timeout(function() {  
	              	$window.location.href = angular_base_url+'welcome/thankEvent'; 
	            }, 900); 
			}
			else{
				toaster.pop("danger", data.message, "");
				$timeout(function() {  
	              	$window.location.href = angular_base_url+'welcome/cancelEvent'; 
	            }, 900); 
			}
		});
	}
	
});
