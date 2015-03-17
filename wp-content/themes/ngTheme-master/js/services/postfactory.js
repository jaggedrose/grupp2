//our .factory() service for "Pages" rest calls
app.factory("Posts", ["WPRest", function (WPRest) {
  //in a .factory() service object literal syntax is required
  var pageServant = {
    get : function(pageId) {
      var callUrl = pageId ? "/posts/"+pageId : "/posts";
      WPRest.restCall(callUrl, "GET", {}, "gotPostData");
    },
    post : function(data) {
      var callUrl = "/posts";
      WPRest.restCall(callUrl, "POST", data, "savedNewPost");
    },
    put : function(pageId, data) {
      var callUrl = "/posts/"+pageId;
      WPRest.restCall(callUrl, "PUT", data, "updatedPost");
    },
    delete : function(pageId) {
      var callUrl = "/posts/"+pageId;
      WPRest.restCall(callUrl, "DELETE", {}, "deletedPost");
    }
  };

  //.factory() services MUST return an object
  return pageServant;
}]);