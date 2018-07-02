var app = angular.module("myApp", ['toaster', 'ui.bootstrap']);

app.controller("editAssignPerformerController", function($scope, $http, toaster, $location, $window, $timeout) { 
    
	$scope.result={};
	$scope.get={};
	$scope.parameters={};
	
	var main_url = $location.absUrl();
	var url = $location.absUrl().split('/?')[0];
	var id = $location.absUrl().split('/?')[1];
	var page_id = $location.absUrl().split('/?')[2];
	console.log($scope.get);
	if(page_id){
		$scope.parameters.page=page_id;
	}
	else{
		$scope.parameters.page=1;
	}

	$scope.hstep = 1;
  	$scope.mstep = 1;
  	$scope.ismeridian = true;

	init();
	function init(){
		$http.post('', {'id' : id}).success(function(data){
			$scope.get=data.response[0];
			console.log($scope.get);
		});
	}

	$scope.editAssignPerformer = function(){
		
		$http.post('../saveEditAssignPerformer', $scope.get).success(function(data){
			$scope.listing=data;
			if(data.status == 0){
				toaster.pop("success", data.message, "");
				$timeout(function() {  
                  	$window.location.href = base_url+'welcome/editBookAssignPerformer/?'+$scope.get.booking_id+'/?'+page_id;  
                }, 900);  
			}
			else{
				toaster.pop("error", data.message, "");
			}
		});
	}

    
});