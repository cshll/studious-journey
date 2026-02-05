var liveMap = L.map('live-map', {
  zoomControl: false
}).setView([53.46, -2.28], 13);

L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
  maxZoom: 19,
  attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
}).addTo(liveMap);

L.control.locate({
  position: 'bottomright'
}).addTo(liveMap);

L.control.zoom({
  position: 'bottomright'
}).addTo(liveMap);

const busMarkers = {};
const busPaths = {};
const socket = io('http://localhost:3000');

socket.on('initRoutes', (allRoutes) => {
  Object.keys(allRoutes).forEach(routeId => {
    const routeData = allRoutes[routeId];
    const latLngs = routeData.path.map(coord => [coord[1], coord[0]]);

    L.polyline(latLngs, {
      color: routeData.color,
      weight: 4,
      opacity: 0.7
    }).addTo(liveMap);
  });
});

socket.on('busesUpdate', (buses) => {
  buses.forEach(bus => {
    if (busMarkers[bus.id]) {
      busMarkers[bus.id].setLatLng([bus.lat, bus.lng]);
    } else {
      const customIcon = L.divIcon({
        className: 'bus-icon-div',
        iconSize: [30, 30],
        iconAnchor: [15, 15],
        popupAncor: [0, -15],

        html: `<div class="bus-marker-circle" style="background-color: ${bus.color};">${bus.route}</div>`
      });

      const marker = L.marker([bus.lat, bus.lng], { icon: customIcon }).addTo(liveMap);
      marker.bindPopup(`
              <b>${bus.id}</b><br>
              Route: ${bus.route}
            `);

      busMarkers[bus.id] = marker;
    }
  });
});
