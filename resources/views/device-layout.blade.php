<!DOCTYPE html>
<html lang="en">
<head>
  <!-- Meta Information -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>

@if(isset($addDevice))
  <form method="post">
    Device Token: <b>{{$addDevice['deviceToken']}}</b>

    <input name="deviceToken" value="{{$addDevice['deviceToken']}}" hidden>
    Auth Token: <input name="authToken" value="" type="text">

    <input type="submit">
  </form>
@endif

@if(isset($verifyDevice))
  {{$verifyDevice['message']}}{{$verifyDevice['errorMessage']}}
@endif




</body>
</html>