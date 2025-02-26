<?php

namespace App\Domain\File\Repositories;

use App\Models\CourseSection;
use App\Models\File;
use App\Models\Lesson;
use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class FileRepository extends BaseRepository
{
    public const TYPE_FILE_UPLOAD = 1;
    public const TYPE_YOUTUBE_LINK = 2;
    public const TYPE_VIDEO_UPLOAD = 3;
    public const TYPE_OTHER_LINK = 4;

    protected string $uploadPath = 'lessons';

    public function __construct(File $model)
    {
        parent::__construct($model);
    }

    public function handleSectionFiles(CourseSection $section, array $files): void
    {
        $oldFileIds = $this->model->where('modal_id', $section->id)->pluck('id')->toArray();
        $newFileIds = [];

        foreach ($files as $file) {
            $newFileIds[] = $this->updateOrCreateFile($section, $file);
        }

        $deletedFileIds = array_diff($oldFileIds, $newFileIds);
        if (!empty($deletedFileIds)) {
            $this->deleteFiles($deletedFileIds);
        }
    }

    private function deleteFiles(array $fileIds): void
    {
        $files = $this->getByIds($fileIds);
        
        foreach ($files as $file) {
            if (Storage::disk('public')->exists($file->getRawOriginal('file_url'))) {
                Storage::disk('public')->delete($file->getRawOriginal('file_url'));
            }
        }
        
        $this->delete($fileIds);
    }

    public function deleteByModalIds(array $modalIds): void
    {
        $files = $this->model->whereIn('modal_id', $modalIds)->get();
        foreach ($files as $file) {
            if (Storage::disk('public')->exists($file->getRawOriginal('file_url'))) {
                Storage::disk('public')->delete($file->getRawOriginal('file_url'));
            }
        }
        $this->model->whereIn('modal_id', $modalIds)->delete();
    }

    private function updateOrCreateFile(CourseSection $section, array $fileData): int
    {
        $downloadable = isset($fileData['downloadable']) 
            ? implode(',', $fileData['downloadable']) 
            : '0';

        $attributes = [
            'modal_type' => CourseSection::class,
            'file_name' => $fileData['file_name'],
            'type' => $fileData['file_type'],
            'modal_id' => $section->id,
            'downloadable' => $downloadable
        ];

        if (!empty($fileData['file'])) {
            $attributes['file_url'] = $fileData['file']->store('course_material', 'public');
        }

        $file = $this->updateOrCreate(
            ['id' => $fileData['file_id'] ?? null],
            $attributes
        );

        return $file->id;
    }

    public function getFirstBySectionId(int $sectionId): ?File
    {
        return $this->model
            ->where('modal_id', $sectionId)
            ->where('modal_type', CourseSection::class)
            ->first();
    }

    public function createLessonFile(Lesson $lesson, array $fileData): File
    {
        $file = new File();
        $file->file_name = $fileData['name'];
        $file->modal()->associate($lesson);

        $this->setFileAttributes($file, $fileData);
        $this->create($file);

        return $file;
    }

    private function setFileAttributes(File $file, array $data): void
    {
        switch ($data['type']) {
            case 'file_upload':
                $file->type = 1;
                $file->file_url = $data['file']->store('lessons', 'public');
                break;

            case 'youtube_link':
                $file->type = 2;
                $this->handleThumbnail($file, $data['thumbnail']);
                $file->file_url = $data['link'];
                break;

            case 'video_upload':
                $file->type = 3;
                $this->handleThumbnail($file, $data['thumbnail']);
                $file->file_url = $data['file']->store('lessons', 'public');
                break;

            case 'other_link':
                $file->type = 4;
                $this->handleThumbnail($file, $data['thumbnail']);
                $file->file_url = $data['link'];
                break;
        }
    }

    private function handleThumbnail(File $file, UploadedFile $thumbnail): void
    {
        $fileName = time() . '-' . $thumbnail->getClientOriginalName();
        $filePath = 'lessons/' . $fileName;
        
        resizeImage($thumbnail);
        
        $destinationPath = storage_path('app/public/lessons');
        $thumbnail->move($destinationPath, $fileName);

        $file->file_thumbnail = $filePath;
    }

    public function updateLessonFile(array $fileData): void
    {
        $file = $this->getByIdOrFail($fileData['id']);
        $file->file_name = $fileData['name'];

        $this->updateFileAttributes($file, $fileData);
        $this->update($file->toArray(), $file->id);
    }

    private function updateFileAttributes(File $file, array $data): void
    {
        switch ($data['type']) {
            case 'file_upload':
                $this->handleFileUpload($file, $data);
                break;
            case 'youtube_link':
                $this->handleYoutubeLink($file, $data);
                break;
            case 'video_upload':
                $this->handleVideoUpload($file, $data);
                break;
            case 'other_link':
                $this->handleOtherLink($file, $data);
                break;
        }
    }

    private function handleFileUpload(File $file, array $data): void
    {
        $file->type = 1;
        if (!empty($data['file'])) {
            $this->deleteExistingFile($file->getRawOriginal('file_url'));
            $file->file_url = $data['file']->store('lessons', 'public');
        }
    }

    private function handleYoutubeLink(File $file, array $data): void
    {
        $file->type = 2;
        if (!empty($data['thumbnail'])) {
            $this->handleThumbnailUpdate($file, $data['thumbnail']);
        }
        $file->file_url = $data['link'];
    }

    private function handleVideoUpload(File $file, array $data): void
    {
        $file->type = 3;
        if (!empty($data['file'])) {
            $this->deleteExistingFile($file->getRawOriginal('file_url'));
            $file->file_url = $data['file']->store('lessons', 'public');
        }
        if (!empty($data['thumbnail'])) {
            $this->handleThumbnailUpdate($file, $data['thumbnail']);
        }
    }

    private function handleOtherLink(File $file, array $data): void
    {
        $file->type = 4;
        if (!empty($data['thumbnail'])) {
            $this->handleThumbnailUpdate($file, $data['thumbnail']);
        }
        $file->file_url = $data['link'];
    }

    private function handleThumbnailUpdate(File $file, UploadedFile $thumbnail): void
    {
        $this->deleteExistingFile($file->getRawOriginal('file_thumbnail'));
        $file->file_thumbnail = $this->saveThumbnail($thumbnail);
    }

    private function deleteExistingFile(?string $path): void
    {
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }

    private function saveThumbnail(UploadedFile $thumbnail): string
    {
        $fileName = time() . '-' . $thumbnail->getClientOriginalName();
        $filePath = 'lessons/' . $fileName;
        
        resizeImage($thumbnail);
        
        $destinationPath = storage_path('app/public/lessons');
        $thumbnail->move($destinationPath, $fileName);

        return $filePath;
    }

    public function safeDelete(int $fileId): void
    {
        $file = $this->getByIdOrFail($fileId);
        
        $this->deleteStorageFile($file->getRawOriginal('file_url'));
        $this->deleteStorageFile($file->getRawOriginal('file_thumbnail'));
        
        $this->delete($fileId);
    }

    private function deleteStorageFile(?string $path): void
    {
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }

    private array $typeMap = [
        'file_upload' => self::TYPE_FILE_UPLOAD,
        'youtube_link' => self::TYPE_YOUTUBE_LINK,
        'video_upload' => self::TYPE_VIDEO_UPLOAD,
        'other_link' => self::TYPE_OTHER_LINK
    ];

    public function createFile(Model $model, array $fileData): File
    {
        $file = new File();
        $file->file_name = $fileData['name'];
        $file->modal()->associate($model);
        $file->type = $this->typeMap[$fileData['type']] ?? null;

        $this->setFileTypeAndUrls($file, $fileData);
        
        return $this->create($file);
    }

    private function setFileTypeAndUrls(File $file, array $data): void
    {
        switch ($file->type) {
            case self::TYPE_FILE_UPLOAD:
                $file->file_url = $this->storeFile($data['file']);
                break;

            case self::TYPE_YOUTUBE_LINK:
                $file->file_thumbnail = $this->saveThumbnail($data['thumbnail']);
                $file->file_url = $data['link'];
                break;

            case self::TYPE_VIDEO_UPLOAD:
                $file->file_thumbnail = $this->saveThumbnail($data['thumbnail']);
                $file->file_url = $this->storeFile($data['file']);
                break;

            case self::TYPE_OTHER_LINK:
                $file->file_thumbnail = $this->saveThumbnail($data['thumbnail']);
                $file->file_url = $data['link'];
                break;
        }
    }

    private function storeFile(UploadedFile $file): string
    {
        return $file->store($this->uploadPath, 'public');
    }
}