@extends('layout.master')

@section('content')

<div class="">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3>@lang('global_report')</h3>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Sequence</th>
                                <th>Action</th>
                                <th>Groups</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($terms as $term)
                                @foreach($term->sequence as $sequence)
                                    <tr>
                                        <td>{{ $sequence->name }}</td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="{{ route('global-report.term', $term->id) }}" class="btn btn-sm btn-outline-primary rounded-lg">@lang('generate_report') Term</a>
                                                <a href="{{ route('global-report.term', ['term' => $term->id, 'compact' => true]) }}" class="btn btn-sm btn-outline-warning rounded-lg">@lang('generate_compact_report') Term</a>
                                                <a href="{{ route('global-report.sequence', $sequence->id) }}" class="btn btn-sm btn-outline-primary rounded-lg">@lang('generate_report')</a>
                                                <a href="{{ route('global-report.sequence', ['sequence' => $sequence->id, 'compact' => true]) }}" class="btn btn-sm btn-outline-warning rounded-lg">@lang('generate_compact_report')</a>
                                            </div>
                                        </td>
                                        <td>
                                            @foreach($groups as $group)
                                                <div class="btn-group">
                                                    <a href="{{ route('global-report.sequence', ['sequence' => $sequence->id, 'groups' => $group->id]) }}" class="btn btn-sm btn-outline-primary rounded-lg">{{ $group->name }}</a>
                                                </div>
                                            @endforeach
                                        </td>
                                    </tr>
                                @endforeach
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection