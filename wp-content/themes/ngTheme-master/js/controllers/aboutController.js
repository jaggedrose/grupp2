app.controller("aboutController", ["$scope", "Pages", "$sce", "$routeParams", function($scope, Pages, $sce, $routeParams) {
	console.log("aboutController Working! routeParams: ", $routeParams);
	// Get the page
	Pages.get($routeParams.slug);

	// Check for broadcast
	$scope.$on("gotPageData", function(event, data) {
		console.log("aboutController on gotPageData: ", data);
		$scope.page = data[0];
	});
}]);