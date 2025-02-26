<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Global Report for {{ $sequence->name }}</title>
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
        <h3>Global Report for {{ $sequence->name }}</h3>
    </div>
    <div class="section-title">RESULTATS GLOBAUX</div>
    <table>
        <thead>
            <tr>
                <th colspan="3">Class Size</th>
                <th colspan="3">Evalues</th>
                <th colspan="3">Participation rate</th>
                <th colspan="3">Success rate</th>
                <th colspan="3">Average score</th>
                <th colspan="3">Minimum score</th>
                <th colspan="3">Maximum score</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <!-- Class Size -->
                <td>M</td>
                <td>F</td>
                <td>T</td>
                <!-- Evalues -->
                <td>M</td>
                <td>F</td>
                <td>T</td>
                <!-- Participation rate -->
                <td>M</td>
                <td>F</td>
                <td>T</td>
                <!-- Success rate -->
                <td>M</td>
                <td>F</td>
                <td>T</td>
                <!-- Average score -->
                <td>M</td>
                <td>F</td>
                <td>T</td>
                <!-- Minimum score -->
                <td>M</td>
                <td>F</td>
                <td>T</td>
                <!-- Maximum score -->
                <td>M</td>
                <td>F</td>
                <td>T</td>
            </tr>
            <tr>
                <!-- Class Size -->
                <td>{{ $data['EFF']['male'] }}</td>
                <td>{{ $data['EFF']['female'] }}</td>
                <td>{{ $class_size }}</td>
                <!-- Evalues -->
                <td>{{ $data['EVA']['male'] }}</td>
                <td>{{ $data['EVA']['female'] }}</td>
                <td>{{ $data['EVA']['male'] + $data['EVA']['female'] }}</td>
                <!-- Participation rate -->
                <td>{{ number_format($male_part, 2) }}%</td>
                <td>{{ number_format($female_part, 2) }}%</td>
                <td>{{ number_format($part, 2) }}%</td>
                <!-- Success rate -->
                <td>{{ number_format($male_rate, 2) }}%</td>
                <td>{{ number_format($female_rate, 2) }}%</td>
                <td>{{ number_format($total_rate, 2) }}%</td>
                <!-- Average score -->
                <td>{{ number_format($male_avg, 2) }}</td>
                <td>{{ number_format($female_avg, 2) }}</td>
                <td>{{ number_format($total_avg, 2) }}</td>
                <!-- Minimum score -->
                <td>{{ $data['MIN']['male'] }}</td>
                <td>{{ $data['MIN']['female'] }}</td>
                <td>{{ min($data['MIN']['male'], $data['MIN']['female']) }}</td>
                <!-- Maximum score -->
                <td>{{ $data['MAX']['male'] }}</td>
                <td>{{ $data['MAX']['female'] }}</td>
                <td>{{ max($data['MAX']['male'], $data['MAX']['female']) }}</td>
            </tr>
        </tbody>
    </table>
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
                    <td>{{ number_format($stats['male_part'], 2) }}%</td>
                    <td>{{ number_format($stats['female_part'], 2) }}%</td>
                    <td>{{ number_format($stats['part'], 2) }}%</td>
                    <td>{{ number_format($stats['male_rate'], 2) }}%</td>
                    <td>{{ number_format($stats['female_rate'], 2) }}%</td>
                    <td>{{ number_format($stats['total_rate'], 2) }}%</td>
                    <td>{{ number_format($stats['male_avg'], 2) }}</td>
                    <td>{{ number_format($stats['female_avg'], 2) }}</td>
                    <td>{{ number_format($stats['total_avg'], 2) }}</td>
                    <td>{{ $stats['MIN']['male'] }}</td>
                    <td>{{ $stats['MIN']['female'] }}</td>
                    <td>{{ min($stats['MIN']['male'], $stats['MIN']['female']) }}</td>
                    <td>{{ $stats['MAX']['male'] }}</td>
                    <td>{{ $stats['MAX']['female'] }}</td>
                    <td>{{ max($stats['MAX']['male'], $stats['MAX']['female']) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
