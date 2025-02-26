<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Class Stats Report for {{ $sequence->name }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            line-height: 1.6;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .title {
            font-size: 1.5em;
            font-weight: bold;
            text-align: center;
            margin: 20px 0;
        }
        .section-title {
            font-weight: bold;
            background-color: #f4f4f4;
            padding: 8px;
            margin: 20px 0 10px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0 20px 0;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
        }
        th {
            background-color: #f4f4f4;
        }
    </style>
</head>
<body>
    <div class="header">
        <h3>Class Stats Report for {{ $sequence->name }}</h3>
    </div>
    <div class="section-title">RESULTATS PAR CLASSE</div>
    <table>
        <thead>
            <tr>
                <th>Class Section</th>
                <th colspan="3">Class Size</th>
                <th colspan="3">Participation Rate</th>
                <th colspan="3">Success Rate</th>
                <th colspan="3">Average Score</th>
                <th colspan="3">Minimum Score</th>
                <th colspan="3">Maximum Score</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td></td>
                <td>M</td>
                <td>F</td>
                <td>T</td>
                <td>M</td>
                <td>F</td>
                <td>T</td>
                <td>M</td>
                <td>F</td>
                <td>T</td>
                <td>M</td>
                <td>F</td>
                <td>T</td>
                <td>M</td>
                <td>F</td>
                <td>T</td>
                <td>M</td>
                <td>F</td>
                <td>T</td>
            </tr>
            @foreach($class_stats as $stats)
                <tr>
                    <td>{{ $stats['class_section'] }}</td>
                    <td>{{ $stats['EFF']['male'] }}</td>
                    <td>{{ $stats['EFF']['female'] }}</td>
                    <td>{{ $stats['class_size'] }}</td>
                    <td>{{ round($stats['male_part'], 2) }}%</td>
                    <td>{{ round($stats['female_part'], 2) }}%</td>
                    <td>{{ round($stats['part'], 2) }}%</td>
                    <td>{{ round($stats['male_rate'], 2) }}%</td>
                    <td>{{ round($stats['female_rate'], 2) }}%</td>
                    <td>{{ round($stats['total_rate'], 2) }}%</td>
                    <td>{{ round($stats['male_avg'], 2) }}</td>
                    <td>{{ round($stats['female_avg'], 2) }}</td>
                    <td>{{ round($stats['total_avg'], 2) }}</td>
                    <td>{{ $stats['min'] }}</td>
                    <td>{{ $stats['max'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
