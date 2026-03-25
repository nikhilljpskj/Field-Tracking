      </main> <!-- main -->
    </div> <!-- .wrapper -->
    <script src="js/jquery.min.js"></script>
    <script src="js/popper.min.js"></script>
    <script src="js/moment.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/simplebar.min.js"></script>
    <script src='js/daterangepicker.js'></script>
    <script src='js/jquery.stickOnScroll.js'></script>
    <script src="js/tinycolor-min.js"></script>
    <script src="js/config.js"></script>
    <script src="js/apps.js"></script>
    <!-- HERE Maps JS API Scripts -->
    <script type="text/javascript" src="https://js.api.here.com/v3/3.1/mapsjs-core.js"></script>
    <script type="text/javascript" src="https://js.api.here.com/v3/3.1/mapsjs-service.js"></script>
    <script type="text/javascript" src="https://js.api.here.com/v3/3.1/mapsjs-ui.js"></script>
    <script type="text/javascript" src="https://js.api.here.com/v3/3.1/mapsjs-mapevents.js"></script>

    <script>
      window.HERE_API_KEY = "<?php echo \App\Core\Config::get('HERE_API_KEY'); ?>";
    </script>
    
    <?php if(isset($_SESSION['user_id']) && isset($_SESSION['is_checked_in'])): ?>
    <script>
      // Optimized Background Tracking System
      (function() {
          let lastLat = null;
          let lastLng = null;
          const MIN_DISTANCE = <?php echo \App\Core\Config::get('MIN_MOVE_DISTANCE', 50); ?>; // meters
          const INTERVAL = <?php echo \App\Core\Config::get('SYNC_INTERVAL', 30); ?> * 1000; // ms

          function calculateDistance(lat1, lon1, lat2, lon2) {
              const R = 6371e3; // meters
              const φ1 = lat1 * Math.PI/180;
              const φ2 = lat2 * Math.PI/180;
              const Δφ = (lat2-lat1) * Math.PI/180;
              const Δλ = (lon2-lon1) * Math.PI/180;
              const a = Math.sin(Δφ/2) * Math.sin(Δφ/2) +
                        Math.cos(φ1) * Math.cos(φ2) *
                        Math.sin(Δλ/2) * Math.sin(Δλ/2);
              const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
              return R * c;
          }
          
          function logLocation() {
              if (navigator.geolocation) {
                  navigator.geolocation.getCurrentPosition(function(position) {
                      const lat = position.coords.latitude;
                      const lng = position.coords.longitude;
                      const accuracy = position.coords.accuracy;
                      
                      if (accuracy > 100) return;

                      // Only log if moved significantly or first time
                      if (lastLat !== null && lastLng !== null) {
                          const dist = calculateDistance(lat, lng, lastLat, lastLng);
                          if (dist < MIN_DISTANCE) {
                              console.log("Movement below threshold:", Math.round(dist), "m");
                              return;
                          }
                      }
                      
                      $.post('tracking?action=log', {
                          latitude: lat,
                          longitude: lng,
                          accuracy: accuracy
                      }).done(function() {
                          lastLat = lat;
                          lastLng = lng;
                          console.log("Location synced:", new Date().toLocaleTimeString());
                      });
                  }, null, { enableHighAccuracy: true, maximumAge: 0, timeout: 20000 });
              }
          }

          logLocation();
          setInterval(logLocation, INTERVAL);
      })();
    </script>
    <?php endif; ?>
  </body>
</html>
