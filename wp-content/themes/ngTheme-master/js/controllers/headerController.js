//"ngTheme" controller.
app.controller("headerController", ["$scope", "$location", "Menus", "SITE_INFO", function($scope, $location, Menus, SITE_INFO) {
	$scope.partialsDir = SITE_INFO.partials;

	console.log("headerController is alive!");
	console.log("SITE_INFO: ", SITE_INFO);

	//get the menuLinks for menuId 2 using WPRest
	Menus.get(2);

	//listen to the broadcast "gotMenuLinks"
	$scope.$on("gotMenuLinks", function(event, data) {
		console.log("headerController on gotMenuLinks: ", data);
		$scope.menuLinks = data.items;
	});


	//a simple $scope method for changing urls using ng-click in views
	$scope.goTo = function(url, hardReload) {
		//any relative path destined for hardReload 
		//gets http_root instead of initial "/"
		if (hardReload) {
			url = url.indexOf("/") === 0 ?
				SITE_INFO.http_root + url.substr(1) :
				SITE_INFO.http_root + url;
		}

		if (hardReload) {
			//hard reloads use traditional JS window.location.href 
			//to change url
			window.location.href = url;
			return;
		}

		//all "soft" reloads (location change within app) use
		//angulars $location.url() to change url using push/pop-state
		$location.url(url);
	};

}]);