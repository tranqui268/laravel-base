@extends('layouts.empty')
@section('content')
<link href="{{ asset('css/login.css') }}" rel="stylesheet">
<div class="login-box text-center">
    <h2>Shop Management</h2>
    @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    <form action={{ route('login') }} method="POST">
        @csrf
      <div class="mb-3 text-start">
        <label for="email" class="form-label">Email</label>
        <input type="email" id="email" name="email" class="form-control" placeholder="username@gmail.com" value="{{ old('email') }}" required>
      </div>
      <div class="mb-2 text-start">
        <label for="password" class="form-label">Password</label>
        <input type="password" id="password" name="password" class="form-control" placeholder="Password" required>
      </div>
      
      <div class="d-flex justify-content-between align-items-center mb-3">
        <div class="form-check text-start">
          <input type="checkbox" class="form-check-input" id="remember" name="remember" value="{{ old('remember') ? 'checked' : '' }}">
          <label class="form-check-label text-white" for="remember">Remember Me</label>
        </div>
        <div>
          <a href="#" class="text-white text-decoration-underline small">Forgot Password?</a>
        </div>
      </div>
  
      <button type="submit" class="btn btn-login w-100 mb-3">Sign in</button>
  
      <p class="mb-2">or continue with</p>
      <div class="social-login">
        <img src="https://cdn-icons-png.flaticon.com/512/281/281764.png" alt="Google">
        <img src="https://cdn-icons-png.flaticon.com/512/25/25231.png" alt="GitHub">
        <img src="https://cdn-icons-png.flaticon.com/512/145/145802.png" alt="Facebook">
      </div>
  
      <div class="register-link">
        <p class="mt-3 mb-0">Don't have an account yet? <a href="/register">Register for free</a></p>
      </div>
    </form>
  </div>
  
@endsection