<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Login Page</title> 
    <link rel="stylesheet" href="{{ asset('CSS/bootstrap.css') }}"> 
    <link rel="stylesheet" href="{{ asset('CSS/bootstrap.min.css') }}">  
</head>
<body class="d-flex flex-column min-vh-100">   
  
  <!-- Navbar -->
  <nav class="navbar navbar-light" style="background-color: #707FDD;">
    <div class="container-fluid">
      <a class="navbar-brand" href="#">
        <img src="{{ asset('assets/fti-ukdw.png') }}" width="40" height="40" alt=""> 
        <img src="{{ asset('assets/logo-ukdw.png') }}" width="40" height="40" alt="">
      </a>
    </div>
  </nav>
  
  <!-- Main Content -->
  <main class="d-flex flex-grow-1 align-items-center justify-content-center h-100">
    <div class="container h-100">
      <div class="row h-100 d-flex align-items-center justify-content-center">  
        <form action="{{ route('login.post') }}" method="POST" class="col-sm-10 col-md-8 col-lg-6 col-xl-5">
          @csrf
          <div class="card text-black shadow-lg" style="border-radius: 1rem; background-color: white;">
            <div class="card-body p-5 text-center">
              <h2 class="mb-4 text-uppercase">Login</h2>

              @if($errors->has('loginError'))
                <div class="alert alert-danger">
                  {{ $errors->first('loginError') }}
                </div>
              @endif

              <div class="mb-3 text-start">
                <label class="form-label" for="typeEmailX">User</label>
                <input type="text" name="user" id="typeEmailX" class="form-control form-control-lg w-100" required />
              </div>
              
              <div class="mb-3 text-start">
                <label class="form-label" for="typePasswordX">Password</label>
                <input type="password" name="password" id="typePasswordX" class="form-control form-control-lg w-100" required />
              </div>

              <button class="btn btn-primary btn-lg w-100" type="submit">Login</button>
            </div>
          </div>
        </form>      
      </div>
    </div>
  </main> 

  <footer class="text-center p-3" style="background-color:#707FDD; color: white; font-weight: bold;">
    Fakultas Teknologi Informasi
  </footer>  

</body>
</html>
