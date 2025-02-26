<?php

namespace App\Services;

use App\Models\ClassSection;
use App\Models\Exam;
use App\Models\ExamClassSection;
use App\Models\ExamTimetable;
use App\Models\SessionYear;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Throwable;

class CenterService {

    /**
     * @param $centerID
     * @return bool
     * @throws Throwable
     */
    public static function preSetup($centerID) {
        try {
            DB::beginTransaction();
            SessionYear::

            DB::commit();
            return true;
        } catch (Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * @param array $TermSequence array [
     * <pre>
     *  [
     *      term_id=>1, Required
     *      sequence_id=>1 Required
     *  ],
     *  [
     *      term_id=>1, Required
     *      sequence_id=>1 Required
     *  ],
     * ]
     * @param array|null $classSectionID
     * @return bool
     * @throws Throwable
     */
    public static function createDummyExams(array $TermSequence, array $classSectionID = null) {
        try {
            DB::beginTransaction();
            $classSections = ClassSection::with('subjects:subjects.id,name');
            if (!empty($classSectionID)) {
                $classSections->whereIn('id', $classSectionID);
            }
            $classSections = $classSections->get();
            $examClassSection = $examTimetables = [];
            $setting = getSettings('session_year');
            foreach ($TermSequence as $data) {
                foreach ($classSections as $classSection) {
                    foreach ($classSection->subjects as $subject) {
                        $exam = Exam::create([
                            'name'             => $subject->name . " Exam",
                            'type'             => 1,
                            'session_year_id'  => $setting['session_year'],
                            'exam_term_id'     => $data['term_id'],
                            'exam_sequence_id' => $data['sequence_id'],
                            'center_id'        => Auth::user()->center->id
                        ]);

                        $examClassSection[] = [
                            'exam_id'          => $exam->id,
                            'class_section_id' => $classSection->id
                        ];

                        $examTimetables[] = [
                            'exam_id'          => $exam->id,
                            'class_section_id' => $classSection->id,
                            'subject_id'       => $subject->id,
                            'session_year_id'  => $setting['session_year'],
                            'total_marks'      => 20,
                            'passing_marks'    => 10,
                        ];
                    }
                }
            }
            ExamClassSection::insert($examClassSection);
            ExamTimetable::insert($examTimetables);
            DB::commit();
            return true;
        } catch (Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

}