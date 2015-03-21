app.factory("Bostad", ["WPRest", "$sce", function(WPRest, $sce) {
	
 //WPRest.restCall("/", "GET", {}, "WPRestWorks");

 var bostadServant = {
 find : function(searchParams) {
			searchParams = searchParams ? searchParams : {};

			var callUrl = "/posts?filter[category_name]=bostad";

			for (var i in searchParams) {
				callUrl += "&filter["+i+"]="+searchParams[i];
			}
			console.log("Hitta bostad metoden ropar på REST url: ", callUrl);

			WPRest.restCall(callUrl, "GET", {}, {
				broadcastName: "KEBAAHB",
				callback: function(postData) {

					console.log("Bostad hittade bostad posts: ", postData);

					var resultsToBroadcast = [];

					var i = 0;
					postData.forEach(function(post) {
						var last = i === postData.length-1; /*WATH*/
						if (!post.terms.bostad) {return;} /*WHAT*/

						post.excerpt = $sce.trustAsHtml(post.excerpt);
						post.content = $sce.trustAsHtml(post.content);

						var bostadTag = post.terms.bostad[0].slug;

						var mediaCallUrl = "/media?filter[bostad]="+bostadTag;
						WPRest.restCall(mediaCallUrl, "GET", {}, {
							broadcastName: last ? "foundBostad" : "notDone",
							callback: function(mediaData) {
								console.log("Bostad hittade bostad media: ", mediaData);

								resultsToBroadcast.push({
									"media": mediaData,
									"post": post,
									"bostadData": post.bostad_data
								});

								if (last) {
									return resultsToBroadcast;
								}
							}
						});

						i++;
						//console.log("i = "+ i);
					});

				}
			});
		}
	};


	return bostadServant;
}]);