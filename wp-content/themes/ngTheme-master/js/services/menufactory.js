// .factory() service for "Menus" rest calls.
app.factory("Menus", ["WPRest", function (WPRest) {

	var menuServant = {
		get : function(menuId) {
			var callUrl = menuId ? "/menus/"+menuId : "/menus";
			WPRest.restCall(callUrl, "GET", {}, "gotMenuData");
		},
		post : function(data) {
			var callUrl = "/menus";
			WPRest.restCall(callUrl, "POST", data, "savedNewMenu");
		},
		put : function(menuId, data) {
			var callUrl = "/menus/"+menuId;
			WPRest.restCall(callUrl, "PUT", data, "updatedMenu");
		},
		delete : function(menuId) {
			var callUrl = "/menus/"+menuId;
			WPRest.restCall(callUrl, "DELETE", {}, "deletedMenu");
		}
	};
	//.factory() services MUST return an object
	return menuServant;
}]);