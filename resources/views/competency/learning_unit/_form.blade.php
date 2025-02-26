<div class="mb-3">
    <label class="form-label required">@lang('Name')</label>
    <input type="text" name="name" class="form-control" required>
</div>

<div class="mb-3">
    <label class="form-label required">@lang('Class')</label>
    <select name="class_id" class="form-control" required>
        <option value="">@lang('Select Class')</option>
        @foreach($classes as $class)
            <option value="{{ $class->id }}">{{ $class->name }}</option>
        @endforeach
    </select>
</div>

<div class="mb-3">
    <label class="form-label required">@lang('Exam Term')</label>
    <select name="exam_term_id" class="form-control" required>
        <option value="">@lang('Select Exam Term')</option>
        @foreach($examTerms as $term)
            <option value="{{ $term->id }}">{{ $term->name }}</option>
        @endforeach
    </select>
</div>