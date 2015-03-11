//a "core" service that handles all rest calls to WordPress
app.service("WPRest", ["$http", "$rootScope", "SITE_INFO", "API_ROUTE", function($http, $rootScope, SITE_INFO, API_ROUTE) {
  var APIPath = SITE_INFO.http_root + API_ROUTE;
  console.log("APIPath: ", APIPath);

  //in a .service() service this syntax is preferred
  this.restCall = function(url, method, data, broadcastInstructions) {
    //using the "real" $http, instead of shorthands such as $http.get() etc.
    $http({
      url: APIPath + url,
      method: method,
      data: data,
      responseType: "json"
    }).success(function(data) {
      console.log("WPRest restCall success: ", data, " broadcast instructions: ", broadcastInstructions);

      if (typeof broadcastInstructions == "object") {
        // If broadcastInstructions is an object, it contains callback instruction
        $rootScope.$broadcast(
          broadcastInstructions.broadcastName, // Name to broadcast on
          broadcastInstructions.callback(data) // Function to call BEFORE broadcasting
        );
      } else {
        // Using $rootScope.$broadcast() to broadcast throughout our app (module)
        // to any $on() listeners in controllers and services
        broadcastInstructions = broadcastInstructions ? broadcastInstructions : "restSuccess";

        $rootScope.$broadcast(
          broadcastInstructions, // Name to broadcast on
          data // Data to broadcast
        );
      }
    });
  };
}]);