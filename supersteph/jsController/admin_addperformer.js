var app = angular.module("myApp", ['toaster','ngFileUpload', 'ui.select']);

app.controller("AddPerformerController", function($scope, $http, toaster, $location, $window, $timeout, Upload) { 
    
	$scope.result={};
	$scope.get={};
	$scope.categoryListing=[];

	get_category();
	function get_category(){
		$http.post('categoryList').success(function(data){
			$scope.categoryListing=data.response;
		});
	}
	
	//Performer Upload
	$scope.performer_upload = function () {
		Upload.upload({
			url: 'addperformer',
			method: 'POST',
			data:{'file':$scope.get.performer_image, 'data': $scope.get}
		}).then(function (resp) {
			$scope.result=resp.data;
			if(resp.data.status == 0){
				$scope.get = "";
				toaster.pop('success', resp.data.message, '');	
				$timeout(function() {  
                  	$window.location.href = base_url+'welcome/performer'; 
                }, 900); 
			}
			else {
				toaster.pop('error', resp.data.message, ''); 
			}
		});
    };

	$scope.addPerformer = function(){

		if(($scope.get.performer_image!='' && $scope.get.performer_image)){
			$scope.performer_upload();
		}
		else{
			$http.post('addperformer', $scope.get).success(function(data){
				$scope.listing=data;
				if(data.status == 0){
					toaster.pop("success", data.message, "");
					$scope.get = "";
					$timeout(function() {  
	                  	$window.location.href = base_url+'welcome/performer'; 
	                }, 900); 
				}
				else{
					toaster.pop("success", data.message, "");
					//toaster.pop("error", data.message, "");
					$timeout(function() {  
	                  	$window.location.href = base_url+'welcome/performer'; 
	                }, 900); 
				}
			});
		}
	}

    
});

app.filter('propsFilter', function() {
	return function(items, props) {
		var out = [];
		if (angular.isArray(items)) {
			var keys = Object.keys(props);

			items.forEach(function(item) {
				var itemMatches = false;

				for (var i = 0; i < keys.length; i++) {
					var prop = keys[i];
					var text = props[prop].toLowerCase();
					if (item[prop].toString().toLowerCase().indexOf(text) !== -1) {
						itemMatches = true;
						break;
					}
				}
				if (itemMatches) {
					out.push(item);
				}
			});
		} 
		else {
			out = items;
		}
		return out;
	};
});
