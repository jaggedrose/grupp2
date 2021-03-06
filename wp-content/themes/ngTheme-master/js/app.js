//app declaration and dependency injection
var app = angular.module("ngTheme", ["ngRoute", "ui.bootstrap"]);
//app config
app.config(["$routeProvider", "$locationProvider", "SITE_INFO", function($routeProvider, $locationProvider, SITE_INFO) {
  //route config
  $routeProvider
    .when("/", {
      templateUrl: SITE_INFO.partials+"views/home.html",
      controller: "homeController"
    })
    .when("/pages/till-salu", {
      templateUrl: SITE_INFO.partials+"views/tillSalu.html",
      controller: "tillSaluController"
    })
    .when("/pages/om-oss", {
      templateUrl: SITE_INFO.partials+"views/about.html",
      controller: "aboutController"
    })
    .when("/pages/kontakt", {
      templateUrl: SITE_INFO.partials+"views/contact.html",
      controller: "contactController"
    })
    .when("/bostad/:name", {
      templateUrl: SITE_INFO.partials+"views/bostad.html",
      controller: "bostadsController"
    })
    .otherwise({
      redirectTo: "/"
    });

  $locationProvider.html5Mode(true);
}])
.constant('SITE_INFO', myLocalized)
.constant('API_ROUTE', "wp-json");