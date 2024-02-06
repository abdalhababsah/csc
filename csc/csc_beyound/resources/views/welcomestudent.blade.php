<!DOCTYPE html>
<html>
<head>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/5.1.3/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-8roTzsuOlH0gaV0j8kGssd8Ew5X1cEzJZLkA6kW2ylM/0+5RqLFVH2I0h5Rc3jFu" crossorigin="anonymous">

<title>Page Title</title>
</head>
<body>

@if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif
@if(session('warning'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif
</body>
</html>