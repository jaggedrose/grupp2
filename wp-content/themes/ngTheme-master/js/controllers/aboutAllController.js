app.controller("aboutAllController", ["$scope", "About", "SITE_INFO", "$routeParams", "$location", function ($scope, About, SITE_INFO, $routeParams, $location) {
	console.log("aboutAllController is alive! params: ", $routeParams);

	About.find($routeParams);

	$scope.partialsDir = SITE_INFO.partials;


	$scope.$on("foundPosts", function(event, data) {

		console.log("aboutAllController on foundPosts: ", data);
		$scope.aboutModels = data;
	});

	$scope.goTo = function(url) {
		$location.url(url);
	};
}]);