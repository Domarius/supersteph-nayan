var app = angular.module("myApp", ['toaster']);

app.controller("categoryController", function($scope, $http, toaster, $location, $window, $timeout) { 
    
	$scope.result={};
	$scope.get={};
	$scope.listing={};
	$scope.parameters={};							//initialize parameters to be send
	$scope.parameters.limit=10;						//initialize pagination default limit to 10
	$scope.parameters.page=1;						//initialize first page value to 1 for pagination
	$scope.start=$scope.end=$scope.total=0;			//initialize start to 0 for first result
	$scope.result.status=-1;

	get_category();
	function get_category(){
		$http.post('category', $scope.parameters).success(function(data){
			update(data.response.pop().total);
			$scope.listing=data.response;
		});
	}

	$scope.searchFunction = function(searchData){
		$scope.parameters.searchtype = searchData;
		$scope.parameters.page=1;
		get_category();
    };

    $scope.deleteCategory = function(id){
		var isConfirmed = confirm("Are you sure to delete this record ?");
		if(isConfirmed){
			$http.post('deletecategory', {'id' : id}).success(function(data){
				$scope.result=data;
				if(data.status == 0){
					toaster.pop("success", data.message, "");
					get_category();
				}
				else{
					toaster.pop("success", data.message, "");
				}
			});
		}
    };


    function update(total){
    	
		$scope.total=total;
		$scope.lastpage=Math.ceil(total/$scope.parameters.limit);
		if(total>0){
			$scope.start=((parseInt($scope.parameters.page)-1)*$scope.parameters.limit)+1;
			
			if(total<=(parseInt($scope.parameters.page)*$scope.parameters.limit)){
				$scope.end=total;
			}
			else{
				$scope.end=parseInt($scope.parameters.page)*$scope.parameters.limit;
			}
		}
		else{
			$scope.start=$scope.end=$scope.parameters.page=0;
		}
	}
	
	$scope.changePage=function(page){
		if(page=='down'){
			if(parseInt($scope.parameters.page) > 1){
				$scope.parameters.page=parseInt($scope.parameters.page)-1;
			}
		}
		else if(page=='up'){
			if(parseInt($scope.parameters.page) < $scope.lastpage){
				$scope.parameters.page=parseInt($scope.parameters.page)+1;
			}
		}
		else{
			if(page<1)
			$scope.parameters.page=1;
			else if(page>$scope.lastpage)
			$scope.parameters.page=$scope.lastpage;
			else if(page>=1 && page<=$scope.lastpage)
			$scope.parameters.page=$scope.parameters.page;
			else
			$scope.parameters.page=1;
		}
		$scope.result.status=-1;						//Hide the alert box
		get_category();
	};
	
});
