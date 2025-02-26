<?php

namespace App\Domain\Center\Repositories;

use App\Application\Services\FileUploadService;
use App\Models\Center;
use App\Repositories\BaseRepository;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class CenterRepository extends BaseRepository
{
    public function __construct(Center $center, private FileUploadService $fileUploadService)
    {
        parent::__construct($center);
    }

    public function update($data, $id = 0, $attributes = 'id'): bool
    {
        $center = $this->getById($id);

        if (isset($data['logo'])) {
            $this->updateLogo($center, $data['logo']);
        }

        $center->fill($data);
        return $center->save();
    }

    private function updateLogo(Center $center, UploadedFile $newLogo): void
    {
        if (Storage::disk('public')->exists($center->getRawOriginal('logo'))) {
            Storage::disk('public')->delete($center->getRawOriginal('logo'));
        }
        
        $center->logo = $this->fileUploadService->uploadFile($newLogo, 'centers');
    }
}