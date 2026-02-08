const mysql = require('mysql2/promise');
const axios = require('axios');
const fs = require('fs');

const dbConfig = {
  host: 'db',
  user: 'root',
  password: 'busses',
  database: 'bus_db'
};

async function generateRoutes() {
  let connection;
  try {
    // Connect to backend database
    connection = await mysql.createConnection(dbConfig);

    // Pull down all current routes from the database
    const [routes] = await connection.execute("SELECT route_id, route_number FROM routes");

    const masterRouteData = {};

    for (const route of routes) {
      // Select all trips from each route
      const [trips] = await connection.execute(
        `SELECT trip_id FROM trips WHERE route_id = ? AND direction = 0 LIMIT 1`,
        [route.route_id]
      );

      // If there is no trips, skip this route
      if (trips.length === 0) continue;

      const tripId = trips[0].trip_id;

      // Collect all stops from the trip
      const [stops] = await connection.execute(`
        SELECT stops.latitude, stops.longitude 
        FROM stop_times 
        JOIN stops ON stop_times.stop_id = stops.stop_id 
        WHERE stop_times.trip_id = ? 
        ORDER BY stop_times.stop_sequence ASC
      `, [tripId]);

      // Map out the coordinates of each stop
      const coordinates = stops.map(s => `${s.longitude},${s.latitude}`).join(';');

      try {
        // Attempt to connect to router project API and draw lines between each stop
        const url = `http://router.project-osrm.org/route/v1/driving/${coordinates}?overview=full&geometries=geojson`;
        const response = await axios.get(url);

        // Color and map out relevant route data
        masterRouteData[route.route_number] = {
          color: '#FFD100',
          path: response.data.routes[0].geometry.coordinates
        };

        await new Promise(r => setTimeout(r, 1000));
      } catch (error) {
        console.error(`Failed to load ${route.route_number}: `, error.message);
      }
    }

    // Write routes to a JSON file for later use
    fs.writeFileSync('all_routes.json', JSON.stringify(masterRouteData, null, 2));
  } catch (error) {
    console.error("Database error: ", error);
  } finally {
    if (connection) await connection.end();
  }
}

generateRoutes();
