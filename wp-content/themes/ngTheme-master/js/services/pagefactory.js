//our .factory() service for "Pages" rest calls
app.factory("Pages", ["WPRest", "$sce", function (WPRest, $sce) {
  //in a .factory() service object literal syntax is required
  var pageServant = {
    get : function(pageSlug) {
      var callUrl = pageSlug ? "/pages?filter[name]="+pageSlug : "/pages";

      WPRest.restCall(callUrl, "GET", {}, {
        broadcastName : "gotPageData",
        callback : function(data) {
          // Make html trusted, to make angular happy
          if (data.constructor.name == "Array") {
            data.forEach(function(item) {
              item.customContent = {};
              if (pageSlug == "hem") {

                // Testing - Converting content back to string
                var allContent = item.content;
                
                // Spliting the content into sections
                var splitString = allContent.split("</p>");

                item.customContent.aboutus = $sce.trustAsHtml(splitString[0]);
                item.customContent.contact = $sce.trustAsHtml(splitString[1]);
                item.customContent.forsale = $sce.trustAsHtml(splitString[2]);

              }

              item.excerpt = $sce.trustAsHtml(item.excerpt);
              item.content = $sce.trustAsHtml(item.content);
            });
          } else {
            data.excerpt = $sce.trustAsHtml(data.excerpt);
            data.content = $sce.trustAsHtml(data.content);
          }
          return data;
        }
      });
    },
    post : function(data) {
      var callUrl = "/pages";
      WPRest.restCall(callUrl, "POST", data, "savedNewPage");
    },
    put : function(pageId, data) {
      var callUrl = "/pages/"+pageId;
      WPRest.restCall(callUrl, "PUT", data, "updatedPage");
    },
    delete : function(pageId) {
      var callUrl = "/pages/"+pageId;
      WPRest.restCall(callUrl, "DELETE", {}, "deletedPage");
    }
  };

  //.factory() services MUST return an object
  return pageServant;
}]);