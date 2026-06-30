<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use League\Flysystem\UnableToCheckFileExistence;
use League\Flysystem\UnableToDeleteFile;
use RuntimeException;

class WasabiService
{
    protected function disk()
    {
        if (blank(config('wasabi.access_key')) || blank(config('wasabi.secret_key'))) {
            throw new RuntimeException('Les identifiants Wasabi sont manquants. Définissez WAS_ACCESS_KEY et WAS_SECRET_KEY dans le fichier .env.');
        }

        config([
            'filesystems.disks.wasabi' => [
                'driver' => 's3',
                'key' => config('wasabi.access_key'),
                'secret' => config('wasabi.secret_key'),
                'region' => config('wasabi.region'),
                'bucket' => config('wasabi.bucket'),
                'endpoint' => config('wasabi.endpoint'),
                'url' => config('wasabi.url'),
                'use_path_style_endpoint' => true,
                'throw' => true,
            ],
        ]);

        return Storage::disk('wasabi');
    }

    public function uploadFile(UploadedFile $file, $directory, $prefix = 'file')
    {
        $directory = trim((string) $directory, '/');
        $filename = $prefix . '-' . time() . '-' . Str::random(10) . '.' . $file->getClientOriginalExtension();
        $path = $directory . '/' . $filename;

        $storedPath = $this->disk()->putFileAs(
            $directory,
            $file,
            $filename,
            [
                'ContentType' => $file->getMimeType(),
            ]
        );

        if (!$storedPath) {
            throw new RuntimeException("Impossible d'envoyer le fichier vers Wasabi.");
        }

        return $path;
    }

    public function uploadAvatar(UploadedFile $file)
    {
        return $this->uploadFile(
            $file,
            config('wasabi.avatar_directory', 'images/avatar'),
            'user'
        );
    }

    public function uploadChauffeurImage(UploadedFile $file)
    {
        return $this->uploadFile(
            $file,
            config('wasabi.chauffeur_image_directory', 'images/chauffeur'),
            'chauffeur'
        );
    }

    public function uploadVehicleImage(UploadedFile $file)
    {
        return $this->uploadFile(
            $file,
            config('wasabi.vehicule_image_directory', 'images/vehicules'),
            'vehicule'
        );
    }

    public function uploadAutodocFile(UploadedFile $file)
    {
        return $this->uploadFile(
            $file,
            config('wasabi.autodoc_directory', 'images/autodoc'),
            'autodoc'
        );
    }

    public function uploadPieceImage(UploadedFile $file)
    {
        return $this->uploadFile(
            $file,
            config('wasabi.piece_image_directory', 'images/annonce'),
            'piece'
        );
    }

    public function publicUrl($fileUrl)
    {
        $path = $this->extractPath($fileUrl);

        if (!$path) {
            return null;
        }

        $baseUrl = rtrim((string) config('wasabi.url'), '/');

        if ($baseUrl === '') {
            return $path;
        }

        return $baseUrl . '/' . ltrim($path, '/');
    }

    public function temporaryUrl($fileUrl, $expirationMinutes = 10080)
    {
        $path = $this->extractPath($fileUrl);

        if (!$path) {
            return null;
        }

        try {
            return $this->disk()->temporaryUrl(
                $path,
                now()->addMinutes($expirationMinutes)
            );
        } catch (\Throwable $e) {
            return null;
        }
    }

    public function deleteFile($fileUrl)
    {
        $path = $this->extractPath($fileUrl);

        if (!$path) {
            return;
        }

        try {
            $this->disk()->delete($path);
        } catch (UnableToCheckFileExistence|UnableToDeleteFile $e) {
            return;
        }
    }

    public function extractPath($fileUrl)
    {
        if (empty($fileUrl)) {
            return null;
        }

        if (filter_var($fileUrl, FILTER_VALIDATE_URL)) {
            $path = ltrim(parse_url($fileUrl, PHP_URL_PATH) ?? '', '/');
            $bucket = trim((string) config('wasabi.bucket'), '/');

            if ($bucket !== '' && Str::startsWith($path, $bucket . '/')) {
                return Str::after($path, $bucket . '/');
            }

            return $path ?: null;
        }

        return ltrim($fileUrl, '/');
    }
}
