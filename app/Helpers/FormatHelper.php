<?php

namespace App\Helpers;

class FormatHelper
{
    /**
     * Formate automatiquement le nom d'un chef pour s'assurer qu'il commence par "S.M."
     * sans répéter les préfixes ("Chef: Chef", "Chef S.M.", "S.M. S.M.").
     *
     * @param string|null $name
     * @return string|null
     */
    public static function formatChefName(?string $name): ?string
    {
        if (empty($name)) {
            return null;
        }

        $clean = trim($name);

        // Supprime tous les préfixes répétitifs au début
        while (preg_match('/^(Chef\s*:?|S\.M\.\s*:?|SM\s*:?|Sa\s+Majesté\s*:?)\s*/i', $clean)) {
            $clean = preg_replace('/^(Chef\s*:?|S\.M\.\s*:?|SM\s*:?|Sa\s+Majesté\s*:?)\s*/i', '', $clean);
            $clean = trim($clean);
        }

        if (empty($clean)) {
            return null;
        }

        return 'S.M. ' . $clean;
    }

    /**
     * Nettoie le nom d'un village pour supprimer le mot répétitif "Village " s'il est présent.
     * Exemple : "Village Hsem Bandjoun" -> "Hsem Bandjoun"
     *
     * @param string|null $name
     * @return string
     */
    public static function cleanVillageName(?string $name): string
    {
        if (empty($name)) {
            return '';
        }
        return trim(preg_replace('/^Village\s+/i', '', trim($name)));
    }

    /**
     * Nettoie les répétitions dans le nom d'un canton ou d'un groupement.
     * Exemple : "Canton Canton Bandjoun" -> "Canton Bandjoun"
     *
     * @param string|null $name
     * @return string
     */
    public static function cleanCantonName(?string $name): string
    {
        if (empty($name)) {
            return '';
        }
        $clean = trim($name);
        $clean = preg_replace('/^(Canton\s+)+Canton\s+/i', 'Canton ', $clean);
        return $clean;
    }
}
