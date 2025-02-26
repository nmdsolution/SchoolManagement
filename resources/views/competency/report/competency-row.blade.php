@php
    $count = $classCompetency->competencyTypes->count();
    $maxCompetencyTypeMarks = 0;
    $rowTotals = [];
    $competencyTypeTotal = 0;
    // $marks = $sequenceData->marks;
    $competencyCompetencyTypesTotal = 0;
@endphp

@foreach ($classCompetency->competencyTypes as $competencyType)
    <tr>
        @if ($loop->first)
            <td class="left-align" rowspan="{{ $count }}" width="40%">
                <strong>
                    {{ $classCompetency->competency->name }}
                </strong>
            </td>
        @endif
        <td class="left-align">
            {{ $competencyType->name }} ({{ $competencyType->pivot->total_marks }})
        </td>
        @php
            $maxCompetencyTypeMarks += $competencyType->pivot->total_marks;
            $cid = $classCompetency->competency->id;
            $rowTotals[$cid] = 0;
        @endphp
        @foreach ($terms as $term)
            @php
                $termSequences = $term->sequence;
            @endphp

            @foreach ($termSequences as $sequence)
                @php
                    $marks = $sequenceData[$sequence->id]['marks'];
                    // $mark = ($marks && $marks[$classCompetency->competency->id]) && ($marks[$classCompetency->competency->id]['marks'] && $marks[$classCompetency->competency->id]['marks'][$competencyType->id]) ? $marks[$classCompetency->competency->id]['marks'][$competencyType->id]['mark'] : 0;

                    $mark = $marks[$cid]['marks'][$competencyType->id]['mark'] ?? 0;
                    $rowTotals[$sequence->id] = isset($marks[$cid]['total']) ? $marks[$cid]['total'] : 0;
                    $competencyTypeTotal += $mark;
                @endphp

                <td>{{ $mark ?? '-' }}</td>
                @if ($loop->parent->parent->first)
                    <td rowspan="{{ $count }}">{{ number_format($classCompetency->cote ?? 0, 1) }}</td>
                @endif
                @if ($loop->iteration % $termSequences->count() == 0)
                    <td class="bg">
                        <strong>{{ $competencyTypeTotal }}</strong>
                    </td>
                    @php
                        $competencyTypeTotal = 0;
                    @endphp
                @endif
            @endforeach
        @endforeach
    </tr>
@endforeach



<tr class="bg">
    <td colspan="" class="left-align font-bold">
        <strong>
            Total ({{ $maxCompetencyTypeMarks }})
        </strong>
    </td>
    <td></td>
    @foreach ($terms as $term)
        @php
            $termSequences = $term->sequence;
        @endphp
        @foreach ($termSequences as $sequence)
            @php
                $competencyCompetencyTypesTotal += $rowTotals[$sequence->id];
            @endphp

            <td>
                <strong>{{ $rowTotals[$sequence->id] ?? 0 }}</strong>
            </td>
            <td></td>
        @endforeach
        <td>
            {{ $competencyCompetencyTypesTotal }}
        </td>
        @php
            $competencyCompetencyTypesTotal = 0;
        @endphp
    @endforeach
</tr>
