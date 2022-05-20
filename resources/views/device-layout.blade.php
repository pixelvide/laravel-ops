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
  <input value="{{$addDevice->deviceToken}}">
</form>

</body>
</html>