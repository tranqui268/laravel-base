<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Login Page</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  
</head>
<body>

@yield('content')
@include('layouts.scripts')
<meta name="csrf-token" content="{{ csrf_token() }}">
</body>
</html>
