var app = angular.module("myApp", ['toaster']);

app.controller("generalAddBookingController", function($scope, $http, toaster, $location, $window, $timeout) { 
    
	$scope.result={};
	$scope.get={};

	var main_url = $location.absUrl();
	var url = $location.absUrl().split('/?')[0];
	var id = $location.absUrl().split('/?')[1];

	init();
	function init(){
		$http.post('../performerBooking', {'id' : id}).success(function(data){
			$scope.listing=data.response;
		   //console.log(data.response);
		});
	}

get_bookingperformer();
	function get_bookingperformer(){
		$http.post('../infoBooking', {'id' : id}).success(function(data){
			$scope.listings=data.response;
			$scope.get =data.response[0];
			//console.log($scope.listings);
			console.log($scope.get);
			
		});
	}
//$scope.get =data.response;
	$scope.addGeneralBooking = function(){
		$scope.get.id= id;
		$http.post('generalAddBooking', $scope.get).success(function(data){
			$scope.result=data;
			console.log(data.response);
			if(data.status == 0){
				//$scope.get = "";
				toaster.pop("success", data.message, "");
				$timeout(function() {  
                  	$window.location.href = base_url+'welcome/thankYou';  
                }, 900);  
			}
			else{
				toaster.pop("error", data.message, "");
				$window.location.href = base_url+'welcome/sessionExpire';  
			}
		});
	}

    
});
