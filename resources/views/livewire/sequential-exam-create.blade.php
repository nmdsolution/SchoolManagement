<div>
    <form wire:submit.prevent="submit" class="teacher-create-exam-validate pt-3 mt-6">
        <div class="row">
            <div class="row">
                <div class="form-group col-sm-12 col-md-6 local-forms sequential-exam">
                    <label>{{ __('Exam Terms') }} <span class="text-danger">*</span></label>
                    <select wire:model.live="exam_term_id" class="form-control" required>
                        @if(count($exam_terms))
                            <option value="">--{{ __('Select Exam Term') }}--</option>
                        @else
                            <option value="">--{{ __('no_data_found') }}--</option>
                        @endif
                        @foreach ($exam_terms as $term)
                            <option value="{{ $term->id }}">{{ $term->name }}</option>
                        @endforeach
                    </select>
                </div>

                @isset($exam_term_id)
                    <div class="form-group col-sm-12 col-md-6 local-forms sequential-exam">
                        <label>{{ __('Exam Sequences') }} <span class="text-danger">*</span></label>
                        <select wire:model="exam_sequence_id" class="form-control" required>
                            @if(count($sequences))
                                <option value="">--{{ __('Select Exam Sequence') }}--</option>
                            @else
                                <option value="">--{{ __('no_data_found') }}--</option>
                            @endif
                            @foreach ($sequences as $sequence)
                                <option value="{{ $sequence->id }}">{{ $sequence->name }}</option>
                            @endforeach
                        </select>
                    </div>
                @endisset
            </div>

            <div class="row">
                <div class="form-group col-sm-12 col-md-6 local-forms">
                    <label>{{ __('Class Section') }} <span class="text-danger">*</span></label>
                    <select wire:model="class_section_id" class="form-control" required>
                        @if($class_sections)
                            <option value="">--{{ __('Select Class Section') }}--</option>
                        @else
                            <option value="">--{{ __('no_data_found') }}--</option>
                        @endif
                        @foreach ($class_sections as $class_section)
                            <option value="{{ $class_section->id }}">{{ $class_section->full_name }}</option>
                        @endforeach
                    </select>
                </div>

                @if(isset($class_section_id) && isset($exam_sequence_id))
                    <div class="form-group col-md-6 local-forms">
                        <label>{{ __('Subject') }} <span class="text-danger">*</span></label>
                        <select wire:model="timetable_subject_id" class="form-control exam_subjects_options" required>
                            @if(count($timetable_subjects))
                                <option value="">--{{ __('Select') }}--</option>
                            @else
                                <option value="">--{{ __('all_subjects_set') }}--</option>
                            @endif
                            @foreach ($timetable_subjects as $subject)
                                <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif
            </div>

            <div class="form-group col-md-12 local-forms">
                <label>{{ __('Description') }}</label>
                <textarea wire:model.debounce.500ms="description" class="form-control" placeholder="{{ __('Exam Description') }}"></textarea>
            </div>
        </div>

        @if ($exam_term_id && $exam_sequence_id && $class_section_id && $timetable_subject_id )
            <input class="btn btn-primary" type="submit" value="{{ __('Submit') }}">
        @endif
    </form>

</div>
