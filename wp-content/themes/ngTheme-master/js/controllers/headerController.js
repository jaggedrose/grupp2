//"ngTheme" controller.
app.controller("headerController", ["$scope", "Menus", "$location", function($scope, Menus, $location) {
	console.log("headerController is alive!");
	// Get all menus
	Menus.get(2);

	// Menu broadcast from WPRest
	$scope.$on("gotMenuData", function(event, data) {
		console.log("headerController on gotMenuData: ", data);
	});

}]);