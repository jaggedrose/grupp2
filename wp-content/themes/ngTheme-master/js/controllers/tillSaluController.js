// app.controller("tillSaluController", ["$scope", "Pages", "$sce", "$routeParams", function($scope, Pages, $sce, $routeParams) {
// 	console.log("tillSaluController Working! routeParams: ", $routeParams);
// 	// Get the page
// 	Pages.get($routeParams.slug);

// 	// Check for broadcast
// 	$scope.$on("gotPageData", function(event, data) {
// 		console.log("tillSaluController on gotPageData: ", data);
// 		$scope.page = data[0];
// 	});
// }]);