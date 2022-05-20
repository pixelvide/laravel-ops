<!DOCTYPE html>
<html lang="en">
<head>
  <!-- Meta Information -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>

<form method="post">
  Device Token: <b>{{$addDevice['deviceToken']}}</b>

  <input value="{{$addDevice['deviceToken']}}" hidden>
  Auth Token: <input value="" type="text">

  <input type="submit">
</form>

</body>
</html>