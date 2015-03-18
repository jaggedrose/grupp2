// .factory() service for "Menus" rest call.
app.factory("Menus", ["WPRest", "SITE_INFO", function(WPRest, SITE_INFO) {

	// Using Menu tree function from bootstrap_play
	function createMenuTree(menuLinkArr) {
		// The menu tree (our new deep structure)
		var menuTree = [];

		// Sort by order
		menuLinkArr.sort(function(x,y){
		return x.order > y.order;
		});

		//since JS always assigns properties by reference
		//we can use separate variables to track the same objects in menuData 
		//(all our menu links)
		var hash = {};

		// Loop through original menuData, add all links to hash map,
		//and add all top level links to the menuTree
		menuLinkArr.forEach(function(link) {
			//with a small modification 
			//(removes http://{{site root}}/ from link urls)
			link.url = link.url.replace(SITE_INFO.http_root, "");

			//give each menu link a new property called children
			link.children = [];

			//track each link using our hash map
			hash["_"+link.ID] = link;

			// if i am top level, add to tree right away
			if(link.parent === 0){
				//remember: 
				menuTree.push(link);
				return;
			}
		});

		//then add children to all menu_links using the hash map
		//(so we don't have to loop through any number of sublevels)
		for(var i in hash){
			link = hash[i];
			//if no parent (no parent), skip this iteration of the loop
			if(link.parent === 0){continue;}

			//add me to menuTree using the hash map "shortcut" to each link
			hash["_"+link.parent].children.push(link);
		}

		console.log("menuTree: ", menuTree);
		return menuTree;
	}

	//the callback function for GET requests with a menuId
	//that converts menu.items from a "flat" array into a tree
	function prepareMenu(menuObj) {
		menuObj.items = createMenuTree(menuObj.items);

		return menuObj;
	}

	//our factory object
	var menuServant = {
		get : function(menuId) {
			var callUrl = menuId ? "/menus/" + menuId : "/menus";

			var broadcastInstructions = menuId ?
				//only use callback function if we are asking to a specific menu
				//using a menuId
				{
					broadcastName : "gotMenuLinks",
					callback: prepareMenu
				} :
				//else only provide the broadcast name
				"gotMenus";

			WPRest.restCall(callUrl, "GET", {}, broadcastInstructions);
		}
	};

	return menuServant;
}]);