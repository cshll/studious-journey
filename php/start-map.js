var liveMap = L.map('live-map', {
  zoomControl: false
  //Manchester City Centre Coordinates
}).setView([53.46, -2.28], 13);

L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
  //OpenStreetMap is used to display the map
  maxZoom: 19,
  attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
}).addTo(liveMap);

L.control.locate({
  position: 'bottomright' //This is the position of the locate control
}).addTo(liveMap);

L.control.zoom({
  position: 'bottomright' //This is the position of the zoom control
}).addTo(liveMap);

const urlParams = new URLSearchParams(window.location.search);
const requestedRoute = urlParams.get('route');
let hasAutoZoomed = false;

const busMarkers = {}; //This is an object to store the bus markers
const busPaths = {}; //This is an object to store the bus paths

let storedRoutes = {}; //This is an object to store the stored routes
let activeRouteLine = null; //This is the active route line

const socket = io('http://localhost:3000'); //This is the socket.io connection to the server

socket.on('initRoutes', (allRoutes) => { //This is the event listener for the initRoutes event
  storedRoutes = allRoutes;

  if (requestedRoute && storedRoutes[requestedRoute]) {
    drawRouteOnMap(requestedRoute, storedRoutes[requestedRoute].color);
  }
});

socket.on('busesUpdate', (buses) => { //This is the event listener for the busesUpdate event
  buses.forEach(bus => {
    if (busMarkers[bus.id]) {
      busMarkers[bus.id].setLatLng([bus.lat, bus.lng]);
    } else {
      const customIcon = L.divIcon({ //This is the custom icon for the bus markers        
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

  if (requestedRoute && !hasAutoZoomed) {
    const targetBus = buses.find(b => b.route === requestedRoute);

    if (targetBus) {
      highlightSelectedBus(targetBus.id);
      hasAutoZoomed = true;
    }
  }
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
