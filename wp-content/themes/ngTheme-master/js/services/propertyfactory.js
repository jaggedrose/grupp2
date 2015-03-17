app.factory("Property", ["WPRest", "$sce", function (WPRest, $sce) {
  //all factory services return data (ex. object)
  var propertyServant = {
    find : function(params) {
      //if no params, make empty obj so we dont crash!
      params = params ? params : {};

      //searching with WP JSON REST API filter parameters
      //always only search for properties
      var callUrl = "/posts?filter[category_name]=properties";
      
      //and add any additional parameters
      for (var j in params) {
        callUrl += "&filter[" + j + "]=" + params[j];
      }
      console.log("Property searching: " + callUrl);

      /*
        1. Get all posts that match the search criteria
      */
      WPRest.restCall(callUrl, "GET", {}, {
        broadcastName: "bananName",
        callback: function(postData) {

          //if no results
          if (!postData) { return; }
          console.log("postData: ", postData);

          var results = [];
          console.log("last: ", i, " count ", postData.length);

          /*
            2. Then, for each post, get any media that has the same
               property (taxonomy) tag
          */
          for (var i = 0; i < postData.length; i++) {
            //using a function to handle async reference problems
            function asyncHandler() {
              var myI = i;
              console.log("last: ", myI, " count ", postData.length);
              var last = myI === postData.length-1;
              var post = postData[myI];
              var propertySlug = post.terms.property ?
                post.terms.property[0].slug :
                false;

              //if this property has no "slug" (machine name), return
              if (!propertySlug) { return; }

              //make post html trusted
              post.excerpt = $sce.trustAsHtml(post.excerpt);
              post.content = $sce.trustAsHtml(post.content);

              //new call url for media search query
              var callUrl = "/media?filter[property]=" + propertySlug;

              WPRest.restCall(callUrl, "GET", {}, {
                broadcastName: last ? "foundProperty" : "notDone",
                callback: function(mediaData) {
                  /*
                    3. And create a new property model from all pieces
                       then push it to the results array

                    All propertyModels look the same:
                    {
                      "post" : { ... },
                      "propertyData" : { ... },
                      "media" : [ ... ]
                    }
                  */
                  results.push({
                    "post" : post,
                    "propertyData" : post.property_data,
                    "media" : mediaData
                  });

                  if (last) {
                    /*
                      3. When last search has been completed, 
                         return results to $broadcast
                    */
                    return results;
                  }
                }
              });
            }
            //this function calls is called for every loop
            asyncHandler();
          }
        }
      });
    }
  };

  return propertyServant;
}]);