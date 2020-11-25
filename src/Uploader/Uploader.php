<?php

namespace App\Uploader;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class Uploader implements UploaderInterface
{
    private const FILENAME_FORMAT = "%s_%s.%s";

    private string $uploadsRelativeDir; // Bind => config/services.yaml
    private string $uploadsAbsoluteDir;
    private SluggerInterface $slugger;

    public function __construct(string $uploadsRelativeDir, string $uploadsAbsoluteDir, SluggerInterface $slugger)
    {
        $this->uploadsRelativeDir = $uploadsRelativeDir;
        $this->uploadsAbsoluteDir = $uploadsAbsoluteDir;
        $this->slugger = $slugger;
    }

    public function upload(UploadedFile $file): string
    {
        $filename = sprintf(
            self::FILENAME_FORMAT,
            $this->slugger->slug($file->getClientOriginalName()),
            uniqid('', true),
            $file->getClientOriginalExtension()
        );

        $file->move($this->uploadsAbsoluteDir, $filename);

        return $this->uploadsRelativeDir . $filename;
    }

    public function remove(string $path): void
    {
        $filesystem = new Filesystem();

        if ($filesystem->exists($path)) {
            $filesystem->remove($path);
        }
    }
}