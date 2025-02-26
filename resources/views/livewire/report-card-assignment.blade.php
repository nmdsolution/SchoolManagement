<div>
    <form wire:submit.prevent="assignTemplates">
        <table class="table">
            <thead>
            <tr>
                <th>{{ __('class') }}</th>
                <th>{{ __('report_card_type') }}</th>
                <th>{{ __('Report Layout') }}</th>
                <th>{{ __('Report Footer Table') }}</th>
            </tr>
            </thead>
            <tbody>
            @foreach($classes as $class)
                <tr>
                    <!-- Class Name -->
                    <td>{{ $class->name }}</td>

                    <!-- Report Card Type -->
                    <td>
                        <select wire:model="selectedReportType.{{ $class->id }}" class="form-control">
                            @foreach($reportCardTypes as $reportType)
                                <option value="{{ $reportType->id }}">{{ trans($reportType->name) }}</option>
                            @endforeach
                        </select>
                    </td>

                    <!-- Report Layout Dropdown -->
                    <td>
                        <select wire:model="selectedReportLayout.{{ $class->id }}" 
                                class="form-control"
                                wire:change="adjustFooterTable({{ $class->id }})">
                            <option value="0">{{ __('Old Layout Without Competencies') }}</option>
                            <option value="1">{{ __('New Layout With Competencies') }}</option>
                            @if (isPrimaryCenter())
                                <option value="2">{{ __('New Layout With Competencies Without Header') }}</option>
                            @endif
                        </select>
                    </td>

                    <!-- Tables at Footer -->
                    <td>
                        <select wire:model="selectedReportFooterTable.{{ $class->id }}" 
                                class="form-control" 
                                @if($selectedReportLayout[$class->id] ?? '0') disabled @endif>
                            <option value="max">{{ __('maximized') }}</option>
                            <option value="min">{{ __('minimized') }}</option>
                        </select>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>

        <!-- Submit Button -->
        <button type="submit" class="btn btn-primary">{{ __('submit') }}</button>
    </form>
</div>
