"use strict";

ymaps.ready(function (){
  var $map = $("#map");
  var map = new ymaps.Map($map[0], {
    center: params.coords,
    zoom: params.zoom,
    controls: ["zoomControl"]
  });
  map.behaviors.disable("scrollZoom");
  var placemark = new ymaps.Placemark(params.coords, {}, {
    iconLayout: 'default#image',
    iconImageHref: '/template/images/map_marker.png',
    iconImageSize: [32, 32],
    iconImageOffset: [-16, -32],
    hasBalloon: false,
    cursor: "default"
  });
  map.geoObjects.add(placemark);
  $map.find(".map_loading").hide();
});