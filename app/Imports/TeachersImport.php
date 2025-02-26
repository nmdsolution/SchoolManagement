<?php

namespace App\Imports;

use App\Models\CenterTeacher;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class TeachersImport implements ToCollection, WithHeadingRow
{
    private string $message = "";

    private string $successInfo = "";

    private bool $errorOccurred = false;

    private $count = 0;

    public function getMessage(): string {
        return $this->message;
    }

    public function getSuccessInfo(): string {
        return $this->successInfo;
    }


    public function collection(Collection $rows)
    {
        $validator = Validator::make($rows->toArray(), [
            '*.full_name' => 'required',
            '*.mobile' => 'nullable|regex:/^([0-9\s\-\+\(\)]*)$/',
            '*.gender' => ['required', Rule::in(['Male', 'Female', "M", "F", "m", "f"])],
            '*.date_of_birth' => 'required|date_format:d/m/Y',
            '*.email' => 'nullable|email|unique:users,email',
        ],[
            '*.full_name.required' => trans("full_name_required"),
            '*.gender.required' => trans("gender_required"),
            '*.gender.in' => trans("invalid_gender"),
            '*.date_of_birth.required' => trans("required_date_of_birth"),
            '*.date_of_birth.date_format' => trans("invalid_date_format"),
            '*.email.email' => trans("invalid_email_address"),
            '*.email.unique' => trans("email_already_taken"),
        ]);

        if ($validator->fails()) {
            $this->errorOccurred = true;
            $this->message = $this->generateUploadErrors($validator->errors()->toArray());
        }

        if (!$this->errorOccurred) {

            foreach ($rows as $row) {

                $this->count++;

                $user = $this->createUser($row);

                $user->assignRole('Teacher');

                $teacher = $this->createTeacher($user, $row);

                $this->createCenterTeacher($user, $teacher);
            }

            $this->successInfo = " $this->count Teachers have been created";
        }
    }

    private function createUser($row)
    {
        $genderFirst = strtolower($row['gender'])[0] ?? 'm';

        if ($genderFirst == "m") {
            $gender = "Male";
        } else {
            $gender = "Female";
        }

        $dob = \DateTime::createFromFormat('d/m/Y', $row['date_of_birth']);

        return User::query()->firstOrCreate(['email' => $row['email'] ?? $this->generateRandomEmail()], [
            'first_name' => $row['full_name'],
            'gender' => $gender,
            'password' => Hash::make(Str::remove('-', $row['date_of_birth'])),
            'mobile' => $row['mobile'],
            'dob' => $dob->format('Y-m-d'),
            'current_address' => $row['current_address'],
            'permanent_address' => $row['permanent_address'],
        ]);
    }

    private function createTeacher($user, $row): \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Eloquent\Builder
    {
        return Teacher::query()->firstOrCreate(['user_id' => $user->id], [
            'qualification' => $row['qualification'],
            'salary' => $row['salary'],
        ]);
    }

    private function createCenterTeacher($user, $teacher): void
    {
        CenterTeacher::query()->firstOrCreate([
            'center_id' => Auth::user()->center->id,
            'teacher_id' => $teacher?->id,
            'user_id' => $user->id,
        ]);
    }

    private function emailExist($email) {
        $user = User::query()->where('email', $email)->first();
        return (bool) $user;
    }

    private function generateRandomEmail(): string
    {
        try {
            do {
                $generateEmail = bin2hex(random_bytes(3));

            } while ($this->emailExist($generateEmail));

            return  $generateEmail . "@yadiko.com";

        }catch (\Throwable $e) {
            $this->errorOccurred = true;
            $this->message = "An Error Occured while generating email";
            return "";
        }
    }

    public function generateUploadErrors($messages): string
    {
        $errorMessages = [];

        foreach ($messages as $field => $errors) {

            foreach ($errors as $error) {
                $values = explode('.', $field);

                $lineNumber = intval($values[0]) + 2;

                if (!isset($errorMessages[$error])) {
                    $errorMessages[$error] = [];
                }

                $errorMessages[$error][] = $lineNumber;
            }
        }

        $formattedErrors = [];
        foreach ($errorMessages as $error => $lines) {
            $lineNumbers = implode(', ', $lines);
            $formattedErrors[] = "$lineNumbers :  $error";
        }


        return implode("<br />", $formattedErrors);
    }
}
