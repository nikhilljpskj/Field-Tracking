<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="favicon.ico">
    <title><?php echo $title ?? 'Redeemer HRMS'; ?></title>
    <!-- Simple bar CSS -->
    <link rel="stylesheet" href="css/simplebar.css">
    <!-- Fonts CSS -->
    <link href="https://fonts.googleapis.com/css2?family=Overpass:ital,wght@0,100;0,200;0,300;0,400;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <!-- Icons CSS -->
    <link rel="stylesheet" href="css/feather.css">
    <!-- App CSS -->
    <link rel="stylesheet" href="css/app-light.css" id="lightTheme">
  </head>
  <body class="light ">
    <div class="wrapper vh-100">
      <div class="row align-items-center h-100">
        <form class="col-lg-3 col-md-4 col-10 mx-auto text-center" method="POST" action="login">
          <a class="navbar-brand mx-auto mt-2 flex-fill text-center" href="./">
             <img src="assets/images/redeemer-technologies-logo.png" alt="Redeemer Technologies" class="navbar-brand-img brand-md mb-2" style="max-height: 60px;">
          </a>
          <h1 class="h6 mb-3">Sign in</h1>
          <?php if(isset($error)): ?>
            <div class="alert alert-danger" role="alert">
              <?php echo $error; ?>
            </div>
          <?php endif; ?>
          <div class="form-group">
            <label for="inputEmail" class="sr-only">Email address</label>
            <input type="email" name="email" id="inputEmail" class="form-control form-control-lg" placeholder="Email address" required="" autofocus="">
          </div>
          <div class="form-group">
            <label for="inputPassword" class="sr-only">Password</label>
            <div class="input-group input-group-lg">
              <input type="password" name="password" id="inputPassword" class="form-control" placeholder="Password" required="">
              <div class="input-group-append">
                <span class="input-group-text bg-transparent border-left-0" style="cursor: pointer;" onclick="togglePassword()">
                  <i class="fe fe-eye fe-16" id="toggleIcon"></i>
                </span>
              </div>
            </div>
          </div>
          <script>
            function togglePassword() {
              const passInput = document.getElementById('inputPassword');
              const icon = document.getElementById('toggleIcon');
              if (passInput.type === 'password') {
                passInput.type = 'text';
                icon.classList.replace('fe-eye', 'fe-eye-off');
              } else {
                passInput.type = 'password';
                icon.classList.replace('fe-eye-off', 'fe-eye');
              }
            }
          </script>
          <button class="btn btn-lg btn-primary btn-block" type="submit">Let me in</button>
          <p class="mt-5 mb-3 text-muted">© 2026 Redeemer Technologies</p>
        </form>
      </div>
    </div>
    <script src="js/jquery.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/config.js"></script>
    <script src="js/apps.js"></script>
  </body>
</html>
