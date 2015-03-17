//"ngTheme" home controller.
//dependent on $scope && WPService being injected to run
app.controller("homeController", ["$scope", "Posts", "$sce", function($scope, Posts, $sce) {
  console.log("homeController alive!");
  //get all pages
  Posts.get();

  // EXAMPLE LISTENER TO A $broadcast COMING FROM WPRest SERVICE!!!
  //listening for the "gotPageData" broadcast on $http success
  $scope.$on("gotPostData", function(event, data) {
    console.log("homeController on gotPostData: ", data);

    /* 
      angular protects us from "dangerous" HTML by converting it to a string

      if we want to show HTML from a string in DOM 
      we first need to tell angular that it can be trusted.
      
      this is done using the $sce service on the HTML string in JS
      and the ng-bind-html directive in the view
    */
    $scope.trustedHtml = $sce.trustAsHtml(data[0].content);
  });
  
}]);