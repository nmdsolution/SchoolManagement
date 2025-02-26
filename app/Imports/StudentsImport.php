<?php

namespace App\Imports;

use App\Models\FormField;
use App\Models\Parents;
use App\Models\SessionYear;
use App\Models\Students;
use App\Models\StudentSessions;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Spatie\Permission\Models\Role;

class StudentsImport implements ToCollection, WithHeadingRow {

    public $class_section_id;
    public int $studentCount = 0;
    private array $modifiedRecords = [];
    private const FALLBACK_DATE = '01/01/2000';


    public function __construct($class_section_id) {
        $this->class_section_id = $class_section_id;
    }

    /**
     * @throws \Exception
     */
    private function validateHeaders(Collection $rows): void
    {
        $headerRow = $rows->first();

        $requiredHeaders = [
            'full_name',
            'gender',
            'dob',
            'admission_date',
            'born_at',
        ];

        foreach ($requiredHeaders as $header) {
            if (!isset($headerRow[$header])) {
                throw new \Exception("Missing header: " . $header);
            }
        }
    }

    private function parseDate($date, $fieldName, $rowIndex): string {
        if (empty($date)) {
            $this->logModification($rowIndex, $fieldName, 'Empty date', self::FALLBACK_DATE);
            return self::FALLBACK_DATE;
        }

        // Remove any potential whitespace
        $date = trim($date);

        // Try different date formats
        $formats = [
            'd/m/Y', 'm/d/Y', 'Y/m/d',
            'd-m-Y', 'm-d-Y', 'Y-m-d',
            'Y.m.d', 'd.m.Y', 'm.d.Y',
            'm/d/y'
        ];

        foreach ($formats as $format) {
            try {
                $parsedDate = Carbon::createFromFormat($format, $date);
                // Validate if date is reasonable
                if ($this->isReasonableDate($parsedDate, $fieldName)) {
                    return $parsedDate->format('d/m/Y');
                }
            } catch (\Exception $e) {
                continue;
            }
        }

        // If no format matches or date is unreasonable, use fallback
        $this->logModification($rowIndex, $fieldName, $date, self::FALLBACK_DATE);
        return self::FALLBACK_DATE;
    }

    private function logModification($rowIndex, $field, $originalValue, $newValue): void {
        $this->modifiedRecords[] = [
            'row' => $rowIndex + 2,
            'field' => $field,
            'original' => $originalValue,
            'modified' => $newValue
        ];
    }

    private function isReasonableDate(Carbon $date, string $fieldName): bool {
        $now = Carbon::now();

        return match ($fieldName) {
            'dob' => $date->isBetween(
                $now->copy()->subYears(100),
                $now->copy()->subYears(2)
            ),
            'admission_date' => $date->isBetween(
                $now->copy()->subYears(100),
                $now
            ),
            'father_dob', 'mother_dob', 'guardian_dob' => $date->isBetween(
                $now->copy()->subYears(200),
                $now->copy()->subYears(10)
            ),
            default => true,
        };
    }

    private function sanitizeName($name, $rowIndex): string {
        $original = $name;

        // Remove multiple spaces and trim the name
        $name = preg_replace('/\s+/', ' ', trim($name));

        // Allow French accented characters along with letters, spaces, single quotes, and hyphens
        $name = preg_replace('/[^a-zA-ZÀ-ÿ\s\'-]/u', '', $name);

        // Proper case the name
        $name = ucwords(strtolower($name));

        if ($name !== $original) {
            $this->logModification($rowIndex, 'name', $original, $name);
        }

        return $name;
    }

    public function getModifiedRecords(): array {
        return $this->modifiedRecords;
    }


    private function processStudentRow($row, $index, $sessionYearData): void
    {
        // Parse and validate dates before processing
        $row['dob'] = $this->parseDate($row['dob'], 'dob', $index);

        $row['admission_date'] = $this->parseDate($row['admission_date'], 'admission_date', $index);

        if (isset($row['father_dob'])) {
            $row['father_dob'] = $this->parseDate($row['father_dob'], 'father_dob', $index);
        }
        if (isset($row['mother_dob'])) {
            $row['mother_dob'] = $this->parseDate($row['mother_dob'], 'mother_dob', $index);
        }
        if (isset($row['guardian_dob'])) {
            $row['guardian_dob'] = $this->parseDate($row['guardian_dob'], 'guardian_dob', $index);
        }

        // Process name fields - sanitize and validate
        $row['full_name'] = $this->sanitizeName($row['full_name'], $index);

        if (isset($row['father_full_name'])) {
            $row['father_full_name'] = $this->sanitizeName($row['father_full_name'], $index);
        }

        if (isset($row['mother_full_name'])) {
            $row['mother_full_name'] = $this->sanitizeName($row['mother_full_name'], $index);
        }


        $fatherParentId = $this->createParentIfNotExists($row, 'father');

        $motherParentId = $this->createParentIfNotExists($row, 'mother');

        $guardianParentId = $this->createGuardianIfNotExists($row);

        $admissionNo = $this->generateAdmissionNumber($sessionYearData);

        $user = $this->createUser($row, $admissionNo);

        $student = $this->createStudent($user, $row, $admissionNo, $fatherParentId, $motherParentId, $guardianParentId);

        $this->createStudentSession($student);

        $this->sendCredentialsEmail($row, $user, $admissionNo);

        assign_roll_number($this->class_section_id);
    }

    private function createParentIfNotExists($row, $parentType)
    {
        $emailField = $parentType . '_email';
        $mobileField = $parentType . '_mobile';
        $fullNameField = $parentType . '_full_name';
        $occupationField = $parentType . '_occupation';
        $dobField = $parentType . '_dob';

        if (empty($row[$mobileField])) {
            return null;
        }

        $existingParent = Parents::query()->where('mobile', $row[$mobileField])->first();

        if ($existingParent) {
            return $existingParent->id;
        }

        $plaintextPassword = str_replace('-', '', date('d-m-Y', strtotime(str_replace('/', '-', $row[$dobField]))));

        $user = new User();
        $user->first_name = $row[$fullNameField];
        $user->email = $row[$emailField];
        $user->password = Hash::make($plaintextPassword);
        $user->mobile = $row[$mobileField];
        $user->image = 'dummy_logo.png';
        $user->dob = date('Y-m-d', strtotime(str_replace('/', '-', $row[$dobField])));
        $user->gender = ($parentType === 'father') ? 'Male' : 'Female';
        $user->save();
        $user->assignRole(Role::where('name', 'Parent')->first());

        $parent = new Parents();
        $parent->user_id = $user->id;
        $parent->first_name = $row[$fullNameField];
        $parent->image = 'dummy_logo.png';
        $parent->occupation = $row[$occupationField];
        $parent->mobile = $row[$mobileField];
        $parent->email = $row[$emailField];
        $parent->dob = date('Y-m-d', strtotime(str_replace('/', '-', $row[$dobField])));
        $parent->gender = ($parentType === 'father') ? 'Male' : 'Female';

        $parent->save();

        return $parent->id;
    }

    private function createGuardianIfNotExists($row)
    {
        if ($row['guardian'] !== 'yes' || empty($row['guardian_mobile'])) {
            return null;
        }

        $existingGuardian = Parents::where('mobile', $row['guardian_mobile'])->first();

        if ($existingGuardian) {
            return $existingGuardian->id;
        }

        $guardian = new Parents();
        $guardian->user_id = null;
        $guardian->first_name = $row['guardian_full_name'];
        $guardian->image = 'dummy_logo.png';
        $guardian->occupation = $row['guardian_occupation'];
        $guardian->mobile = $row['guardian_mobile'];
        $guardian->email = $row['guardian_email'];
        $guardian->dob = date('Y-m-d', strtotime(str_replace('/', '-', $row['guardian_dob'])));
        $guardian->gender = $row['guardian_gender'];
        $guardian->save();

        return $guardian->id;
    }

    private function generateAdmissionNumber($sessionYearData): string
    {
        $lastStudent = Students::withTrashed()->latest('id')->first();
        return $sessionYearData->name . ($lastStudent ? $lastStudent->id + 1 : 1);
    }

    private function createUser($row, $admissionNo): User
    {
        $plaintextPassword = str_replace('-', '', date('d-m-Y', strtotime(str_replace('/', '-', $row['dob']))));

        $user = new User();
        $user->password = Hash::make($plaintextPassword);
        $user->first_name = $row['full_name'];
        $user->email = $admissionNo;
        $user->gender = $row['gender'];
        $user->image = 'dummy_logo.png';
        $user->dob = date('Y-m-d', strtotime(str_replace('/', '-', $row['dob'])));
        $user->born_at = $row['born_at'];

        $user->save();
        $user->assignRole(Role::where('name', 'Student')->first());

        return $user;
    }

    private function createStudent($user, $row, $admissionNo, $fatherParentId, $motherParentId, $guardianParentId): Students
    {
        $student = new Students();

        $formFields = FormField::owner()->whereNot('type', 'file')->orderBy('rank', 'ASC')->get();
        $data = array();

        foreach ($formFields as $form_field) {
            if ($form_field->type == 'radio') {
                $data[] = [
                    str_replace(" ", "_", $form_field->name) => $row[strtolower($form_field->name)]
                ];
            } else if ($form_field->type == 'checkbox') {
                $value = $row[strtolower($form_field->name)];
                $values = explode(',', $value);
                $checkbox_data = [];
                foreach ($values as $key => $value) {
                    $checkbox_data[] = '"' . $value . '":"on"';
                }
                $checkbox_data = implode(',', $checkbox_data);

                $json = '{ "' . $form_field->name . '": { ' . $checkbox_data . ' } }';

                $data[] = json_decode($json);
            } else {
                $data[] = [
                    str_replace(" ", "_", $form_field->name) => $row[strtolower($form_field->name)] ?? ""
                ];
            }
        }

        $sessionYearId = getSettings('session_year')['session_year'];

        $student->dynamic_field_values = json_encode($data);
        $student->user_id = $user->id;
        $student->class_section_id = $this->class_section_id;
        $student->session_year_id = $sessionYearId;
        $student->minisec_matricule = $row['minisec_matricule'];
        $student->admission_no = $admissionNo;
        $student->admission_date = date('Y-m-d', strtotime(str_replace('/', '-', $row['admission_date'])));
        $student->father_id = $fatherParentId;
        $student->mother_id = $motherParentId;
        $student->guardian_id = $guardianParentId;

        $student->center_id = get_center_id();
        $student->save();

        return $student;
    }


    private function createStudentSession($student): void
    {
        $studentSession = new StudentSessions();
        $studentSession->student_id = $student->id;
        $studentSession->class_section_id = $this->class_section_id;
        $studentSession->session_year_id = getSettings('session_year')['session_year'];
        $studentSession->promoted = false;
        $studentSession->save();
    }

    private function sendCredentialsEmail($row, $user, $admissionNo): void
    {
        $schoolName = getSettings('school_name')['school_name'];
        $childPlaintextPassword = str_replace('-', '', date('d-m-Y', strtotime(str_replace('/', '-', $row['dob']))));

        $this->sendParentEmail($row, 'father', $schoolName, $childPlaintextPassword, $admissionNo);

        $this->sendParentEmail($row, 'mother', $schoolName, $childPlaintextPassword, $admissionNo);
    }


    private function sendParentEmail($row, $parentType, $schoolName, $childPlaintextPassword, $admissionNo): void
    {
        $emailField = $parentType . '_email';
        $fullNameField = $parentType . '_full_name';
        $dobField = $parentType . '_dob';

        if (!empty($row[$emailField])) {
            $parentPlaintextPassword = str_replace('-', '', date('d-m-Y', strtotime(str_replace('/', '-', $row[$dobField]))));

            $data = [
                'subject' => 'Welcome to ' . $schoolName,
                'email' => $row[$emailField],
                'name' => ' ' . $row[$fullNameField],
                'username' => ' ' . $row[$emailField],
                'password' => ' ' . $parentPlaintextPassword,
                'child_name' => ' ' . $row['full_name'],
                'child_grnumber' => ' ' . $admissionNo,
                'child_password' => ' ' . $childPlaintextPassword,
            ];

            Mail::send('students.email', $data, function ($message) use ($data) {
                $message->to($data['email'])->subject($data['subject']);
            });
        }
    }

    /**
     * @throws \Exception
     */
    public function collection(Collection $rows): void
    {
        $this->validateHeaders($rows);

        $sessionYearData = SessionYear::select('name')->where('id', getSettings('session_year')['session_year'])->first();

        foreach ($rows as $index => $row) {
            $this->processStudentRow($row, $index, $sessionYearData);
        }

        $this->studentCount = count($rows);
    }

}
