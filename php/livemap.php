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
     <!-- GTFS RT schema (complete official schema from Google) -->
     <script id="gtfs-rt-schema" type="text/plain">
     syntax = "proto2";
     option java_package = "com.google.transit.realtime";
     package transit_realtime;
     
     message FeedMessage {
       required FeedHeader header = 1;
       repeated FeedEntity entity = 2;
     }
     
     message FeedHeader {
       required string gtfs_realtime_version = 1;
       enum Incrementality {
         FULL_DATASET = 0;
         DIFFERENTIAL = 1;
       }
       optional Incrementality incrementality = 2 [default = FULL_DATASET];
       optional uint64 timestamp = 3;
       optional string feed_version = 4;
     }
     
     message FeedEntity {
       required string id = 1;
       optional bool is_deleted = 2 [default = false];
       optional TripUpdate trip_update = 3;
       optional VehiclePosition vehicle = 4;
       optional Alert alert = 5;
       optional Shape shape = 6;
       optional Stop stop = 7;
       optional TripModifications trip_modifications = 8;
     }
     
     message TripUpdate {
       required TripDescriptor trip = 1;
       repeated StopTimeUpdate stop_time_update = 2;
       optional VehicleDescriptor vehicle = 3;
       optional uint64 timestamp = 4;
       optional int32 delay = 5;
     }
     
     message StopTimeUpdate {
       enum ScheduleRelationship {
         SCHEDULED = 0;
         SKIPPED = 1;
         NO_DATA = 2;
         UNSCHEDULED = 3;
       }
       optional uint32 stop_sequence = 1;
       optional string stop_id = 4;
       optional StopTimeEvent arrival = 2;
       optional StopTimeEvent departure = 3;
       optional ScheduleRelationship schedule_relationship = 5 [default = SCHEDULED];
       optional VehiclePosition.OccupancyStatus departure_occupancy_status = 7;
     }
     
     message StopTimeEvent {
       optional int32 delay = 1;
       optional int64 time = 2;
       optional int32 uncertainty = 3;
       optional int64 scheduled_time = 4;
     }
     
     message VehiclePosition {
       enum VehicleStopStatus {
         INCOMING_AT = 0;
         STOPPED_AT = 1;
         IN_TRANSIT_TO = 2;
       }
       enum CongestionLevel {
         UNKNOWN_CONGESTION_LEVEL = 0;
         RUNNING_SMOOTHLY = 1;
         STOP_AND_GO = 2;
         CONGESTION = 3;
         SEVERE_CONGESTION = 4;
       }
       enum OccupancyStatus {
         EMPTY = 0;
         MANY_SEATS_AVAILABLE = 1;
         FEW_SEATS_AVAILABLE = 2;
         STANDING_ROOM_ONLY = 3;
         CRUSHED_STANDING_ROOM_ONLY = 4;
         FULL = 5;
         NOT_ACCEPTING_PASSENGERS = 6;
         NO_DATA_AVAILABLE = 7;
         NOT_BOARDABLE = 8;
       }
       optional TripDescriptor trip = 1;
       optional VehicleDescriptor vehicle = 8;
       optional Position position = 2;
       optional uint32 current_stop_sequence = 3;
       optional string stop_id = 7;
       optional VehicleStopStatus current_status = 4 [default = IN_TRANSIT_TO];
       optional uint64 timestamp = 5;
       optional CongestionLevel congestion_level = 6;
       optional OccupancyStatus occupancy_status = 9;
       optional uint32 occupancy_percentage = 10;
     }
     
     message Position {
       required float latitude = 1;
       required float longitude = 2;
       optional float bearing = 3;
       optional double odometer = 4;
       optional float speed = 5;
     }
     
     message TripDescriptor {
       enum ScheduleRelationship {
         SCHEDULED = 0;
         ADDED = 1 [deprecated = true];
         UNSCHEDULED = 2;
         CANCELED = 3;
         REPLACEMENT = 5;
         DUPLICATED = 6;
         DELETED = 7;
         NEW = 8;
       }
       optional string trip_id = 1;
       optional string route_id = 5;
       optional uint32 direction_id = 6;
       optional string start_time = 2;
       optional string start_date = 3;
       optional ScheduleRelationship schedule_relationship = 4;
     }
     
     message VehicleDescriptor {
       optional string id = 1;
       optional string label = 2;
       optional string license_plate = 3;
       enum WheelchairAccessible {
         NO_VALUE = 0;
         UNKNOWN = 1;
         WHEELCHAIR_ACCESSIBLE = 2;
         WHEELCHAIR_INACCESSIBLE = 3;
       }
       optional WheelchairAccessible wheelchair_accessible = 4 [default = NO_VALUE];
     }
     
     message Alert {
       repeated TimeRange active_period = 1;
       repeated EntitySelector informed_entity = 5;
       enum Cause {
         UNKNOWN_CAUSE = 1;
         OTHER_CAUSE = 2;
         TECHNICAL_PROBLEM = 3;
         STRIKE = 4;
         DEMONSTRATION = 5;
         ACCIDENT = 6;
         HOLIDAY = 7;
         WEATHER = 8;
         MAINTENANCE = 9;
         CONSTRUCTION = 10;
         POLICE_ACTIVITY = 11;
         MEDICAL_EMERGENCY = 12;
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
         NO_EFFECT = 10;
         ACCESSIBILITY_ISSUE = 11;
       }
       enum SeverityLevel {
         UNKNOWN_SEVERITY = 1;
         INFO = 2;
         WARNING = 3;
         SEVERE = 4;
       }
       optional Cause cause = 6 [default = UNKNOWN_CAUSE];
       optional Effect effect = 7 [default = UNKNOWN_EFFECT];
       optional TranslatedString url = 8;
       optional TranslatedString header_text = 10;
       optional TranslatedString description_text = 11;
       optional TranslatedString tts_header_text = 12;
       optional TranslatedString tts_description_text = 13;
       optional SeverityLevel severity_level = 14 [default = UNKNOWN_SEVERITY];
       optional TranslatedImage image = 15;
       optional TranslatedString image_alternative_text = 16;
       optional TranslatedString cause_detail = 17;
       optional TranslatedString effect_detail = 18;
     }
     
     message TranslatedImage {
       message LocalizedImage {
         required string url = 1;
         required string media_type = 2;
         optional string language = 3;
       }
       repeated LocalizedImage localized_image = 1;
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
       optional uint32 direction_id = 6;
     }
     
     message TranslatedString {
       repeated Translation translation = 1;
     }
     
     message Translation {
       required string text = 1;
       optional string language = 2;
     }
     
     message Shape {
       optional string shape_id = 1;
       optional string encoded_polyline = 2;
     }
     
     message Stop {
       enum WheelchairBoarding {
         UNKNOWN = 0;
         AVAILABLE = 1;
         NOT_AVAILABLE = 2;
       }
       optional string stop_id = 1;
       optional TranslatedString stop_code = 2;
       optional TranslatedString stop_name = 3;
       optional TranslatedString tts_stop_name = 4;
       optional TranslatedString stop_desc = 5;
       optional float stop_lat = 6;
       optional float stop_lon = 7;
       optional string zone_id = 8;
       optional TranslatedString stop_url = 9;
       optional string parent_station = 11;
       optional string stop_timezone = 12;
       optional WheelchairBoarding wheelchair_boarding = 13 [default = UNKNOWN];
       optional string level_id = 14;
       optional TranslatedString platform_code = 15;
     }
     
     message TripModifications {
       message Modification {
         optional StopSelector start_stop_selector = 1;
         optional StopSelector end_stop_selector = 2;
         optional int32 propagated_modification_delay = 3 [default = 0];
         repeated ReplacementStop replacement_stops = 4;
         optional string service_alert_id = 5;
         optional uint64 last_modified_time = 6;
       }
       message SelectedTrips {
         repeated string trip_ids = 1;
         optional string shape_id = 2;
       }
       repeated SelectedTrips selected_trips = 1;
       repeated string start_times = 2;
       repeated string service_dates = 3;
       repeated Modification modifications = 4;
     }
     
     message StopSelector {
       optional uint32 stop_sequence = 1;
       optional string stop_id = 2;
     }
     
     message ReplacementStop {
       optional int32 travel_time_to_stop = 1;
       optional string stop_id = 2;
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
        // Map centered on Manchester city center
        var liveMap = L.map('liveMap').setView([53.4808, -2.2426], 13);
        L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
    }).addTo(liveMap);
    L.control.locate().addTo(liveMap);
    //Insert Reference for Leaflet and openmap API
    //leaflet-locatecontrol-gh-pages

    //Icon is made with logo.app and appears on map as a marker for the bus
    var icon263 = L.icon({
        iconUrl: '263.png',        // Path relative to livemap.php
        iconSize: [40, 40],             // Size
        iconAnchor: [20, 20],           // Point of icon that marks the location
        popupAnchor: [0, -20]           // Where popup appears relative to icon
    });
    var icon245 = L.icon({
        iconUrl: '245.png',       
        iconSize: [40, 40],             
        iconAnchor: [20, 20],           
        popupAnchor: [0, -20]           
    });
    var icon30 = L.icon({
        iconUrl: '30.png',        // Changed from 50.png to 30.png (matching your bus ID)
        iconSize: [40, 40],             
        iconAnchor: [20, 20],           
        popupAnchor: [0, -20]           
    });
    
    // Store bus markers so we can update them instead of creating new ones
    var busMarkers = {};
    
    // Function to add/update bus markers on the map
    function updateBusMarkers(vehicles) {
        vehicles.forEach(function(vehicle) {
            var vehicleName = vehicle.vehicleId;  // Bus name: "30", "263", or "245"
            var routeId = vehicle.routeId;        // Route number (for display)
            var lat = vehicle.latitude;            // From API: e.g., 53.4189
            var lng = vehicle.longitude;          // From API: e.g., -2.3592
            
            // Debug: Log coordinates to check if they're correct
            console.log('Bus ' + vehicleName + ' (Route ' + routeId + ') coordinates:', lat, lng);
            
            // Choose the right icon based on BUS NAME (30, 263, 245)
            var icon;
            if (vehicleName === '30') {
                icon = icon30;
            } else if (vehicleName === '263') {
                icon = icon263;
            } else if (vehicleName === '245') {
                icon = icon245;
            } else {
                icon = icon30; // Default fallback
            }
            
            // Use vehicle name as unique key
            var markerKey = vehicleName;
            
            // Check if marker already exists for this bus
            if (busMarkers[markerKey]) {
                // UPDATE existing marker position (moves it to new location)
                busMarkers[markerKey].setLatLng([lat, lng]);
            } else {
                // CREATE new marker
                var marker = L.marker([lat, lng], {icon: icon}).addTo(liveMap);
                var popupText = 'Bus ' + vehicleName;
                if (routeId !== 'Unknown') {
                    popupText += '<br>Route: ' + routeId;
                }
                popupText += '<br>Lat: ' + lat.toFixed(6) + '<br>Lng: ' + lng.toFixed(6);
                marker.bindPopup(popupText);
                busMarkers[markerKey] = marker;  // Store it so we can update it later
            }
        });
    }
    
    // Parse GTFS RT protobuf data and extract vehicle positions
    async function parseGTFSRTData(base64Data) {
      try {
        // Decode base64 to binary - verify it's valid base64 first
        if (!base64Data || base64Data.length === 0) {
          throw new Error('Empty base64 data');
        }
        
        let binaryString;
        try {
          binaryString = atob(base64Data);
        } catch (e) {
          throw new Error('Invalid base64 data: ' + e.message);
        }
        
        const bytes = new Uint8Array(binaryString.length);
        for (let i = 0; i < binaryString.length; i++) {
          bytes[i] = binaryString.charCodeAt(i);
        }
        
        // Verify we have data
        if (bytes.length === 0) {
          throw new Error('Decoded binary data is empty');
        }
        
        console.log('Decoded binary data:', {
          length: bytes.length,
          firstBytes: Array.from(bytes.slice(0, 10)).map(b => b.toString(16).padStart(2, '0')).join(' ')
        });
        
        // Load GTFS RT protobuf schema from inline script (avoids CORS)
        const schemaText = document.getElementById('gtfs-rt-schema').textContent;
        const root = protobuf.parse(schemaText, {
          keepCase: true,
          alternateCommentMode: true
        }).root;
        const FeedMessage = root.lookupType('transit_realtime.FeedMessage');
        
        // Decode the message - catch errors for better debugging
        let message;
        try {
          message = FeedMessage.decode(bytes);
        } catch (decodeError) {
          // If decode fails, the schema might be incomplete or data corrupted
          // Log detailed error info
          console.error('Decode error details:', {
            error: decodeError.message,
            offset: decodeError.offset,
            bytesLength: bytes.length,
            firstBytes: Array.from(bytes.slice(0, 20)).map(b => '0x' + b.toString(16).padStart(2, '0')).join(' ')
          });
          throw decodeError;
        }
        const feed = FeedMessage.toObject(message, {
          longs: String,
          enums: String,
          bytes: String,
        });
        
        // Filter: Only show buses with these vehicle names/labels
        // These are the bus names, not route numbers
        // 263: Altrincham - Sale - Stretford - Hulme - Piccadilly Gardens
        // 30: The Trafford Centre - Trafford Park - Piccadilly Gardens
        // 245: The Trafford Centre - Urmston - Sale - Altrincham
        const allowedBusNames = ['30', '263', '245'];
        
        // Track seen vehicle IDs to avoid duplicates
        const seenVehicleIds = new Set();
        
        // Extract vehicle positions
        const vehicles = [];
        if (feed.entity && Array.isArray(feed.entity)) {
          feed.entity.forEach((entity, index) => {
            if (entity.vehicle && entity.vehicle.position) {
              const pos = entity.vehicle.position;
              const trip = entity.vehicle.trip || {};
              const vehicleDesc = entity.vehicle.vehicle || {};
              
              // Extract vehicle name/label (this is what identifies the bus: "30", "263", "245")
              // Vehicle name is typically in vehicle.vehicle.label or vehicle.vehicle.id
              const vehicleName = vehicleDesc.label || 
                                 vehicleDesc.id || 
                                 vehicleDesc.vehicleId ||
                                 entity.id || 
                                 `vehicle-${index}`;
              
              // Convert to string for comparison
              const vehicleNameStr = String(vehicleName);
              
              // Filter: Only include buses with these specific names (30, 263, 245)
              if (!allowedBusNames.includes(vehicleNameStr)) {
                console.log(`Skipping bus - name "${vehicleNameStr}" not in allowed list`);
                return; // Skip buses not in the allowed list
              }
              
              // Also get route_id for display purposes
              const routeId = trip.routeId || 
                             trip.route_id || 
                             trip['routeId'] ||
                             trip['route_id'] ||
                             'Unknown';
              
              // Debug: Log vehicle information
              console.log(`Found bus ${vehicleNameStr} (route: ${routeId}) at (${pos.latitude}, ${pos.longitude})`);
              
              // Deduplicate: Skip if we've already seen this vehicle name
              if (seenVehicleIds.has(vehicleNameStr)) {
                console.log(`Skipping duplicate entry for vehicle ${vehicleNameStr} (entity.id: ${entity.id})`);
                return; // Skip duplicate
              }
              seenVehicleIds.add(vehicleNameStr);
              
              // Log vehicle data for debugging
              if (index < 5) {
                console.log(`Vehicle ${vehicleNameStr} data:`, {
                  entityId: entity.id,
                  vehicleDesc: vehicleDesc,
                  vehicleLabel: vehicleDesc.label,
                  vehicleId: vehicleDesc.id,
                  routeId: routeId,
                  trip: trip,
                  position: pos
                });
              }
              
              vehicles.push({
                id: entity.id || `vehicle-${index}`,
                latitude: parseFloat(pos.latitude) || 0,
                longitude: parseFloat(pos.longitude) || 0,
                bearing: pos.bearing !== undefined ? parseFloat(pos.bearing) : null,
                speed: pos.speed !== undefined ? parseFloat(pos.speed) : null,
                routeId: routeId,  // Route number (for display)
                vehicleId: vehicleNameStr  // Bus name: "30", "263", or "245"
              });
            }
          });
        }
        
        // Display the extracted data
        displayVehiclePositions(vehicles, feed);
        
        // Update markers on the map with real coordinates
        updateBusMarkers(vehicles);
        
      } catch (error) {
        console.error('Error parsing GTFS RT data:', error);
        // Fallback: show data info with helpful message
        const binarySize = Math.round((base64Data.length * 3) / 4);
        const errorMsg = error.message || 'Unknown error';
        const isWireTypeError = errorMsg.includes('wire type');
        
        let helpfulMessage = 'GTFS RT data contains vehicle positions for all buses. Each vehicle has latitude and longitude coordinates.';
        if (isWireTypeError) {
          helpfulMessage += '\n\nNote: The protobuf schema may need to be updated to match the exact GTFS RT format used by BODS. The data is being received successfully, but parsing requires a complete schema definition.';
        }
        
        displayBusData(
          `Error parsing GTFS RT data: ${errorMsg}\n\nData size: ${binarySize} bytes\n\n${helpfulMessage}`,
          {
            format: 'GTFS RT (Binary Protobuf)',
            size: binarySize + ' bytes',
            message: helpfulMessage,
            error: errorMsg
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
                    <th style="padding: 8px; text-align: left; border-bottom: 2px solid #ddd;">Latitude</th>
                    <th style="padding: 8px; text-align: left; border-bottom: 2px solid #ddd;">Longitude</th>
                  </tr>
                </thead>
                <tbody>
        `;
        
        vehicles.forEach((vehicle, index) => {
          content += `
            <tr style="${index % 2 === 0 ? 'background: #fafafa;' : ''}">
              <td style="padding: 8px; border-bottom: 1px solid #eee;">${vehicle.vehicleId}</td>
              <td style="padding: 8px; border-bottom: 1px solid #eee;">${vehicle.latitude.toFixed(6)}</td>
              <td style="padding: 8px; border-bottom: 1px solid #eee;">${vehicle.longitude.toFixed(6)}</td>
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
