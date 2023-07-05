<html>

<head>
    <title>Destinations report</title>
</head>

<body>
    <h1>Destinations report generated at {{ now()->toDateTimeString() }}. </h1>
    <h2>Collected results since {{ $since->toDateString() }}</h2>

    <table>
        <thead>
            <tr>
                <th>Branch</th>
                <th>Destination</th>
                <th>Assignments</th>
                <th>Leads</th>
                <th>Leads missing cnt.</th>
                <th>Deposits</th>
                <th>Deposits missing cnt.</th>
                <th>Error</th>
                <th>Missing leads ID's</th>
                <th>Missing FTD's ID's</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($report as $row)
                <tr>
                    <td>{{ $row['branch'] }}</td>
                    <td>{{ $row['destination'] }}</td>
                    <td>{{ $row['assignments'] }}</td>
                    <td>{{ $row['leads'] }}</td>
                    <td>{{ count($row['missingLeads']) }}</td>
                    <td>{{ $row['deposits'] }}</td>
                    <td>{{ count($row['missingFtd']) }}</td>
                    <td>{{ $row['error'] }}</td>
                    <td>{{ collect($row['missingLeads'])->implode(',') }}</td>
                    <td>{{ collect($row['missingFtd'])->implode(',') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>
