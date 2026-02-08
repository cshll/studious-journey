const express = require('express');
const http = require('http');
const { Server } = require("socket.io");
const fs = require('fs');

const app = express();
const server = http.createServer(app);
const io = new Server(server);

app.use(express.static('public'));

let allRoutes = {};
try {
  // Read the all_routes JSON file containing route data and parse it
  allRoutes = JSON.parse(fs.readFileSync('all_routes.json', 'utf8'));
} catch (error) {
  console.error("Could not load routes: ", error.message);
}

// Create a key for each bus
const buses = Object.keys(allRoutes).map(routeKey => {
  return {
    id: `Bus-${routeKey}`,
    route: routeKey,
    path: allRoutes[routeKey].path,
    currentIndex: 0,
    color: allRoutes[routeKey].color
  };
});

// Wait for connection
io.on('connection', (socket) => {
  // Emit initRoutes socket for the frontend
  socket.emit('initRoutes', allRoutes);
});

// Update the bus every two seconds
setInterval(() => {
  const updates = [];

  // Update the bus position
  buses.forEach(bus => {
    bus.currentIndex = (bus.currentIndex + 1) % bus.path.length;

    const [lon, lat] = bus.path[bus.currentIndex];

    updates.push({
      id: bus.id,
      route: bus.route,
      lat: lat,
      lng: lon,
      color: bus.color
    });
  });

  // Send bus update socket to frontend
  io.emit('busesUpdate', updates);
}, 2000);

// Wait for connection on port 3000
server.listen(3000, () => {
  console.log("Server up on port 3000, brace yourself for chaos.");
});
