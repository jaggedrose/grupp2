//custom directive to apply background images to elements
app.directive('backImg', function(){
  return function(scope, element, attrs){
    attrs.$observe('backImg', function(value) {
      element.css({
          'background-image': 'url(' + value +')',
          'background-size' : 'cover'
      });
    });
  };
});