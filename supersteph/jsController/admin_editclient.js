var app = angular.module("myApp", ['toaster','ngFileUpload']);

app.controller("EditClientController", function($scope, $http, toaster, $location, $window, $timeout, Upload) { 
    
	$scope.result={};
	$scope.get={};

	var main_url = $location.absUrl();
	var url = $location.absUrl().split('/?')[0];
	var id = $location.absUrl().split('/?')[1];

	init();
	function init(){
		$http.post('', {'id' : id}).success(function(data){
			$scope.get=data.response[0];
		});
	}
	
	$scope.editClient = function(){

		$http.post('../saveEditclient', $scope.get).success(function(data){
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
                  	$window.location.href = angular_base_url+'admin/editclient/?'+id;
                }, 900);
			}
		});
	}

    
});
