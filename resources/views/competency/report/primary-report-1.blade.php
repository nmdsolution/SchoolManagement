<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bulletin Scolaire</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            font-family: Arial, sans-serif;
            font-size: 12px;
        }

        th,
        td {
            padding: 4px;
            text-align: center;
            border: 1px solid #000;
        }

        th {
            background-color: #f2f2f2;
        }

        .left-align {
            text-align: left;
        }
    </style>
</head>

<body>
    @if ($settings['report_layout_type'] == 1)
        <div class="row text-center" style="display: flex;">
            <div class="col-5 report_left_header" style="position: relative;top: -45px;">
                {!! $settings['report_left_header'] ?? '' !!}
            </div>
            <div class="col-2">
                @if ($reportHeaderLogo)
                    <img src="{{ 'data:image/png;base64,' . base64_encode(@file_get_contents(public_path('storage/' . $reportHeaderLogo->getRawOriginal('message')))) }}"
                        alt="Report Header" class="w-100" />
                @endif
            </div>
            <div class="col-5 report_right_header" style="position: relative;top: -45px;">
                {!! $settings['report_right_header'] ?? '' !!}
            </div>
        </div>
    @endif
    <table>
        <thead>
            <tr>
                <th class="left-align">NIVEAU : I COURS : {{ $classSection->name }} ECOLE : {{ $student->center->name }}</th>
                <th colspan="{{ ($termsCount == 1 ? 14 : $termsCount == 2) ? 13 : 16 }}">Année scolaire : {{ $sessionYear->name }}</th>
            </tr>
            <tr>
                <th class="left-align">Nom de l'Enseignant : {{ $classSection->teacher->user->full_name }}</th>
                <th colspan="{{ ($termsCount == 1 ? 14 : $termsCount == 2) ? 13 : 16 }}"></th>
            </tr>
            <tr>
                <th class="left-align">Nom de l'Élève : {{ $student->full_name }}</th>
                <th colspan="{{ ($termsCount == 1 ? 14 : $termsCount == 2) ? 13 : 16 }}">Classe : {{ $student->class_name }}</th>
            </tr>
            <tr>
                <th class="left-align"></th>
                <th>Trimestre</th>
                @foreach ($terms as $term)
                    @php
                        $sequencesCount = $sequences->where('exam_term_id', $term->id)->count();
                    @endphp
                    <th colspan="{{ $sequencesCount * 2 }}">{{ $term->name }}</th>
                @endforeach
                <th>Total</th>
            </tr>
            <tr>
                <th class="left-align">COMPETENCES</th>
                <th>Unité d'apprentissage</th>
                @foreach ($sequences as $sequence)
                    <th colspan="2">{{ $sequence->name }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @php
                $lastDomainId = null;
            @endphp
            @foreach ($competencies as $classCompetency)
                @if ($lastDomainId !== $classCompetency->competency->competency_domain->id)
                    <tr>
                        <td colspan="100%" class="left-align font-bold">
                            <strong>{{ $classCompetency->competency->competency_domain->name }}</strong>
                        </td>
                    </tr>
                @endif
                @php
                    $lastDomainId = $classCompetency->competency->competency_domain->id;
                @endphp

                @include('competency.report.competency-row', ['classCompetency' => $classCompetency])
            @endforeach
        </tbody>
    </table>
</body>

</html>
