var app = angular.module("myApp", ['toaster','ngFileUpload']);

app.controller("EditEmailTemplateController", function($scope, $http, toaster, $location, $window, $timeout, Upload) { 
    
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
	
	$scope.saveEmailTemplate = function(){
		
		$http.post('../saveEditemailTemplate', $scope.get).success(function(data){
			$scope.listing=data;
			if(data.status == 0){
				$scope.get = "";
				toaster.pop("success", data.message, "");
				$timeout(function() {  
                  	$window.location.href = base_url+'welcome/emailTemplate';  
                }, 900);  
			}
			else{
				toaster.pop("error", data.message, "");
			}
		});
	}

    
});
