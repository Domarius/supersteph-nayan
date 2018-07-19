var app = angular.module("myApp", ['toaster','ui.bootstrap']);

app.controller("ChasingPaperworkController", function($scope, $http, toaster, $location, $window, $timeout) { 
    
	$scope.result={};
	$scope.get={};

	$scope.parameters={};							//initialize parameters to be send
	$scope.parameters.limit=100;						//initialize pagination default limit to 10
	$scope.parameters.page=1;						//initialize first page value to 1 for pagination
	$scope.start=$scope.end=$scope.total=0;			//initialize start to 0 for first result
	
	get_paperworkEmail();
	function get_paperworkEmail(){
		$scope.parameters.template='Chasing Paperwork';
		$http.post('managerEmailTemplate', $scope.parameters).success(function(data){
			$scope.get=data.response[0];
		});
	}

	get_paperwork();
	function get_paperwork(){
		$http.post('chasingPaperwork', $scope.parameters).success(function(data){
			update(data.response.pop().total);
			$scope.listing=data.response;
		});
	}

		$scope.filterRecords = function (record) {
		$scope.parameters.filter= record;
		$scope.parameters.searchtype = "";
		$scope.parameters.start_date='';
		$scope.parameters.end_date='';
		$scope.parameters.page=1;
		get_paperwork();
	}
	//Start CheckBox
	$scope.SelectID = [];
	$scope.get.checkalltype = false;
	$scope.delete_count = 0;
	$scope.selectCheck = function (ids, checkall) {
		if(ids == 'All'){
			if(checkall){
				angular.forEach($scope.listing, function(items){
					$scope.SelectID.push(items.id);
					$scope.delete_count = $scope.delete_count + 1;
				});	
			}
			else{
				$scope.SelectID=[];
				$scope.delete_count = 0;
			}
		}
		else{
			var index_id = $scope.SelectID.indexOf(ids);
            if(index_id == -1){
                $scope.SelectID.push(ids);
				$scope.delete_count = $scope.delete_count + 1;
            }
            else{
                $scope.SelectID.splice(index_id,1);
				$scope.delete_count = $scope.delete_count - 1;
            }
		}
    };
	//End CheckBox

	$scope.assignEmail = function(){
		if(confirm("Do you want to send email?")){
			$scope.get.booking_id= $scope.SelectID;
			$http.post('sendPaperworkEmail', $scope.get).success(function(data){
				$scope.result = data;
				if(data.status == 0){
					toaster.pop("success", data.message, "");
					$scope.delete_count = 0;
					$window.location.href = base_url+'welcome/chasingPaperwork';
				}
				else{
					toaster.pop("error", data.message, "");
				}
			});
		}
    };

	
// Ignore Clients
$scope.ignoreassignEmail = function(){
		if(confirm("Do you want to Ignore clients?")){
			$scope.get.booking_id= $scope.SelectID;
			$http.post('ignorePaperworkEmail', $scope.get).success(function(data){
				$scope.result = data;
				if(data.status == 1){
					toaster.pop("success", data.message, "");
					$scope.delete_count = 0;
					$window.location.href = base_url+'welcome/chasingPaperwork';
				}
				 else{
				 	toaster.pop("error", data.message, "");
				 }
			});
		}
    };


// Ignore Clients 
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
		get_performer();
	};

    
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

