app.controller("bostadsListController", ["$scope", "Bostad", "SITE_INFO", "$routeParams", "$location", function ($scope, Bostad, SITE_INFO, $routeParams, $location) {
	console.log("bostadsListController is alive! params: ", $routeParams);

	Bostad.find($routeParams);

	$scope.partialsDir = SITE_INFO.partials;

	$scope.carouselInterval = 5000;

	$scope.$on("foundBostad", function(event, data) {

		console.log("BostadsController on foundBostad: ", data);
		$scope.bostadModels = data;
	});

	$scope.goTo = function(url) {
		$location.url(url);
	};
}]);