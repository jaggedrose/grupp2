app.controller("bostadsListController", ["$scope", "Bostad", "SITE_INFO", "$routeParams", "$location", function ($scope, Bostad, SITE_INFO, $routeParams, $location) {
	// console.log("bostadsListController is alive! params: ", $routeParams);


	$scope.partialsDir = SITE_INFO.partials;


	$scope.bostadPrisFilter = {
		prisKlass : []
	};

    $scope.bostadYta_m2Filter = {
        yta_m2Klass : []
    };

	$scope.filterReset = function() {
		$scope.bostadPrisFilter = {
			prisKlass : []
		};

        $scope.bostadYta_m2Filter = {
            yta_m2Klass : []
        };
	};


	$scope.carouselInterval = 5000;

	var pageNo = 1;
	Bostad.find($routeParams);
	$scope.$on("foundBostad", function(event, data) {

		// console.log("BostadsController on foundBostad: ", data);
		$scope.bostadModels = data;
		pageNo++;
		Bostad.find($routeParams, pageNo);
	});

	$scope.goTo = function(url) {
		$location.url(url);
	};

}]);