<?php

namespace App\Domain\Center\Services;

use App\Application\Services\FileUploadService;
use App\Domain\Center\Repositories\CenterRepository;
use App\Domain\User\Repositories\UserRepository;
use App\Http\Requests\Center\CenterRequest;
use App\Models\Center;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class CenterService
{
    private const DEFAULT_PASSWORD = 'center@123';

    public function __construct(
        private CenterRepository $centerRepository,
        private UserRepository $userRepository,
        private FileUploadService $fileUploadService
    )
    {
        
    }

    public function createCenterWithUser(Request $request, string $defaultPassword): array
    {
        try {
            $user = $this->createCenterUser($request, $defaultPassword);
            $center = $this->createCenter($request, $user->id);

            return [
                'user' => $user,
                'center' => $center,
                'error' => false
            ];
        } catch (\Throwable $e) {
            return [
                'error' => true,
                'message' => $e->getMessage()
            ];
        }
    }

    private function createCenterUser(Request $request, string $defaultPassword): User
    {
        $userData = $this->prepareCenterUserData($request, $defaultPassword);
        $user = $this->userRepository->create($userData);
        $this->userRepository->assignRole($user, 'Center');

        return $user;
    }

    private function createCenterOnly(Request $request, int $userId): Center
    {
        $centerData = $this->prepareCenterData($request, $userId);
        return $this->centerRepository->create($centerData);
    }

    public function createCenter(Request $request): array
    {
        $centerCreatedSuccessfully = false;

        try {
            DB::beginTransaction();

            $user = $this->createCenterUser($request, self::DEFAULT_PASSWORD);
            $center = $this->createCenterOnly($request, $user->id);

            system_installation($center->id);

            $this->sendRegistrationEmail(
                $request,
                self::DEFAULT_PASSWORD
            );

            DB::commit();
            $centerCreatedSuccessfully = true;

            return [
                'error' => false,
                'message' => trans('data_store_successfully'),
                'email' => $request->user_email,
                'password' => self::DEFAULT_PASSWORD
            ];

        } catch (\Throwable $e) {
            DB::rollBack();

            if ($this->isMailError($e)) {
                $centerCreatedSuccessfully = true;
                DB::commit();
                
                return [
                    'warning' => true,
                    'error' => false,
                    'message' => "Center Registered successfully. But Email not sent.",
                    'email' => $request->user_email,
                    'password' => self::DEFAULT_PASSWORD
                ];
            }

            return [
                'error' => true,
                'message' => trans('error_occurred'),
                'data' => $e
            ];
        }
    }

    private function isMailError(\Throwable $e): bool
    {
        return $e instanceof \TypeError && 
               Str::contains($e->getMessage(), ['Mail', 'Mailer', 'MailManager']);
    }

    private function prepareCenterUserData(Request $request, string $defaultPassword): array
    {
        $image = $this->fileUploadService->uploadFile(
            $request->file('user_image'),
            'users'
        );

        return [
            'first_name' => $request->user_first_name,
            'gender' => $request->user_gender,
            'image' => $image,
            'current_address' => $request->user_current_address,
            'permanent_address' => $request->user_permanent_address,
            'email' => $request->user_email,
            'mobile' => $request->user_contact,
            'dob' => date('Y-m-d', strtotime($request->user_dob)),
            'password' => Hash::make($defaultPassword),
        ];
    }

    private function prepareCenterData(Request $request, int $userId): array
    {
        $logo = $this->fileUploadService->uploadFile(
            $request->file('logo'),
            'centers'
        );

        return [
            'name' => $request->name,
            'support_email' => $request->email,
            'support_contact' => $request->contact,
            'logo' => $logo,
            'tagline' => $request->tagline,
            'address' => $request->address,
            'user_id' => $userId,
            'type' => $request->type,
        ];
    }

    private function sendRegistrationEmail($request, $defaultPassword): void
    {
        $email_data = [
            'subject' => 'Center Registration',
            'centerName' => $request->name,
            'adminName' => $request->user_first_name,
            'email' => $request->user_email,
            'password' => $defaultPassword,
        ];

        Mail::send('center.email', $email_data, static function ($message) use ($email_data) {
            $message->to($email_data['email'])->subject($email_data['subject']);
        });
    }

    public function updateCenter(Request $request, int $id): array
    {
        try {
            DB::beginTransaction();

            $center = $this->centerRepository->getById($id);
            
            $this->userRepository->update($center->user, $this->prepareUserData($request));
            $this->centerRepository->update($center, $this->prepareCenterData($request, $center->user->id));

            DB::commit();

            return [
                'error' => false,
                'message' => trans('data_update_successfully')
            ];

        } catch (\Throwable $e) {
            DB::rollBack();
            
            return [
                'error' => true,
                'message' => trans('error_occurred'),
                'data' => $e
            ];
        }
    }

    private function prepareUserData(CenterRequest $request): array
    {
        return [
            'first_name' => $request->user_first_name,
            'last_name' => $request->user_last_name,
            'gender' => $request->user_gender,
            'current_address' => $request->user_current_address,
            'permanent_address' => $request->user_permanent_address,
            'email' => $request->user_email,
            'mobile' => $request->user_contact,
            'dob' => date('Y-m-d', strtotime($request->user_dob))
        ];
    }
}
