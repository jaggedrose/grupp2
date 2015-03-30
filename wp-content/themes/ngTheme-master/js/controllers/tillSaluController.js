app.controller("tillSaluController", ["$scope", "Pages", "Bostad", "$sce", "$routeParams", function($scope, Pages, Bostad, $sce, $routeParams) {
	console.log("tillSaluController Working! routeParams: ", $routeParams);
	// Get the page
	Pages.get("till-salu");

	// Check for broadcast
	$scope.$on("gotPageData", function(event, data) {
		console.log("tillSaluController on gotPageData: ", data);
		$scope.page = data[0];
	});

	$scope.bostadFilters = {
		Stad : ""
 	};
 	Bostad.find($routeParams);	
}]);