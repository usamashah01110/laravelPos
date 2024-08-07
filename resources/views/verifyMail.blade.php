<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{$data['title']}}</title>
</head>
<body>
<p>{{$data['body']}}</p>
@if(isset($data['url']))
    <a href="{{$data['url']}}">click here to reset password</a>
@endif
@if(isset($data['code']))
    <h1>{{$data['code']}}</h1>
@endif
<p>Thank you.</p>
</body>
</html>
