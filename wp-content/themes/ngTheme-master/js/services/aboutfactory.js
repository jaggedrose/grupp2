app.factory("About", ["WPRest", "$sce", function(WPRest, $sce) {

	WPRest.restCall("/", "GET", {}, "WPRestWorks");


	var aboutServant = {
		find: function(searchParams) {

			searchParams = searchParams ? searchParams : {};

			var callUrl = "/posts?filter[category_name]=om-oss";

			for (var i in searchParams) {
				callUrl += "&filter["+i+"]="+searchParams[i];
			}
			console.log("Hitta om-oss metoden ropar på REST url: ", callUrl);

			WPRest.restCall(callUrl, "GET", {}, {
			  broadcastName: "Om-oss",
			  callback: function(postData) {
			  	console.log("Aboutfactory hittade om-oss poster: ", postData);

			  	var resultsToBroadcast = [];
			  	var i = 0;
			  	postData.forEach(function(post){

				  post.excerpt = $sce.trustAsHtml(post.excerpt);
			  	  post.content = $sce.trustAsHtml(post.content);

			  	  resultsToBroadcast.push({
			  	  	"post": post,
			  	  	"aboutData": post.about_data
			  	});
			  
			  	return resultsToBroadcast;
			  	
			  	i++;

			  	});

			  }
			
			});

		}

	};

	return aboutServant;

}]);

