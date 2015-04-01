app.factory("About", ["WPRest", "$sce", function(WPRest, $sce) {

	//WPRest.restCall("/", "GET", {}, "WPRestWorks");

	

	var aboutServant = {
		find: function(searchParams) {

			searchParams = searchParams ? searchParams : {};

			var callUrl = "/posts?filter[category_name]=om-oss";

			var first = true;

			for (var i in searchParams) {

			  if (searchParams[i].constructor.name != "Object") {

			  	callUrl += first ?
			  	"?filter["+i+"]="+searchParams[i] :
			  	"&filter["+i+"]="+searchParams[i];
			  } else {

					for (var j in searchParams[i]) {
					callUrl += first ?
					"?filter["+i+"]["+j+"]="+searchParams[i][j] :
					"&filter["+i+"]["+j+"]="+searchParams[i][j];
					first = false;			  		
				  	}
			 	}

			 	first = false;
				
			}
			console.log("om-oss metoden ropar på REST url: ", callUrl);


			WPRest.restCall(callUrl, "GET", {}, {
			  broadcastName: "Important NOT!",
			  callback: function(postData) {

			  	//	console.log("Aboutfactory hittade om-oss poster: ", postData);

			  	for (var i = postData.length -1; i > 0; i--) {
					if (!postData[i].terms.about) {
						postData.splice(i, 1);
				    }
				}
			  

			  	var resultsToBroadcast = [];

			  	var i = 0;
			  	postData.forEach(function(post){
				  		var last = i === postData.length-1;
				  		//if (post.terms.about) {

							post.excerpt = $sce.trustAsHtml(post.excerpt);
						  	post.content = $sce.trustAsHtml(post.content);

						  	var aboutTag = post.terms.about[0].slug;

						  	var mediaCallUrl = "/media?filter[about]="+aboutTag;

						WPRest.restCall(mediaCallUrl, "GET", {}, {
							broadcastName: last ? "foundAbout" : "notDone", 
							callback: function(mediaData) {
							 //console.log("Hittade om-oss media: ", mediaData);

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
	//return our factory object
	return aboutServant;
}]);