app.controller("contactController", ["$scope", "Pages", "$sce", "$routeParams", function($scope, Pages, $sce, $routeParams) {
	console.log("contactController WOrking! routeParams: ", $routeParams);
	// Get the page
	Pages.get($routeParams.slug);

	// Check for broadcast
	$scope.$on("gotPageData", function(event, data) {
		console.log("contactController on gotPageData: ", data);
		$scope.page = data[0];
	});
}]);