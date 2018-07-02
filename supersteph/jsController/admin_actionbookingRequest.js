var app = angular.module("myApp", ['toaster','ngMaterial','ngMessages','angularjs-datetime-picker']);

app.controller("ActionBookingRequestController", function($scope, $http, toaster, $location, $window, $timeout) { 
    
	$scope.result={};
	$scope.get={};
	$scope.listing={};
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

	get_actionbooking();
	function get_actionbooking(){
		$http.post('actionbookingRequest', {'id':id}).success(function(data){
			$scope.listing=data.response;
			$scope.booking_name = data.response[0]['name'];
		});
	}


	$scope.paperWork = function(id){
		$http.post('../paperworkMail', {'id': id}).success(function(data){
			$scope.result=data;
			if(data.status == 0){
				toaster.pop("success", data.message, "");
				get_actionbooking();
			}
			else{
				toaster.pop("success", data.message, "");
			}
		});
    };

    $scope.lookingForward = function(id){
		$http.post('../lookingForwardMail', {'id': id}).success(function(data){
			$scope.result=data;
			if(data.status == 0){
				toaster.pop("success", data.message, "");
				get_actionbooking();
			}
			else{
				toaster.pop("success", data.message, "");
			}
		});
    };

    $scope.payment = function(id){
		$http.post('../paymentMail', {'id': id}).success(function(data){
			$scope.result=data;
			if(data.status == 0){
				toaster.pop("success", data.message, "");
				get_actionbooking();
			}
			else{
				toaster.pop("success", data.message, "");
			}
		});
    };

    $scope.feedback = function(id){
		$http.post('../feedbackMail', {'id': id}).success(function(data){
			$scope.result=data;
			if(data.status == 0){
				toaster.pop("success", data.message, "");
				get_actionbooking();
			}
			else{
				toaster.pop("success", data.message, "");
			}
		});
    };

	
	
});
