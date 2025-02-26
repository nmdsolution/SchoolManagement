@extends('layout.master')

@section('title')
    {{ __('update_section_for_class') }}
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">
                {{ trans("Update Class Section For: ") . $class->name }}
            </h3>
        </div>

        <div class="row">
            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">

                        <form id="update-section-form" method="POST" action="{{ route("class.rename") }}">
                            @csrf

                            <div class="row my-4 justify-content-around">

                                <div class="col-md-5 col-sm-12">
                                    <div class="form-group">
                                        <label>Current Section</label>
                                        <select name="from_section_id" id="from_section_id" class="form-control select2"
                                                style="width:100%;" tabindex="-1"
                                                aria-hidden="true">
                                            @foreach($currentSections as $section)
                                                <option class="font-bold text-xl" value="{{$section->id}}">{{$section->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div id="from_list" class="row justify-content-between"></div>
                                </div>

                                <input type="text" name="class_id" value="{{ $class->id }}" hidden />

                                <div class="col-md-5">
                                    <div class="form-group">
                                        <label>To Section</label>
                                        <select name="to_section_id" id="to_section_id" class="form-control select2" style="width:100%;"
                                                tabindex="-1"
                                                aria-hidden="true">
                                            @php
                                                $list = $currentSections->pluck('id')->toArray();
                                            @endphp
                                            @foreach($sections as $section)
                                                @if (!in_array($section->id, $list))
                                                    <option value="{{$section->id}}">{{$section->name}}</option>
                                                @endif
                                            @endforeach
                                        </select>
                                    </div>

                                    <div id="to_list" class="row justify-content-between">

                                    </div>
                                </div>

                            </div>

                            <div class="d-flex justify-content-center align-items-center mt-3">
                                <button class="btn btn-outline-primary" type="submit">Transfer Section</button>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script type="text/javascript">

        $(document).ready(function () {
            fromSectionClasses();
            toSectionClasses();
        });

        const fromSection = $("#from_section_id");

        const toSection = $("#to_section_id");

        function fromSectionClasses() {
            $.ajax({
                url: '/get-section-classes',
                type: 'GET',
                data: {
                    id: fromSection.val()
                },
                success: function(response) {
                    console.log(response);
                    let html = '';
                    for (let i = 0; i < response.length; i++) {
                        html += '<div class="col-md-5">'+ response[i] + '</div>'
                    }
                    $('#from_list').html(html);
                },
                error: function(error) {
                    console.log("error fetching data", error);
                }
            });
        }

        function toSectionClasses() {
            $.ajax({
                url: '/get-section-classes',
                type: 'GET',
                data: {
                    id: toSection.val()
                },
                success: function(response) {
                    let html = '';
                    for (let i = 0; i < response.length; i++) {
                        html += '<div class="col-md-5">'+ response[i] + '</div>'
                    }
                    $('#to_list').html(html);
                },
                error: function(error) {
                    console.log("error fetching data", error);
                }
            });
        }

        function submitForm() {
            $.ajax({
                url: '/class-section/rename',
                type: 'POST',
                data: {
                    from_section_id: fromSection.val(),
                    to_section_id: toSection.val(),
                    class_id: {{ $class->id }}
                },
                success: function(response) {
                    fromSectionClasses();
                    toSectionClasses();
                    successFunction(response);
                },
                error: function(error) {
                    console.log("error fetching data", error);
                }
            });
        }

        function successFunction(response) {
            Swal.fire('Class transferred Successfully!', response);
        }

        fromSection.on('change', function () {
            fromSectionClasses();
        });

        toSection.on("change", function () {
            toSectionClasses();
        });

        $("#update-section-form").on('submit', function (event) {
            event.preventDefault();
            submitForm();
        });

    </script>
@endsection
