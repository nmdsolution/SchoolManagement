<div class="accordion" id="changingMarks">
    <div class="accordion-item">
      <h2 class="accordion-header">
        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="false" aria-controls="collapseOne">
          @lang('change_total_or_passing_marks') <i class="fa fa-arrow-right"></i>
        </button>
      </h2>
      <div id="collapseOne" class="accordion-collapse collapse" data-bs-parent="#changingMarks">
        <div class="accordion-body">
            <form action="{{ route('exams.update-total-marks') }}" class="create-form mt-4" data-success-function="updateTotalMarksSuccess" id="total-marks-update-form" style="display: none;">
                <input type="hidden" name="exam_timetable_id" id="exam_timetable_id"/>
                <div class="row">
                    <div class="form-group col-sm-12 col-md-4 local-forms">
                        <label for="">{{ __('Total Marks') }}</label>
                        <input type="number" name="total_marks" id="total_marks" class="form-control"/>
                    </div>
                    <div class="form-group col-sm-12 col-md-4 local-forms">
                        <label for="">{{ __('Passing marks') }}</label>
                        <input type="number" name="passing_marks" id="passing_marks" class="form-control"/>
                    </div>
                    <div class="form-group col-sm-12 col-md-4 local-forms">
                        <input type="submit" value="{{__('update')}}" class="btn btn-primary">
                    </div>
                    <small class="text-danger">NOTE : Please update the Total Marks & Passing Marks first , Before Uploading the Student Marks</small>
                </div>
            </form>
        </div>
      </div>
    </div>
  </div>