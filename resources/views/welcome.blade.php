<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <!-- Latest compiled and minified CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

</head>

<body>



    <div class="container mt-5 m-auto w-50">

        <div>
            <h1 class="">Payment With Stripe</h1>
            <hr>
            <div class="form-group">
                <label for="">Taka</label>
                <input type="text" class="form-control" id="" placeholder="1000$">
            </div>
            <a href="{{ url('stripe') }}" class="btn btn-primary mt-3">Payment Stripe Online</a>
        </div>




        <!-- Latest compiled JavaScript -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
