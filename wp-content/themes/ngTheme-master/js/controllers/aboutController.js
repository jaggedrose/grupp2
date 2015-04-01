app.controller("aboutController", ["$scope", "Pages", "$sce", "$routeParams", function($scope, Pages, $sce, $routeParams) {
	// console.log("aboutController Working! routeParams: ", $routeParams);
	// Get the page
	Pages.get("om-oss");

	// Check for broadcast
	$scope.$on("gotPageData", function(event, data) {
		// console.log("aboutController on gotPageData: ", data);
		if (data.length === 0) {return;}
		$scope.page = data[0];
	});
}]);