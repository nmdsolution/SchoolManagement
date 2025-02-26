{{-- <!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Student ID Card</title>
    <style>
        html {
            margin: 0;
        }

        body {}

        .std-id-card {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0px 2px 8px rgba(0, 0, 0, 0.2);
            height: 340px;
            margin-left: auto;
            margin-right: auto;
            margin-top: 20px;
            width: 480px;
            border: 1px solid gray;
        }

        .id-header {
            background-color: {{ $header_color }};
            color: {{ $font_color }};
            display: flex;
            flex-direction: row;
            justify-content: space-between;
            align-items: center;
            padding: 20px;
            border-top-left-radius: 8px;
            border-top-right-radius: 8px;
        }

        .id-header .profile {
            height: 100px;
            width: 100px;
            border-radius: 50%;
            border: 3px solid #fff;
            object-fit: cover;
        }

        .school-logo {
            height: 30px;
            width: auto;
            float: right;
        }

        .id-header h2 {
            font-size: 28px;
            margin: 0;
            padding: 0;
            text-align: right;
            margin-top: -40px;
        }

        .id-info {
            padding: 20px;
            background-color: white;
        }

        .id-info p {
            font-size: 18px;
            margin: 10px 0;
            padding: 0;
        }

        .id-info strong {
            font-weight: bold;
        }

        .school-info {
            float: right;
        }

        .school-name {
            margin-top: 30px;
            font-size: 16px;
        }

        .id-footer {
            
            background: {{ $footer_color }};
            height: 20px;
            width: 100%;
            margin-top: 8px;
            text-align: center;
            font-weight: 400;
            letter-spacing: 1px;
            padding-top: 1px;
            color: {{ $font_color }};
            border-bottom-left-radius: 8px;
            border-bottom-right-radius: 8px;
        }

        .page-break {
            page-break-after: always;
        }
    </style>
</head>

<body>


    @foreach ($students as $student)
        @if ($counter < 3)
            @php
                $counter++;
            @endphp
        @else
            <div class="page-break"> </div>
            @php
                $counter=0;
            @endphp
        @endif
        <div class="std-id-card">
            <div class="id-header">
                <div class="school-info">
                    <img class="school-logo" src="{{ public_path('storage/logo.png') }}" alt="">
                    <h3 class="school-name">Shayona Primary School</h3>
                </div>
                <img class="profile" src="{{ public_path(str_replace(url('/'), '', $student->user->image)) }}"
                    alt="Student Photo">
                <h2 class="footer">{{ $student->user->full_name }}</h2>
            </div>
            <div class="id-info" style="padding-bottom: 10px">
                <p><strong>Class Section: </strong> {{ $student->class_section->class->name }} -
                    {{ $student->class_section->section->name }} - {{ $student->class_section->class->medium->name }}
                </p>
                <p><strong>Roll Number: </strong> {{ $student->roll_number }}</p>
                <p><strong>GR No.: </strong> {{ $student->admission_no }}</p>
                <p><strong>Session Year: </strong> {{ $school_detail['session_year'] }}</p>

            </div>
            <div class="id-footer">
                <label for="">Student Identification Card</label>
            </div>
        </div>
        
    @endforeach
</body>

</html> --}}

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Student ID Card</title>
    <style>
        .card {
            width: 60%;
            margin: auto;
            height: 297px;
            border-radius: 8px;
            border: 1px solid black;
            margin-bottom: 20px;
        }

        .header {
            /* background-color: #0099CC; */
            /* background-color:

        {{ $footer_color }}  ; */
            background-color: {{ $header_color }};
            height: 65px;
            border-top-left-radius: 8px;
            border-top-right-radius: 8px;
            border-bottom-left-radius: 10%;
            border-bottom-right-radius: 10%;
        }

        .school_logo {
            height: 40px;
            float: right;
            margin-bottom: 20px;

        }

        .top-right {
            padding: 10px;
            /* float: right; */
            margin-left: 13%;
            width: 44%;

        }

        .school_name {
            font-size: 18px;
            font-weight: 700;
            letter-spacing: 1px;
            margin-top: 50px;
            color: white;
            text-align: center;
            color: {{ $font_color }};
        }

        .school_title {
            /* float: right; */
            text-align: right !important;
            /* font-weight: 800; */
            /* font-size: 24px; */
            /* margin-top: 44px; */
        }

        .top-left {
            /* background-color: #015B89; */

            margin-top: -64px;
            /* background-color: {{ $header_color }}; */
            background-color: {{ $footer_color }};
            height: 65px;
            width: 100%;
            border-top-left-radius: 8px;
            border-top-right-radius: 50%;

            /* border-bottom-left-radius: 50%; */
        }

        .water-mark {
            color: black;
            padding-top: 20px;
            text-align: center;
            letter-spacing: 5px;
            font-size: 10px;
        }

        .div-border {
            /* background-color: #015B89; */
            background-color: {{ $header_color }};
            height: 65px;
            border-top-right-radius: 50%;
            /* border-bottom-left-radius: 50%; */
            /* border-left: 2px solid white; */
            /* border-right: 2px solid white; */
            margin-left: 40px;
            margin-right: 15px;
        }

        .profile {
            height: 150px;
            width: 130px;
            /* border: 3px solid #015B89; */
            border: 3px solid{{ $header_color }};
            object-fit: cover;
        }

        .student-logo {
            margin-top: 20px;
            margin-left: 10px;
        }

        .student-detail {
            margin-left: 160px;
            margin-top: -158px;
            height: 150px;
            margin-right: 10px;
        }

        table tr th {
            text-align: left;
        }

        table tr {
            line-height: 25px;
        }

        .footer {
            /* background-color: #0099CC; */
            background-color: {{ $footer_color }};
            height: 25px;
            margin-top: 22px;
            border-bottom-left-radius: 7px;
            border-bottom-right-radius: 7px;
        }

        .bottom-left {
            padding: 2px 0px;
            text-align: right;
            margin-right: 3%;
            /* color: #fff; */
            /* width: 90%; */
            /* margin-left: 10%; */
            /* float: right; */
            padding-top: 5px;
            color: {{ $font_color }};
            font-weight: 500;
            letter-spacing: 2px;


        }

        .bottom-logo {
            /* width: 10%; */
            float: left;
            margin-top: -10px !important;
        }

        .yadiko-logo {
            margin-left: -10px;
            margin-top: -48px;
        }

        .page-break {
            page-break-after: always;
        }

        .std-id-card {
            text-align: center;
            letter-spacing: 1px;
            font-weight: 500;
            margin-bottom: -10px;
            margin-top: 2px;

        }

        .water_mark {
            position: fixed;
            /* margin-left: 10px; */
            margin-top: -20px;
            opacity: 0.2;
            height: 200px;
            margin-left: 30px;
        }

        .school_logo_div {

            /* margin-top: -10px; */
            margin-left: 10px;
            width: 20%;
            float: left;

        }

        .school_logo_set_new {

            /* margin-top: -40px;
            margin-left: 10px; */
        }
    </style>
</head>

<body>

    <div class="container">
        <div class="body">
            <div class="row">
                @php
                    $counter = 0;
                @endphp
                @foreach ($students as $student)
                    @if ($counter < $print_per_page)
                        @php
                            $counter++;
                        @endphp
                    @else
                        <div class="page-break"> </div>
                        @php
                            $counter = 1;
                        @endphp
                    @endif
                    <div class="card">
                        <div class="header">
                            <div class="top-right" style="position: fixed">

                                <div class="school_title">
                                    <label class="school_name">{{ $school_detail['name'] }}</label>
                                    {{-- <label class="school_name">ST MICHEL'S ACADEMY OF SCIENCE AND ARTS NKWEN BAMENDA</label> --}}

                                </div>
                            </div>
                            <div class="school_logo_div">
                                <img src="{{ public_path($school_detail['logo']) }}" height="60"
                                    class="school_logo_set_new" alt="">
                            </div>
                            {{-- <div class="top-left">
                                <div class="div-border">
                                    <div class="water-mark">
                                        <div class="yadiko-logo">
                                            <img src="{{ public_path($school_detail['logo']) }}" height="60" class="school_logo_left" style="margin-top: 30px" alt="">
                                            
                                        </div>
                                    </div>
                                </div>
                            </div> --}}
                        </div>
                        <div class="std-id-card">
                            <b>{{ __('Student Identification Card') }}</b>

                        </div>
                        <div class="card-body">
                            <div class="student-logo">
                                {{-- <img class="profile"
                                    src="{{ public_path('storage/student/uSuH0aiw2HfN11aFvAryru6LXNdtakoojv55A35a.jpg') }}"
                                    alt=""> --}}
                                @if ($student->user->image)
                                    <img class="profile"
                                        src="{{ public_path('storage/' . $student->user->getRawOriginal('image')) }}"
                                        alt="">
                                @else
                                    <img class="profile"
                                        src="{{ public_path('assets/img/no_image_available.jpg') }}"
                                        alt="">
                                @endif

                            </div>
                            <div class="student-detail">
                                <div class="water_mark" style="float: right;width: 35%">
                                    @if ($school_detail['water_mark'])
                                        <img class="water_mark_image" style="display: block;margin-left: auto; margin-right: auto;"
                                            src="{{ public_path('storage/' . $school_detail['water_mark']) }}"
                                            alt="">
                                    @endif

                                </div>

                                <table style="width:100%;font-size:11px">
                                    @foreach ($student_id_fields as $student_id_field)
                                        <tr>
                                            <th>{{ $student_id_field == 'admission_no' ? __('matricule') : __($student_id_field) }}
                                                :</th>
                                            <td style="width: 68%">{{ $student_id_field == 'admission_no' ? $initial_code . $student->$student_id_field : $student->$student_id_field }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </table>
                            </div>
                        </div>
                        <div class="footer">
                            <div class="bottom-logo">
                                <img src="{{ public_path('storage/yadiko.png') }}" height="45" alt="">
                            </div>
                            <div class="bottom-left">
                                <b>{{ __('Valid until') }} {{ $school_detail['valid'] }}</b>

                            </div>
                        </div>
                    </div>

            </div>
            @endforeach


        </div>
    </div>
    </div>
</body>

</html>
