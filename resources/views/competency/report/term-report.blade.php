<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bulletin Scolaire</title>
    <link href="{{ asset('assets/plugins/bootstrap/css/bootstrap.css') }}" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <style>
        body {
            position: relative;
            /* For pseudo-element positioning */

            /* Other styles */
            margin: 0;
            font-family: var(--bs-body-font-family);
            font-size: 60%;
            font-weight: var(--bs-body-font-weight);
            line-height: var(--bs-body-line-height);
            color: var(--bs-body-color);
            background-color: var(--bs-body-bg);
            -webkit-text-size-adjust: 100%;
            -webkit-tap-highlight-color: transparent;
            height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        body::before {

            background-repeat: no-repeat;
            background-position: center;
            background-size: 400px 400px;

            /* Add blur and transparency */
            filter: blur(100px);
            /* Adjust blur intensity (e.g., 5px, 10px) */
            opacity: 0.2;
            /* Adjust transparency (e.g., 0.1 to 0.3 for subtle visibility) */

            /* Positioning and sizing */
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            /* Place it behind other content */
        }


        .content {
            margin: auto;
            width: 100%;
        }

        thead {
            background-color: {{ $settings['report_color'] ?? '' }};
        }

        dd,
        legend {
            margin-bottom: .5rem;
        }

        progress,
        sub,
        sup {
            vertical-align: baseline;
        }

        :root {
            --bs-blue: #0d6efd;
            --bs-indigo: #6610f2;
            --bs-purple: #6f42c1;
            --bs-pink: #d63384;
            --bs-red: #dc3545;
            --bs-orange: #fd7e14;
            --bs-yellow: #ffc107;
            --bs-green: #198754;
            --bs-teal: #20c997;
            --bs-cyan: #0dcaf0;
            --bs-white: #fff;
            --bs-gray: #6c757d;
            --bs-gray-dark: #343a40;
            --bs-gray-100: #f8f9fa;
            --bs-gray-200: #e9ecef;
            --bs-gray-300: #dee2e6;
            --bs-gray-400: #ced4da;
            --bs-gray-500: #adb5bd;
            --bs-gray-600: #6c757d;
            --bs-gray-700: #495057;
            --bs-gray-800: #343a40;
            --bs-gray-900: #212529;
            --bs-primary: #0d6efd;
            --bs-secondary: #6c757d;
            --bs-success: #198754;
            --bs-info: #0dcaf0;
            --bs-warning: #ffc107;
            --bs-danger: #dc3545;
            --bs-light: #f8f9fa;
            --bs-dark: #212529;
            --bs-primary-rgb: 13, 110, 253;
            --bs-secondary-rgb: 108, 117, 125;
            --bs-success-rgb: 25, 135, 84;
            --bs-info-rgb: 13, 202, 240;
            --bs-warning-rgb: 255, 193, 7;
            --bs-danger-rgb: 220, 53, 69;
            --bs-light-rgb: 248, 249, 250;
            --bs-dark-rgb: 33, 37, 41;
            --bs-white-rgb: 255, 255, 255;
            --bs-black-rgb: 0, 0, 0;
            --bs-body-color-rgb: 33, 37, 41;
            --bs-body-bg-rgb: 255, 255, 255;
            --bs-font-sans-serif: system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", "Liberation Sans", sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji";
            --bs-font-monospace: SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
            --bs-gradient: linear-gradient(180deg, rgba(255, 255, 255, 0.15), rgba(255, 255, 255, 0));
            --bs-body-font-family: var(--bs-font-sans-serif);
            --bs-body-font-size: 0.1rem;
            --bs-body-font-weight: 400;
            --bs-body-line-height: 1.5;
            --bs-body-color: #212529;
            --bs-body-bg: #fff;
        }

        *,
        ::after,
        ::before {
            box-sizing: border-box;
        }

        hr {
            background-color: currentColor;
            border: 0;
            /*opacity: .25;*/
        }

        hr:not([size]) {
            height: 1px;
        }

        img,
        svg {
            vertical-align: middle;
        }

        table {
            caption-side: bottom;
            border-collapse: collapse;
            border-right: 1px solid #000000;
            font-size: 12px;
            width: 100%;
        }

        th {
            text-align: inherit;
            text-align: -webkit-match-parent;
            padding: 0.2rem;
            text-transform: uppercase;
        }

        .student-table td {
            padding: 0.2rem;
            text-transform: uppercase;
        }

        td {
            padding: 0.2rem;
            text-transform: uppercase;
        }


        tbody,
        td,
        tfoot,
        th,
        thead,
        tr {
            border: 0 solid;
            border-color: inherit;
        }

        .row {
            --bs-gutter-x: 1.5rem;
            display: flex;
            flex-direction: row;
            flex-wrap: wrap;
            margin-top: calc(-1 * var(--bs-gutter-y));
            margin-right: calc(-.5 * var(--bs-gutter-x));
            margin-left: calc(-.5 * var(--bs-gutter-x));
        }

        .row>* {
            flex-shrink: 0;
            width: 100%;
            max-width: 100%;
            padding-right: calc(var(--bs-gutter-x) * .5);
            padding-left: calc(var(--bs-gutter-x) * .5);
            margin-top: var(--bs-gutter-y);
        }

        .col {
            flex: 1 0 0;
        }

        .col-12,
        .row-cols-1>* {
            flex: 0 0 auto;
            width: 100%;
        }

        .col-6,
        .row-cols-2>* {
            flex: 0 0 auto;
            width: 50%;
        }

        .row-cols-3>* {
            flex: 0 0 auto;
            width: 33.3333333333%;
        }

        .col-3,
        .row-cols-4>* {
            flex: 0 0 auto;
            width: 25%;
        }

        .row-cols-5>* {
            flex: 0 0 auto;
            width: 20%;
        }

        .row-cols-6>* {
            flex: 0 0 auto;
            width: 16.6666666667%;
        }

        .col-2 {
            display: inline-block;
            padding-right: 15px;
            padding-left: 15px;
            width: 16.66666667%;
        }

        .col-4,
        .col-5 {
            flex: 0 0 auto;
        }

        .col-4 {
            display: inline-block;
            padding-right: 15px;
            padding-left: 15px;
            width: 33.33333333%;
        }

        .col-5 {
            display: inline-block;
            padding-right: 15px;
            padding-left: 15px;
            width: 41.66666667%;
        }

        .col-7,
        .col-8 {
            flex: 0 0 auto;
        }

        .col-7 {
            display: inline-block;
            padding-right: 15px;
            padding-left: 15px;
            width: 58.33333333%;
        }

        .col-8 {
            display: inline-block;
            padding-right: 15px;
            padding-left: 15px;
            width: 66.66666667%;
        }

        .col-9 {
            display: inline-block;
            padding-right: 15px;
            padding-left: 15px;
            flex: 0 0 auto;
            width: 75%;
        }

        .table {
            --bs-table-bg: transparent;
            --bs-table-accent-bg: transparent;
            --bs-table-striped-color: #212529;
            --bs-table-striped-bg: rgba(0, 0, 0, 0.05);
            --bs-table-active-color: #212529;
            --bs-table-active-bg: rgba(0, 0, 0, 0.1);
            --bs-table-hover-color: #212529;
            --bs-table-hover-bg: rgba(0, 0, 0, 0.075);
            width: 100%;
            margin-bottom: 1rem;
            color: #212529;
            vertical-align: top;
            border-color: #dee2e6;
        }

        .student-table {
            --bs-table-bg: transparent;
            --bs-table-accent-bg: transparent;
            --bs-table-striped-color: #212529;
            --bs-table-striped-bg: rgba(0, 0, 0, 0.05);
            --bs-table-active-color: #212529;
            --bs-table-active-bg: rgba(0, 0, 0, 0.1);
            --bs-table-hover-color: #212529;
            --bs-table-hover-bg: rgba(0, 0, 0, 0.075);
            width: 100%;
            margin-bottom: 1rem;
            color: #212529;
            vertical-align: top;
            border-color: #dee2e6;
        }

        .student-table>tbody {
            vertical-align: inherit;
        }


        .table> :not(caption)>*>* {
            padding-left: .5rem;
            padding-right: .5rem;
            padding-top: .3rem;
            background-color: var(--bs-table-bg);
            border-bottom-width: 1px;
            box-shadow: inset 0 0 0 9999px var(--bs-table-accent-bg);
        }

        .table>tbody {
            vertical-align: inherit;
        }

        .table>thead {
            vertical-align: bottom;
        }

        .table> :not(:first-child) {
            border-top: 2px solid currentColor;
        }

        .table-sm> :not(caption)>*>* {
            padding: .25rem;
        }

        .table-bordered> :not(caption)>* {
            border-width: 1px 1px;
        }

        .table-bordered> :not(caption)>*>* {
            border-width: 1px 1px;
        }

        .align-middle {
            vertical-align: middle !important;
        }

        .border-bottom {
            border-bottom: 1px solid #dee2e6 !important;
        }

        .border-dark {
            border-color: #212529 !important;
        }

        .border-1 {
            border-width: 1px !important;
        }

        .w-100 {
            width: 100% !important;
        }

        .mt-2 {
            margin-top: .5rem !important;
        }

        .mt-3 {
            margin-top: 1rem !important;
        }

        .mb-0 {
            margin-bottom: 0 !important;
        }

        .ms-3 {
            margin-left: 1rem !important;
        }

        .pe-0 {
            padding-right: 0 !important;
        }

        .text-start {
            text-align: left !important;
        }

        .text-center {
            text-align: center !important;
        }

        .bg-white {
            background-color: rgba(var(--bs-white-rgb), var(--bs-bg-opacity)) !important;
        }

        .my-1 {
            margin-top: 0.25rem !important;
            margin-bottom: 0.25rem !important;
        }

        .row {
            display: table;
            width: 100%;
        }

        .row [class^="col-"] {
            display: table-cell;
        }

        .ps-0 {
            padding-left: 0 !important;
        }

        .verticalTableHeader {
            text-align: center;
            white-space: nowrap;
            g-origin: 50% 50%;
            -webkit-transform: rotate(270deg);
            -moz-transform: rotate(270deg);
            -ms-transform: rotate(270deg);
            -o-transform: rotate(270deg);
            transform: rotate(270deg);
            padding: 1.5rem 0 !important;
        }

        .verticalTableHeader p {
            margin: 0 -100%;
            display: inline-block;
        }

        .verticalTableHeader p:before {
            content: '';
            width: 0;
            padding-top: 110%;
            /* takes width as reference, + 10% for faking some extra padding */
            display: inline-block;
            vertical-align: middle;
        }

        .report_left_header p,
        .report_right_header p {
            margin: 0;
        }

        .student-image {
            width: 100%;
            /* Full width of container */
            height: 100%;
            /* Full height of container */
            object-fit: cover;
            /* Ensure image fits within cell without cropping */
            display: block;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-family: Arial, sans-serif;
            font-size: 12px;
        }

        thead {
            display: table-header-group;
        }

        tfoot {
            display: table-footer-group;
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

        tr {
            page-break-inside: avoid;
        }

        .bg {
            background-color: #f2f2f2;
        }

        .font-bold {
            font-weight: bold;
        }
    </style>
</head>

<body>
    @php
        $termsCount = $terms->count();
    @endphp
    @if (isset($settings['report_layout_type']) && $settings['report_layout_type'] == 1)
        <div class="row text-center" style="display: flex;">
            <div class="col-5 report_left_header" style="position: relative;top: -45px;">
                {!! $settings['report_left_header'] ?? '' !!}
            </div>
            <div class="col-2">
                @if ($reportHeaderLogo)
                    <img src="{{ 'data:image/png;base64,' . base64_encode(@file_get_contents(public_path('storage/' . $reportHeaderLogo->getRawOriginal('message')))) }}"
                        alt="Report Header" class="w-100" style="width: 100px; height: auto;" />
                @endif
            </div>
            <div class="col-5 report_right_header" style="position: relative;top: -45px;">
                {!! $settings['report_right_header'] ?? '' !!}
            </div>
        </div>
    @endif

    <table class="mt-3">
        <tr>
            <th class="left-align">NIVEAU : I COURS : {{ $classSection->name }} ECOLE : {{ $student->center->name }}
            </th>
            <th colspan="{{ $sequences->count() * 2 + $terms->count() + 1 }}">Année scolaire :
                {{ $sessionYear->name }}
            </th>
        </tr>
        <tr>
            <th class="left-align">Nom de l'Enseignant : {{ $classSection->teacher->user->full_name }}</th>
            <th colspan="{{ $sequences->count() * 2 + $terms->count() + 1 }}"></th>
        </tr>
        <tr>
            <th class="left-align">Nom de l'Élève : {{ $student->full_name }}</th>
            <th colspan="{{ $sequences->count() * 2 + $terms->count() + 1 }}">Classe :
                {{ $student->class_name }}</th>
        </tr>
        <tr>
            <th class="left-align"></th>
            <th>Trimestre</th>
            @foreach ($terms as $term)
                @php
                    $sequencesCount = $term->sequence->count();
                @endphp
                <th colspan="{{ $sequencesCount * 2 + 1 }}">{{ $term->name }}</th>
            @endforeach
        </tr>
        <tr>
            <th class="left-align">COMPETENCES</th>
            <th>Unité d'apprentissage</th>
            @foreach ($terms as $term)
                @php
                    $termSequences = $term->sequence;
                @endphp
                @foreach ($termSequences as $sequence)
                    <th colspan="2">{{ $sequence->name }}</th>
                    @if ($loop->iteration % $termSequences->count() == 0)
                        <!-- Vérifier si c'est le troisième élément -->
                        <th>Total</th> <!-- Afficher la balise spéciale -->
                    @endif
                @endforeach
            @endforeach
        </tr>

        @php
            $lastDomainId = null;
        @endphp
        @foreach ($competencies as $classCompetency)
            @if ($lastDomainId !== $classCompetency->competency->competency_domain->id)
                <tr class="bg">
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

        <tr class="bg">
            <td class="left-align font-bold">Moyenne par Séquence</td>
            <td></td>
            @foreach ($terms as $term)
                @php
                    $termSequences = $term->sequence;
                @endphp
                @foreach ($termSequences as $sequence)
                    <td>
                        <strong>{{ number_format($sequencesAverage[$sequence->id] ?? 0, 2) }}</strong>
                    </td>
                    <td></td>
                @endforeach
                <td>
                    <strong>{{ number_format($quarterlyAverage, 2) }}</strong>
                </td>
            @endforeach
        </tr>
    </table>

    @if ($termsCount == 3)
        @include('competency.report.observation')
    @endif
</body>

</html>
