app.filter('prisKlass', [function () {
  return function (bostads, range) {
    if (!bostads) { return; }
     console.log("bostads: ", bostads);
    
    var filtered = [];
    
    if (range.length === 0) {
      return bostads;
    }

    for (var i = 0; i < bostads.length; i++) {
      var bostad = bostads[i];
      bostad.bostadData.pris = bostad.bostadData.pris / 1;

      if (
        range[0] && range[1] &&
        bostad.bostadData.pris >= range[0] &&
        bostad.bostadData.pris <= range[1]) {

        filtered.push(bostad);
      } else if (
        range[0] && !range[1] &&
        bostad.bostadData.pris >= range[0]) {

        filtered.push(bostad);
      } else if (
        !range[0] && range[1] &&
        bostad.bostadData.pris <= range[1]) {

        filtered.push(bostad);
      } else if (!range[0] && !range[1]) {

        filtered.push(bostad);
      }
    }

    return filtered;
  };
}]);