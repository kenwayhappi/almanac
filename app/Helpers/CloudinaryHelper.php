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

            if (is_object($result) && method_exists($result, 'getSecurePath') && $result->getSecurePath()) {
                return $result->getSecurePath();
            }
            if (is_array($result) && isset($result['secure_url'])) {
                return $result['secure_url'];
            }
            return $result->getPublicId();
        } catch (\Exception $e) {
            Log::error('Cloudinary upload error', [
                'folder' => $folder,
                'file' => is_object($file) ? $file->getClientOriginalName() : 'unknown',
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
     * Retourne l'URL publique d'un fichier Cloudinary.
     * Compatible avec les anciens chemins locaux et types de media (image, video, raw).
     *
     * @param  string|null  $publicId
     * @param  string|null  $type  (ex: 'video', 'photo', 'pdf', 'audio')
     * @return string|null
     */
    public static function url(?string $publicId, ?string $type = null): ?string
    {
        if (!$publicId) {
            return null;
        }

        // Rétrocompatibilité : si c'est déjà une URL complète, on la retourne telle quelle
        if (str_starts_with($publicId, 'http://') || str_starts_with($publicId, 'https://')) {
            return $publicId;
        }

        // Rétrocompatibilité : si c'est un ancien chemin local (ex: 'personalities/abc.jpg')
        if (!str_contains($publicId, 'almanac/')) {
            return asset('storage/' . $publicId);
        }

        try {
            $isVid = ($type === 'video' || $type === 'audio') || preg_match('/\.(mp4|mov|avi|webm|mkv|flv|mp3|wav|ogg)$/i', $publicId);
            $isRaw = ($type === 'pdf' || $type === 'raw') || preg_match('/\.(pdf|doc|docx)$/i', $publicId);

            if ($isVid) {
                return cloudinary()->video($publicId)->toUrl();
            } elseif ($isRaw) {
                return cloudinary()->raw($publicId)->toUrl();
            }

            return cloudinary()->image($publicId)->toUrl();
        } catch (\Exception $e) {
            Log::warning('Cloudinary URL generation error', [
                'public_id' => $publicId,
                'type' => $type,
                'message' => $e->getMessage(),
            ]);
            return null;
        }
    }
}
