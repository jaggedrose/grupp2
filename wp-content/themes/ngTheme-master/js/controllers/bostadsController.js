
app.controller("bostadsController", ["$scope", "Bostad" ,"$routeParams", "SITE_INFO", function($scope, Bostad, $routeParams, SITE_INFO) {
	// console.log("bostadsController is alive! params: ", $routeParams);

	Bostad.find($routeParams);
	$scope.partialsDir = SITE_INFO.partials;

	$scope.carouselInterval = 5000;

	$scope.$on("foundBostad", function(event, data) {
		// console.log("bostadsController on foundBostad: ", data);
		if (data.length === 0) {return;}
		$scope.bostad = data[0];
	});
}]);