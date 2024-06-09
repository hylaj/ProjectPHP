<?php
/**
 * File upload service.
 */

namespace App\Service;

use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

/**
 * Class FileUploadService.
 */
class FileUploadService implements FileUploadServiceInterface
{
    /**
     * Constructor.
     *
     * @param string           $targetDirectory Target directory
     * @param SluggerInterface $slugger         Slugger
     */
    public function __construct(private readonly string $targetDirectory, private readonly SluggerInterface $slugger)
    {
    }

    /**
     * Upload file.
     *
     * @param UploadedFile $file File to upload
     *
     * @return string Filename of uploaded file
     */
    public function upload(UploadedFile $file): string
    {
        $extension = $file->guessExtension();
        $fileName = '';

        if (null !== $extension) {
            $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $safeFilename = $this->slugger->slug($originalFilename);
            $fileName = $safeFilename.'-'.uniqid().'.'.$extension;
        }

        try {
            $file->move($this->getTargetDirectory(), $fileName);
        } catch (FileException) {
            // ... handle exception if something happens during file upload
        }

        return $fileName;
    }

    /**
     * Getter for target directory.
     *
     * @return string Target directory
     */
    public function getTargetDirectory(): string
    {
        return $this->targetDirectory;
    }
}
