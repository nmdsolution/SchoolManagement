<?php

namespace App\Domain\Lesson\Repositories;

use App\Domain\File\Repositories\FileRepository;
use App\Models\File;
use App\Models\LessonTopic;
use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;

class LessonTopicFileRepository extends BaseRepository
{
    protected string $uploadPath = 'lessons';

    public function __construct(File $file, private FileRepository $fileRepository)
    {
        parent::__construct($file);
    }

    public function createTopicFile(LessonTopic $topic, array $fileData): File
    {
        $file = new File();
        $file->file_name = $fileData['name'];
        $file->modal()->associate($topic);

        $this->setFileAttributes($file, $fileData);
        $this->create($file);

        return $file;
    }

    private function setFileAttributes(File &$file, array $data): void
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

    private function handleFileUpload(File &$file, array $data): void
    {
        $file->type = 1;
        $file->file_url = $data['file']->store($this->uploadPath, 'public');
    }

    private function handleYoutubeLink(File &$file, array $data): void
    {
        $file->type = 2;
        $file->file_thumbnail = $this->saveThumbnail($data['thumbnail']);
        $file->file_url = $data['link'];
    }

    private function handleVideoUpload(File &$file, array $data): void
    {
        $file->type = 3;
        $file->file_thumbnail = $this->saveThumbnail($data['thumbnail']);
        $file->file_url = $data['file']->store($this->uploadPath, 'public');
    }

    private function handleOtherLink(File &$file, array $data): void
    {
        $file->type = 4;
        $file->file_thumbnail = $this->saveThumbnail($data['thumbnail']);
        $file->file_url = $data['link'];
    }

    private function saveThumbnail(UploadedFile $thumbnail): string
    {
        $fileName = time() . '-' . $thumbnail->getClientOriginalName();
        $filePath = $this->uploadPath . '/' . $fileName;
        
        resizeImage($thumbnail);
        
        $destinationPath = storage_path('app/public/' . $this->uploadPath);
        $thumbnail->move($destinationPath, $fileName);

        return $filePath;
    }

    public function updateFiles(array $files): void
    {
        foreach ($files as $file) {
            if ($file['type']) {
                $this->updateFile($file);
            }
        }
    }

    public function createFiles(Model $model, array $files): void
    {
        foreach ($files as $file) {
            $this->fileRepository->createFile($model, $file);
        }
    }

    private function updateFile(array $fileData): void
    {
        $file = $this->getByIdOrFail($fileData['id']);
        $file->file_name = $fileData['name'];

        $this->handleFileType($file, $fileData);
        $this->update($file->toArray(), $file->id);
    }

    private function handleFileType(File $file, array $data): void
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
}