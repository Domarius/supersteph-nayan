var app = angular.module("myApp", ['toaster','ngFileUpload', 'ui.select']);

app.controller("EditPerformerController", function($scope, $http, toaster, $location, $window, $timeout, Upload) { 
    
	$scope.result={};
	$scope.get={};
	$scope.categoryListing=[];

	var main_url = $location.absUrl();
	var url = $location.absUrl().split('/?')[0];
	var id = $location.absUrl().split('/?')[1];

	init();
	function init(){
		$http.post('', {'id' : id}).success(function(data){
			$scope.get=data.response[0];
			get_category();
		});
	}


	function get_category(){
		$http.post('../categoryList').success(function(data){
			$scope.categoryListing=data.response;

			$scope.user_type = [];
		 	angular.forEach($scope.get.category_name,function(value,index){
				var index = $scope.categoryListing.findIndex(x=>x.id === value.id)
		        $scope.user_type.push($scope.categoryListing[index]);
			});

			$scope.get.category = $scope.user_type;
		});
	}

	//Performer Upload
	$scope.performer_upload = function () {
		Upload.upload({
			url: '../saveEditperformer',
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

	$scope.editPerformer = function(){

		if(($scope.get.performer_image!='' && $scope.get.performer_image!=$scope.get.old_performer_image)){
			$scope.performer_upload();
		}
		else{
			$http.post('../saveEditperformer', $scope.get).success(function(data){
				$scope.listing=data;
				if(data.status == 0){
					toaster.pop("success", data.message, "");
					$scope.get = "";
					$timeout(function() {  
	                  	$window.location.href = base_url+'welcome/performer';  
	                }, 900);  
				}
				else{
					toaster.pop("error", data.message, "");
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

