var app = angular.module("myApp", ['toaster']);

app.controller("generalAddFeedbackController", function($scope, $http, toaster, $location, $window, $timeout) { 
    
	$scope.result={};
	$scope.get={};

	var main_url = $location.absUrl();
	var url = $location.absUrl().split('/?')[0];
	var booking_id = $location.absUrl().split('/?')[1];
	var performer_id = $location.absUrl().split('/?')[2];

	init();
	function init(){
		$http.post('../feedbackBooking', {'id' : booking_id}).success(function(data){
			$scope.listing=data.response;
		});
	}

	$scope.addGeneralFeedback = function(){
		$scope.get.booking_id= booking_id;
		$scope.get.performer_id= performer_id;

		$http.post('generalAddFeedback', $scope.get).success(function(data){
			$scope.result=data;
			if(data.status == 1){
				$scope.get = "";
				toaster.pop("success", "Feedback saved Successfully", "");
				$timeout(function() {  
                  	$window.location.href = base_url+'welcome/thankYou';  
                }, 900);  
			}
			else{
				toaster.pop("success", "Feedback saved Successfully", "");
				$timeout(function() {  
                  	$window.location.href = base_url+'welcome/thankYou';  
                }, 900);
				//alert(data.message);
				//toaster.pop("error", data.message, "");
				$timeout(function() {  
                  	//$window.location.href = base_url+'welcome/sessionExpire';  
                }, 900); 
			}
		});
	}

    
});
