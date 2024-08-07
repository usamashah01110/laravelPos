<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Google Authentication Success</title>
    <!-- Bootstrap CSS CDN -->
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    @php
    if(!empty($user['facebook_id'])){
        $name = 'Facebook';
    }else{
        $name = 'Google';
    }
 @endphp
    <h1 class="text-center">{{$name}} Authentication Successful</h1>
    <div class="mt-4">
        <p><strong>Name:</strong> {{ $user['first_name'] }}</p>
        <p><strong>Email:</strong> {{ $user['email'] }}</p>
        <p><strong>Google ID:</strong> {{ $user['google_id'] }}</p>
        <p><strong>Facebook ID:</strong> {{ $user['facebook_id'] }}</p>
    </div>
    <div class="text-center mt-4">
        <a href="{{ url('/') }}" class="btn btn-primary">Back to Home</a>
    </div>
</div>

<!-- Bootstrap JS and dependencies CDN -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
