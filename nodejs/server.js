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
  allRoutes = JSON.parse(fs.readFileSync('all_routes.json', 'utf8'));
} catch (error) {
  console.error("Could not load routes: ", error.message);
}

const buses = Object.keys(allRoutes).map(routeKey => {
  return {
    id: `Bus-${routeKey}`,
    route: routeKey,
    path: allRoutes[routeKey].path,
    currentIndex: 0,
    color: allRoutes[routeKey].color
  };
});

io.on('connection', (socket) => {
  socket.emit('initRoutes', allRoutes);
});

setInterval(() => {
  const updates = [];

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

  io.emit('busesUpdate', updates);
}, 2000);

server.listen(3000, () => {
  console.log("Server up on port 3000, brace yourself for chaos.");
});
