<!DOCTYPE html>
<html>
<head>
    <title>PDF Example</title>
</head>
<body>
    <h1>Customer List : </h1>
    <h1>{{ $title }}</h1>
    <p>This PDF is generated using Laravel and DOMPDF.</p>

    <table border="1">
        <tr>
            <th>id</th>
            <th>name</th>
            <th>email</th>
            <th>address</th>
            <th>city</th>
        </tr>

        @foreach ($customers as $row)
        <tr>
            <td>{{ $row->id }}</td>
            <td>{{ $row->name }}</td>
            <td>{{ $row->email }}</td>
            <td>{{ $row->address }}</td>
            <td>{{ $row->city }}</td>
        </tr>
        @endforeach

    </table>

</body>
</html>
