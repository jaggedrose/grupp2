app.filter('yta_m2Filter', [function () {
  return function (bostads, range) {
    if (!bostads) { return; }
     // console.log("bostads: ", bostads);
     // console.log("range: ", range);

    var filtered = [];

    if (range.length === 0) {
      return bostads;
    }

    for (var i = 0; i < bostads.length; i++) {
      var bostad = bostads[i];
      bostad.bostadData.yta_m2 = bostad.bostadData.yta_m2 / 1;

      if (
        range[0] && range[1] &&
        bostad.bostadData.yta_m2 >= range[0] &&
        bostad.bostadData.yta_m2 <= range[1]) {

        filtered.push(bostad);
      } else if (
        range[0] && !range[1] &&
        bostad.bostadData.yta_m2 >= range[0]) {

        filtered.push(bostad);
      } else if (
        !range[0] && range[1] &&
        bostad.bostadData.yta_m2 <= range[1]) {

        filtered.push(bostad);
      } else if (!range[0] && !range[1]) {

        filtered.push(bostad);
      }
    }

    return filtered;
  };
}]);