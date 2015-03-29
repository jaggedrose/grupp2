//"ngTheme" home controller.
//dependent on $scope && WPService being injected to run
app.controller("homeController", ["$scope", "Pages", "Bostad", "$sce", "$filter", "$routeParams", "SITE_INFO", function($scope, Pages, Bostad, $sce, $filter, $routeParams, SITE_INFO) {
	// console.log("homeController alive! routeParams: ", $routeParams);
	

	Bostad.find($routeParams);
	
	$scope.carouselInterval = 5000;

	$scope.$on("foundBostad", function(event, data) {
		// console.log("bostadsController on foundBostad: ", data);
		
		var mediaItems = [];
		for(var i = data.length - 1; i > -1 && i > data.length - 6; i--) {
			mediaItems.push(data[i].media[data[i].media.length - 1]);
		}

		$scope.mediaItems = mediaItems;
		// console.log(mediaItems);
	});


	// Get page
	Pages.get("hem");

	// EXAMPLE LISTENER TO A $broadcast COMING FROM WPRest SERVICE!!!
	//listening for the "gotPageData" broadcast on $http success
	$scope.$on("gotPageData", function(event, data) {
		console.log("homeController on gotPageData: ", data);
		$scope.page = data[0];

		// Testing - Converting content back to string
		var allContent = data[0].content;
		var allContentString = allContent.toString();
		
		// Spliting the content into sections
		var splitString = allContentString.split("</p>");
		
		$scope.aboutus = $sce.trustAsHtml(splitString[0]);
		$scope.contact = $sce.trustAsHtml(splitString[1]);
		$scope.forsale = $sce.trustAsHtml(splitString[2]);

	});

}]);
