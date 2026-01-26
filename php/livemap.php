<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Bus Company</title>
  <link rel="stylesheet" href="style.css">
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
     integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
     crossorigin=""/>
  <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
     integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
     crossorigin=""></script>
     <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/leaflet.locatecontrol@0.86.0/dist/L.Control.Locate.min.css" />
     <script src="https://cdn.jsdelivr.net/npm/leaflet.locatecontrol@0.86.0/dist/L.Control.Locate.min.js" charset="utf-8"></script>
     <!-- Protobuf.js for parsing GTFS RT data -->
     <script src="https://cdn.jsdelivr.net/npm/protobufjs@7.2.5/dist/protobuf.min.js"></script>
     <!-- GTFS RT schema (inline to avoid CORS) -->
     <script id="gtfs-rt-schema" type="text/plain">
     syntax = "proto2";
     package transit_realtime;
     
     message FeedMessage {
       required FeedHeader header = 1;
       repeated FeedEntity entity = 2;
     }
     
     message FeedHeader {
       required string gtfs_realtime_version = 1;
       optional int64 timestamp = 2;
     }
     
     message FeedEntity {
       required string id = 1;
       optional bool is_deleted = 2 [default = false];
       optional TripUpdate trip_update = 3;
       optional VehiclePosition vehicle = 4;
       optional Alert alert = 5;
     }
     
     message VehiclePosition {
       optional TripDescriptor trip = 1;
       optional VehicleDescriptor vehicle = 2;
       optional Position position = 3;
       optional uint32 current_stop_sequence = 4;
       optional string stop_id = 5;
       optional VehiclePosition.VehicleStopStatus current_status = 6 [default = IN_TRANSIT_TO];
       optional uint64 timestamp = 7;
       optional VehiclePosition.CongestionLevel congestion_level = 8;
       optional VehiclePosition.OccupancyStatus occupancy_status = 9;
     }
     
     message Position {
       required float latitude = 1;
       required float longitude = 2;
       optional float bearing = 3;
       optional double odometer = 4;
       optional float speed = 5;
     }
     
     message TripDescriptor {
       optional string trip_id = 1;
       optional string route_id = 2;
       optional uint32 direction_id = 3;
       optional string start_time = 4;
       optional string start_date = 5;
       optional TripDescriptor.ScheduleRelationship schedule_relationship = 6;
     }
     
     message VehicleDescriptor {
       optional string id = 1;
       optional string label = 2;
       optional string license_plate = 3;
     }
     
     message Alert {
       repeated TimeRange active_period = 1;
       repeated EntitySelector informed_entity = 5;
       optional Cause cause = 6 [default = UNKNOWN_CAUSE];
       optional Effect effect = 7 [default = UNKNOWN_EFFECT];
       optional TranslatedString url = 8;
       optional TranslatedString header_text = 10;
       optional TranslatedString description_text = 11;
     }
     
     enum VehiclePosition.VehicleStopStatus {
       INCOMING_AT = 0;
       STOPPED_AT = 1;
       IN_TRANSIT_TO = 2;
     }
     
     enum VehiclePosition.CongestionLevel {
       UNKNOWN_CONGESTION_LEVEL = 0;
       RUNNING_SMOOTHLY = 1;
       STOP_AND_GO = 2;
       CONGESTION = 3;
     }
     
     enum VehiclePosition.OccupancyStatus {
       EMPTY = 0;
       MANY_SEATS_AVAILABLE = 1;
       FEW_SEATS_AVAILABLE = 2;
       STANDING_ROOM_ONLY = 3;
       CRUSHED_STANDING_ROOM_ONLY = 4;
       FULL = 5;
       NOT_ACCEPTING_PASSENGERS = 6;
     }
     
     enum TripDescriptor.ScheduleRelationship {
       SCHEDULED = 0;
       ADDED = 1;
       UNSCHEDULED = 2;
       CANCELED = 3;
     }
     
     enum Cause {
       UNKNOWN_CAUSE = 1;
       OTHER_CAUSE = 2;
     }
     
     enum Effect {
       NO_SERVICE = 1;
       REDUCED_SERVICE = 2;
       SIGNIFICANT_DELAYS = 3;
       DETOUR = 4;
       ADDITIONAL_SERVICE = 5;
       MODIFIED_SERVICE = 6;
       OTHER_EFFECT = 7;
       UNKNOWN_EFFECT = 8;
       STOP_MOVED = 9;
     }
     
     message TimeRange {
       optional uint64 start = 1;
       optional uint64 end = 2;
     }
     
     message EntitySelector {
       optional string agency_id = 1;
       optional string route_id = 2;
       optional int32 route_type = 3;
       optional TripDescriptor trip = 4;
       optional string stop_id = 5;
     }
     
     message TranslatedString {
       repeated Translation translation = 1;
     }
     
     message Translation {
       required string text = 1;
       optional string language = 2;
     }
     </script>
</head>
<body>
  <header class="site-header">
    <div class="container header-flex">
      <div class="logo">
        <svg width="50" height="50" viewBox="0 0 60 60" xmlns="http://www.w3.org/2000/svg">
          <g fill="var(--text-dark)" fill-rule="evenodd">
            <path d="M10 20h40c2.2 0 4 1.8 4 4v16c0 2.2-1.8 4-4 4h-4v4h-6v-4H20v4h-6v-4h-4c-2.2 0-4-1.8-4-4V24c0-2.2 1.8-4 4-4zm4 6h10v8H14v-8zm18 0h14v8H32v-8z"/>
          </g>
        </svg>
        <a href="index.php">Trafford Bus</a>
      </div>
      <nav class="main-nav">
      <ul>
          <li><a href="#">Tickets</a></li>
          <li><a href="livemap.php">Map</a></li>
          <li><a href="timetable.php">Timetables</a></li>
          <li><a href="#">Journeys</a></li>
        </ul>
      </nav>
      <?php if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true): ?>
        <a class="btn btn-header" href="logout.php" id="logout">Logout</a>
      <?php else: ?>
        <a class="btn btn-header" href="login.php" id="login">Login</a>
      <?php endif; ?>
    </div>
  </header>

  <main class="site-content">
    <div class="container">
      <h1>Map of Trafford</h1>
    <div id="liveMap" style="width: 100%; height: 500px;"></div>
    <script>
        var liveMap = L.map('liveMap').setView([53.4189361, -2.3592972], 13);
        L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
    }).addTo(liveMap);
    L.control.locate().addTo(liveMap);
    //Insert Reference for Leaflet and Thunderforest API
    //leaflet-locatecontrol-gh-pages

    // Parse GTFS RT protobuf data and extract vehicle positions
    async function parseGTFSRTData(base64Data) {
      try {
        // Decode base64 to binary
        const binaryString = atob(base64Data);
        const bytes = new Uint8Array(binaryString.length);
        for (let i = 0; i < binaryString.length; i++) {
          bytes[i] = binaryString.charCodeAt(i);
        }
        
        // Load GTFS RT protobuf schema from inline script (avoids CORS)
        const schemaText = document.getElementById('gtfs-rt-schema').textContent;
        const root = protobuf.parse(schemaText, {keepCase: true}).root;
        const FeedMessage = root.lookupType('transit_realtime.FeedMessage');
        
        // Verify the message
        const errMsg = FeedMessage.verify(bytes);
        if (errMsg) {
          console.warn('Verification warning:', errMsg);
        }
        
        // Decode the message
        const message = FeedMessage.decode(bytes);
        const feed = FeedMessage.toObject(message, {
          longs: String,
          enums: String,
          bytes: String,
        });
        
        // Extract vehicle positions
        const vehicles = [];
        if (feed.entity && Array.isArray(feed.entity)) {
          feed.entity.forEach((entity, index) => {
            if (entity.vehicle && entity.vehicle.position) {
              const pos = entity.vehicle.position;
              vehicles.push({
                id: entity.id || `vehicle-${index}`,
                latitude: parseFloat(pos.latitude) || 0,
                longitude: parseFloat(pos.longitude) || 0,
                bearing: pos.bearing !== undefined ? parseFloat(pos.bearing) : null,
                speed: pos.speed !== undefined ? parseFloat(pos.speed) : null,
                routeId: (entity.vehicle.trip && entity.vehicle.trip.routeId) ? entity.vehicle.trip.routeId : 'Unknown',
                vehicleId: (entity.vehicle.vehicle && entity.vehicle.vehicle.id) ? entity.vehicle.vehicle.id : 'Unknown'
              });
            }
          });
        }
        
        // Display the extracted data
        displayVehiclePositions(vehicles, feed);
        
      } catch (error) {
        console.error('Error parsing GTFS RT data:', error);
        // Fallback: show data info
        const binarySize = Math.round((base64Data.length * 3) / 4);
        displayBusData(
          `Error parsing GTFS RT data: ${error.message}\n\nData size: ${binarySize} bytes\n\nNote: This GTFS RT feed contains ALL buses/vehicles in the system, each with latitude and longitude coordinates.`,
          {
            format: 'GTFS RT (Binary Protobuf)',
            size: binarySize + ' bytes',
            message: 'GTFS RT data contains vehicle positions for all buses. Each vehicle has latitude and longitude coordinates.'
          }
        );
      }
    }

    // Display extracted vehicle positions
    function displayVehiclePositions(vehicles, feed) {
      const outputDiv = document.getElementById('busDataOutput');
      const timestamp = new Date().toLocaleTimeString();
      
      let content = `
        <div style="margin-top: 20px; padding: 15px; background: #e8f5e9; border-radius: 5px; border-left: 4px solid #4caf50;">
          <h2 style="margin-top: 0; color: #2e7d32;">✓ Bus Location Data Received</h2>
          <p><strong>Last updated:</strong> ${timestamp}</p>
          <p><strong>Total vehicles found:</strong> ${vehicles.length}</p>
      `;
      
      if (vehicles.length > 0) {
        content += `
          <div style="margin-top: 15px;">
            <h3>Vehicle Positions:</h3>
            <div style="max-height: 400px; overflow-y: auto;">
              <table style="width: 100%; border-collapse: collapse; background: white; border-radius: 3px;">
                <thead>
                  <tr style="background: #f5f5f5;">
                    <th style="padding: 8px; text-align: left; border-bottom: 2px solid #ddd;">Vehicle ID</th>
                    <th style="padding: 8px; text-align: left; border-bottom: 2px solid #ddd;">Route</th>
                    <th style="padding: 8px; text-align: left; border-bottom: 2px solid #ddd;">Latitude</th>
                    <th style="padding: 8px; text-align: left; border-bottom: 2px solid #ddd;">Longitude</th>
                    <th style="padding: 8px; text-align: left; border-bottom: 2px solid #ddd;">Bearing</th>
                  </tr>
                </thead>
                <tbody>
        `;
        
        vehicles.forEach((vehicle, index) => {
          content += `
            <tr style="${index % 2 === 0 ? 'background: #fafafa;' : ''}">
              <td style="padding: 8px; border-bottom: 1px solid #eee;">${vehicle.vehicleId}</td>
              <td style="padding: 8px; border-bottom: 1px solid #eee;">${vehicle.routeId}</td>
              <td style="padding: 8px; border-bottom: 1px solid #eee;">${vehicle.latitude.toFixed(6)}</td>
              <td style="padding: 8px; border-bottom: 1px solid #eee;">${vehicle.longitude.toFixed(6)}</td>
              <td style="padding: 8px; border-bottom: 1px solid #eee;">${vehicle.bearing !== null ? vehicle.bearing.toFixed(1) + '°' : 'N/A'}</td>
            </tr>
          `;
        });
        
        content += `
                </tbody>
              </table>
            </div>
          </div>
        `;
      } else {
        content += `
          <p style="margin-top: 10px; padding: 10px; background: white; border-radius: 3px;">
            No vehicle positions found in the data feed.
          </p>
        `;
      }
      
      content += `
          <details style="margin-top: 10px;">
            <summary style="cursor: pointer; color: #1976d2;">Show raw feed info</summary>
            <pre style="background: white; padding: 10px; border-radius: 3px; overflow-x: auto; max-height: 200px; margin-top: 10px; font-size: 12px;">${JSON.stringify(feed.header || {}, null, 2)}</pre>
          </details>
        </div>
      `;
      
      outputDiv.innerHTML = content;
      
      // Log to console for debugging
      console.log(`Found ${vehicles.length} vehicles:`, vehicles);
    }

    // AI Assitance - Fetch bus data through PHP proxy (avoids CORS issues)
    function fetchBusData() {
      // Call our PHP proxy instead of the API directly
      fetch('bus_api_proxy.php')
      .then(response => {
        if (!response.ok) {
          throw new Error('Network response was not ok: ' + response.status);
        }
        return response.json();
      })
      .then(data => {
        // Check if there's an error from the proxy
        if (data.error) {
          throw new Error(data.message);
        }
        
        // Handle different response formats
        let displayData;
        let dataInfo = {};
        
        if (data.format === 'base64') {
          // Parse GTFS RT binary data
          parseGTFSRTData(data.data);
        } else if (typeof data === 'string') {
          displayData = data;
          displayBusData(displayData, {});
        } else {
          displayData = JSON.stringify(data, null, 2);
          displayBusData(displayData, {});
        }
        
        console.log('Bus data received:', data);
      })
      .catch(error => {
        console.error('Error fetching bus data:', error);
        document.getElementById('busDataOutput').innerHTML = 
          '<p style="color: red;">Error loading bus data: ' + error.message + '</p>';
      });
    }

    function displayBusData(data, dataInfo = {}) {
      const outputDiv = document.getElementById('busDataOutput');
      const timestamp = new Date().toLocaleTimeString();
      
      // Display the data with timestamp
      let content = '';
      if (dataInfo.format && dataInfo.format.includes('GTFS RT')) {
        // For binary data, show a more user-friendly message
        content = `
          <div style="margin-top: 20px; padding: 15px; background: #e8f5e9; border-radius: 5px; border-left: 4px solid #4caf50;">
            <h2 style="margin-top: 0; color: #2e7d32;">✓ Bus Location Data Received</h2>
            <p><strong>Last updated:</strong> ${timestamp}</p>
            <p><strong>Format:</strong> ${dataInfo.format}</p>
            <p><strong>Data size:</strong> ${dataInfo.size}</p>
            <p style="margin-top: 10px; padding: 10px; background: white; border-radius: 3px;">
              ${dataInfo.message || 'Vehicle location data successfully retrieved from BODS API.'}
            </p>
            <details style="margin-top: 10px;">
              <summary style="cursor: pointer; color: #1976d2;">Show raw data info</summary>
              <pre style="background: white; padding: 10px; border-radius: 3px; overflow-x: auto; max-height: 200px; margin-top: 10px; font-size: 12px;">${data}</pre>
            </details>
          </div>
        `;
      } else {
        // For text/JSON data, show normally
        content = `
          <div style="margin-top: 20px; padding: 15px; background: #f5f5f5; border-radius: 5px;">
            <h2>Bus Location Data</h2>
            <p><strong>Last updated:</strong> ${timestamp}</p>
            <pre style="background: white; padding: 10px; border-radius: 3px; overflow-x: auto; max-height: 400px;">${data}</pre>
          </div>
        `;
      }
      
      outputDiv.innerHTML = content;
    }

    // Call immediately on page load
    fetchBusData();
    
    // Set up interval to fetch every 10 seconds (10000 milliseconds)
    setInterval(fetchBusData, 10000);
    </script>
    </div>
    
    <!-- Container for displaying bus data -->
    <div id="busDataOutput" class="container"></div>
  </main>

  <footer class="site-footer">
    <div class="container">
      <div class="footer-grid">
        <div class="footer-col">
          <h3>About Us</h3>
          <p>Trafford Bus operates a local bus service within the Trafford area.</p>
        </div>

        <div class="footer-col">
          <h4>Quick Links</h4>
          <ul>
            <li><a href="#">Tickets</a></li>
            <li><a href="livemap.php">Map</a></li>
            <li><a href="#">Timetables</a></li>
            <li><a href="#">Journeys</a></li>
          </ul>
        </div>
      
        <div class="footer-col">
          <h4>Contact Us</h4>
          <p>📧 support@traffordbus.local</p>
          <p>📱 0161</p>
        </div>
      </div>
      <div class="footer-bottom">
        <p>&copy; 2026 Trafford Bus. All rights reserved.</p>
      </div>
    </div>
  </footer>
</body>
</html>
