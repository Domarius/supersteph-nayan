var app = angular.module("myApp", ['toaster','ngFileUpload', 'ui.select', 'ui.bootstrap']);

app.controller("BookPerformerController", function($scope, $http, toaster, $location, $window, $timeout, Upload, $filter) { 
    
	$scope.result={};
	$scope.get={};
	$scope.listing={};
	$scope.parameters={};							//initialize parameters to be send
	$scope.parameters.limit=100;						//initialize pagination default limit to 10
	$scope.parameters.page=1;						//initialize first page value to 1 for pagination
	$scope.start=$scope.end=$scope.total=0;			//initialize start to 0 for first result
	
	$scope.result.status=-1;

	$scope.durationListing=[];

	$scope.durationListing=[
			{"label":"15 Min", "value":"15 Min"},
		{"label":"30 Min", "value":"30 Min"},
		{"label":"45 Min", "value":"45 Min"},
		{"label":"1 Hours", "value":"1 Hours"},
		{"label":"1 Hours 15 Minute", "value":"1 Hours 15 Minute"},
		{"label":"1 Hours 30 Minute", "value":"1 Hours 30 Minute"},
		{"label":"1 Hours 45 Minute", "value":"1 Hours 45 Minute"},
		{"label":"2 Hours", "value":"2 Hours"},		
		{"label":"2 Hours 15 Minute", "value":"2 Hours 15 Minute"},
		{"label":"2 Hours 30 Minute", "value":"2 Hours 30 Minute"},
		{"label":"2 Hours 45 Minute", "value":"2 Hours 45 Minute"},
		{"label":"3 Hours", "value":"3 Hours"},		
		{"label":"3 Hours 15 Minute", "value":"3 Hours 15 Minute"},
		{"label":"3 Hours 30 Minute", "value":"3 Hours 30 Minute"},
		{"label":"3 Hours 45 Minute", "value":"3 Hours 45 Minute"},
		{"label":"4 Hours", "value":"4 Hours"},		
		{"label":"4 Hours 30 Minute", "value":"4 Hours 30 Minute"},
		{"label":"5 Hours", "value":"5 Hours"},		
		{"label":"5 Hours 30 Minute", "value":"5 Hours 30 Minute"},
		{"label":"6 Hours", "value":"6 Hours"},		
		{"label":"6 Hours 30 Minute", "value":"6 Hours 30 Minute"},
		{"label":"7 Hours", "value":"7 Hours"},		
		{"label":"7 Hours 30 Minute", "value":"7 Hours 30 Minute"},
		{"label":"8 Hours", "value":"8 Hours"},	
	];

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


	get_category();
	function get_category(){
		$http.post('../categoryList').success(function(data){
			$scope.categoryListing=data.response;
			//console.log($scope.categoryListing);
		});
	}

	get_performer();
	function get_performer(){
		$http.post('../unblockPerformer', $scope.parameters).success(function(data){
			update(data.response.pop().total);
			$scope.listing=data.response;
		});
	}

	$scope.searchFunction = function(searchData){
		$scope.parameters.searchtype = searchData;
		$scope.parameters.category_id = "";
		$scope.parameters.page=1;
		get_performer();
    };

    $scope.searchCategoryFunction = function(category_id){
    	$scope.parameters.searchtype = "";
		$scope.parameters.category_id = category_id;
		$scope.parameters.page=1;
		get_performer();
    };

    $scope.removeSearch = function(){
		$scope.searchData="";
		$scope.category_id="";
		$window.location.href = base_url+'welcome/bookPerformer/?'+id+'/?'+page_id; 
    };


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

	$scope.assignPerformerName = function(){
		
//$scope.isDisabled = true;
		$http.post('../assignPerformerName', {'performer_id': $scope.SelectID, 'booking_id': id}).success(function(data){
			$scope.listingPerformer = data.response;
		});
		$http.post('../categoryList').success(function(data){
			$scope.categoryListing=data.response;
			console.log($scope.categoryListing);
		});

		$http.post('../assignBookingName', {'booking_id': id}).success(function(data){
			$scope.listingBooking = data.response;

			$scope.give_start_time = $scope.listingBooking['0']['show_time'];
			$scope.give_end_time = $scope.listingBooking['0']['show_end_time'];
			
		});
    };

    $scope.compareStartTime = function(changeStartTime){
    
	    $scope.take_start_time = $scope.give_start_time;
	    $scope.take_end_time = $scope.give_end_time;
	    $scope.change_start_time = $filter('date')(new Date(changeStartTime), 'HH:mm'+':00');

	    if($scope.change_start_time >=  $scope.take_start_time && $scope.change_start_time <= $scope.take_end_time){
	      $scope.time_start_true = "Yes";
	    }
	    else{
	      $scope.time_start_true = "No";
	    }
  	}

  	$scope.compareEndTime = function(changeEndTime){
    
	    $scope.take_start_time = $scope.give_start_time;
	    $scope.take_end_time = $scope.give_end_time;
	    $scope.change_end_time = $filter('date')(new Date(changeEndTime), 'HH:mm'+':00');

	    if($scope.change_end_time >=  $scope.take_start_time && $scope.change_end_time <= $scope.take_end_time){
	      $scope.time_end_true = "Yes";
	    }
	    else{
	      $scope.time_end_true = "No";
	    }
  	}

    $scope.hstep = 1;
  	$scope.mstep = 1;
  	//$scope.ismeridian = true;
 $scope.isDisabled = false;
    $scope.disableClick = function() {
        // alert("Clicked!");
        $scope.isDisabled = true;
        return false;
    }


	$scope.assignPerformer = function(){

$scope.isDisabled = true;
		//if(confirm("Do you want to assign performar?")){
			$http.post('../bookPerformer', {'performer_id': $scope.listingPerformer, 'booking_request_id': id}).success(function(data){
				$scope.result = data;
				if(data.status == 0){
					toaster.pop("success", data.message, "");
					$scope.delete_count = 0;
					$timeout(function() {  
	                  	$window.location.href = base_url+'welcome/editBookAssignPerformer/?'+id+'/?'+page_id; 
	                }, 900); 
				}
				else{
					toaster.pop("error", data.message, "");
					$timeout(function() {  
	                  	$window.location.href = base_url+'welcome/bookPerformer/?'+id+'/?'+page_id; 
	                }, 900); 
				}
			});
	//	}
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

