//"ngTheme" home controller.
//dependent on $scope && WPService being injected to run
app.controller("homeController", ["$scope", "Pages", "Bostad", "$sce", "$routeParams", "SITE_INFO", function($scope, Pages, Bostad, $sce, $routeParams, SITE_INFO) {
	console.log("homeController alive! routeParams: ", $routeParams);
	

	Bostad.find($routeParams);
	$scope.partialsDir = SITE_INFO.partials;

	$scope.carouselInterval = 5000;

	$scope.$on("foundBostad", function(event, data) {
		console.log("bostadsController on foundBostad: ", data);
		if (data.length === 0) {return;}
		$scope.bostad = data[0];
	});


	//get page
	Pages.get("hem");

	// EXAMPLE LISTENER TO A $broadcast COMING FROM WPRest SERVICE!!!
	//listening for the "gotPageData" broadcast on $http success
	$scope.$on("gotPageData", function(event, data) {
		console.log("homeController on gotPageData: ", data);
		$scope.page = data[0];
	});
  
}]);