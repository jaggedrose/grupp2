//"ngTheme" home controller.
//dependent on $scope && WPService being injected to run
app.controller("homeController", ["$scope", "Pages", "$sce", "$routeParams", function($scope, Pages, $sce, $routeParams) {
	console.log("homeController alive! routeParams: ", $routeParams);
	
	//get page
	Pages.get("hem");

	$scope.carouselInterval = 5000;

	// EXAMPLE LISTENER TO A $broadcast COMING FROM WPRest SERVICE!!!
	//listening for the "gotPageData" broadcast on $http success
	$scope.$on("gotPageData", function(event, data) {
		console.log("homeController on gotPageData: ", data);
		$scope.page = data[0];
	});
  
}]);