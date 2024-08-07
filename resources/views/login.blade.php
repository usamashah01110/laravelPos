<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login with Google</title>
    <!-- Bootstrap CSS CDN -->
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h1 class="text-center">Login with Facebook</h1>
{{--    <div class="text-center mt-4">--}}
{{--        <a href="{{ route('google.redirect') }}" class="btn btn-primary">Login with Google</a>--}}
{{--    </div>--}}
    <div class="text-center mt-4">
        <a href="{{ route('login.facebook') }}" class="btn btn-primary">Login with Facebook</a>
    </div>
</div>

<!-- Bootstrap JS and dependencies CDN -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

