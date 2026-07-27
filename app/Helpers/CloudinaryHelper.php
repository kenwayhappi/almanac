<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Log;

/**
 * Helper centralise pour la gestion du stockage Cloudinary.
 * Remplace les appels a Storage::disk('public') pour un hebergement persistant.
 */
class CloudinaryHelper
{
    /**
     * Upload un fichier vers Cloudinary et retourne le public_id.
     *
     * @param  \Illuminate\Http\UploadedFile  $file
     * @param  string  $folder  Dossier Cloudinary (ex: 'personalities', 'villages/chiefs')
     * @return string|null  Le public_id Cloudinary (stocke en base de donnees)
     */
    public static function upload($file, string $folder): ?string
    {
        try {
            $result = cloudinary()->upload($file->getRealPath(), [
                'folder' => 'almanac/' . $folder,
                'resource_type' => 'auto',
            ]);
            return $result->getPublicId();
        } catch (\Exception $e) {
            Log::error('Cloudinary upload error', [
                'folder' => $folder,
                'message' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Supprime un fichier de Cloudinary par son public_id.
     *
     * @param  string|null  $publicId
     * @return void
     */
    public static function delete(?string $publicId): void
    {
        if (!$publicId) {
            return;
        }

        // Si c''est deja une URL complete (ancien stockage local), on ignore
        if (str_starts_with($publicId, 'http://') || str_starts_with($publicId, 'https://')) {
            return;
        }

        // Si c''est un ancien chemin local (pas de prefixe almanac/), on ignore aussi
        if (!str_contains($publicId, 'almanac/')) {
            return;
        }

        try {
            cloudinary()->destroy($publicId);
            Log::info('Cloudinary file deleted', ['public_id' => $publicId]);
        } catch (\Exception $e) {
            Log::warning('Cloudinary delete error', [
                'public_id' => $publicId,
                'message' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Retourne l''URL publique d''un fichier Cloudinary.
     * Compatible avec les anciens chemins locaux (commence par ''http'' ou ancien path).
     *
     * @param  string|null  $publicId
     * @return string|null
     */
    public static function url(?string $publicId): ?string
    {
        if (!$publicId) {
            return null;
        }

        // Retrocompatibilite : si c''est deja une URL complete, on la retourne telle quelle
        if (str_starts_with($publicId, 'http://') || str_starts_with($publicId, 'https://')) {
            return $publicId;
        }

        // Retrocompatibilite : si c''est un ancien chemin local (ex: 'personalities/abc.jpg')
        if (!str_contains($publicId, 'almanac/')) {
            return asset('storage/' . $publicId);
        }

        try {
            return cloudinary()->image($publicId)->toUrl();
        } catch (\Exception $e) {
            Log::warning('Cloudinary URL generation error', [
                'public_id' => $publicId,
                'message' => $e->getMessage(),
            ]);
            return null;
        }
    }
}
