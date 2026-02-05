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

let storedRoutes = {};
let activeRouteLine = null;

const socket = io('http://localhost:3000');

socket.on('initRoutes', (allRoutes) => {
  storedRoutes = allRoutes;
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

      marker.on('click', (e) => {
        L.DomEvent.stopPropagation(e);
        drawRouteOnMap(bus.route, bus.color);
        highlightSelectedBus(bus.id);
      });

      busMarkers[bus.id] = marker;
    }
  });
});

function drawRouteOnMap(routeId, color) {
  if (activeRouteLine) {
    liveMap.removeLayer(activeRouteLine);
  }

  const routeData = storedRoutes[routeId];
  if (!routeData) return;

  const latLngs = routeData.path.map(coord => [coord[1], coord[0]]);

  activeRouteLine = L.polyline(latLngs, {
    color: color,
    weight: 5,
    opacity: 0.7
  }).addTo(liveMap);

  liveMap.flyToBounds(activeRouteLine.getBounds(), {
    padding: [50, 50],
    duration: 1.5,
    easeLinearity: 0.25,
    animate: true
  });
}

function highlightSelectedBus(selectedBusId) {
  Object.keys(busMarkers).forEach(id => {
    const marker = busMarkers[id];

    if (selectedBusId === null) {
      marker.setOpacity(1.0);
      marker.setZIndexOffset(0);
    } else if (id === selectedBusId) {
      marker.setOpacity(1.0);
      marker.setZIndexOffset(1000);
    } else {
      marker.setOpacity(0.5);
      marker.setZIndexOffset(0)
    }
  });
}

liveMap.on('click', () => {
  if (activeRouteLine) {
    liveMap.removeLayer(activeRouteLine);
    activeRouteLine = null;
  }

  highlightSelectedBus(null);
});

liveMap.on('movestart zoomstart', () => {
  liveMap.getContainer().classList.add('is-zooming');
});

liveMap.on('moveend zoomend', () => {
  liveMap.getContainer().classList.remove('is-zooming');
});
