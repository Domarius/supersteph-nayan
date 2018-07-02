var app = angular.module("myApp", ['toaster','ngFileUpload']);

app.controller("addCategoryController", function($scope, $http, toaster, $location, $window, $timeout, Upload) { 
    
	$scope.result={};
	$scope.get={};

	$scope.addCategory = function(){
		
		$http.post('addcategory', $scope.get).success(function(data){
			$scope.listing=data;
			if(data.status == 0){
				$scope.get = "";
				toaster.pop("success", data.message, "");
				$timeout(function() {  
                  	$window.location.href = base_url+'welcome/category';  
                }, 900);  
			}
			else{
				toaster.pop("error", data.message, "");
			}
		});
	}

    
});
