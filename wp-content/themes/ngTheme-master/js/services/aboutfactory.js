app.factory("About", ["WPRest", "$sce", function(WPRest, $sce) {

	//WPRest.restCall("/", "GET", {}, "WPRestWorks");

	

	var aboutServant = {
		find: function(searchParams) {

			searchParams = searchParams ? searchParams : {};

			var callUrl = "/posts?filter[category_name]=om-oss";

			for (var i in searchParams) {
				callUrl += "&filter["+i+"]="+searchParams[i];
			}
			console.log("Hitta om-oss metoden ropar på REST url: ", callUrl);


			WPRest.restCall(callUrl, "GET", {}, {
			  broadcastName: "about_data",
			  callback: function(postData) {
			  	console.log("Aboutfactory hittade om-oss poster: ", postData);

			  	var resultsToBroadcast = [];

			  	var i = 0;
			  	postData.forEach(function(post){
			  		var last = i === postData.length-1;
			  		if (!post.terms.about) {return;}

				  post.excerpt = $sce.trustAsHtml(post.excerpt);
			  	  post.content = $sce.trustAsHtml(post.content);

			  	  var aboutTag = post.terms.about[0].slug;

			  	  var mediaCallUrl = "/media?filter[about]="+aboutTag;
				  WPRest.restCall(mediaCallUrl, "GET", {}, {
				  broadcastName: last ? "foundAbout" : "notDone",
					callback: function(mediaData) {
					// console.log("about hittade about media: ", mediaData);

			  	 // broadcastName: "about";
			  	  resultsToBroadcast.push({
			  	  	"media": mediaData,
			  	  	"post": post,
			  	  	"aboutData": post.about_data
			  	});
			  	if (last) {
					return resultsToBroadcast;
				}
			  	}
			  });
			  	
			  	i++;

			  	});

			  }
			
			});

		}

	};

	return aboutServant;

}]);
