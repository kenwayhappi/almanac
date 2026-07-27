<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Country;
use App\Models\AdministrativeDivision;
use App\Models\VillageGroup;
use App\Models\Village;
use App\Models\PersonnaliteAdministrative;
use App\Models\Personality;
use App\Models\Activity;
use App\Models\Event;
use App\Models\Professional;

class OuestRegionSeeder extends Seeder
{
    public function run()
    {
        // S'assurer que le pays Cameroun existe
        $country = Country::firstOrCreate(
            ['id' => '237'],
            ['name' => 'Cameroon', 'code' => 'CM']
        );

        // Division 1: Région de l'Ouest
        $regionOuest = AdministrativeDivision::firstOrCreate(
            ['name' => 'Ouest', 'country_id' => $country->id],
            ['type_id' => 4]
        );

        // Division 2: Départements (Haut-Nkam et Menoua)
        $deptHautNkam = AdministrativeDivision::firstOrCreate(
            ['name' => 'Haut-Nkam', 'parent_id' => $regionOuest->id, 'country_id' => $country->id],
            ['type_id' => 5]
        );

        $deptMenoua = AdministrativeDivision::firstOrCreate(
            ['name' => 'Menoua', 'parent_id' => $regionOuest->id, 'country_id' => $country->id],
            ['type_id' => 5]
        );

        // Division 3: Arrondissements
        $arrBafang = AdministrativeDivision::firstOrCreate(
            ['name' => 'Bafang', 'parent_id' => $deptHautNkam->id, 'country_id' => $country->id],
            ['type_id' => 6]
        );

        $arrDschang = AdministrativeDivision::firstOrCreate(
            ['name' => 'Dschang', 'parent_id' => $deptMenoua->id, 'country_id' => $country->id],
            ['type_id' => 6]
        );

        // ==========================================
        // GROUPEMENT 1 : BAFANG
        // ==========================================
        $groupementBafang = VillageGroup::firstOrCreate(
            ['name' => 'Chefferie Supérieure Bafang'],
            [
                'parent_id' => $arrBafang->id,
                'chef_groupement' => 'SM Kameni René',
                'description' => 'La chefferie supérieure de Bafang est un groupement traditionnel majeur du département du Haut-Nkam.',
                'histoire' => 'Le groupement Bafang tire son origine de la grande dynastie Bamiléké, reconnue pour ses traditions séculaires, son commerce et son agriculture dynamique. Il est classé chefferie de 2ème degré.',
            ]
        );

        // Personnalités administratives Bafang
        PersonnaliteAdministrative::firstOrCreate(
            ['nom' => 'Maire', 'prenom' => 'Bafang'],
            [
                'role' => 'Maire',
                'village_group_id' => $groupementBafang->id,
                'biographie' => 'Administration communale de Bafang.'
            ]
        );
        PersonnaliteAdministrative::firstOrCreate(
            ['nom' => 'Sous-Préfet', 'prenom' => 'Bafang'],
            [
                'role' => 'Sous-Préfet',
                'village_group_id' => $groupementBafang->id,
                'biographie' => 'Représentant de l\'État dans l\'arrondissement.'
            ]
        );

        // Villages Bafang (is_village = 1)
        $villageBabone = Village::firstOrCreate(
            ['name' => 'Babone', 'village_group_id' => $groupementBafang->id],
            [
                'is_village' => 1,
                'description' => 'Village traditionnel de Bafang.',
                'population' => 4500,
                'chef_village' => 'Chef Nguatchou',
                'chief_description' => 'Garant des traditions et des coutumes du village Babone.',
            ]
        );
        $villageBassap = Village::firstOrCreate(
            ['name' => 'Bassap', 'village_group_id' => $groupementBafang->id],
            [
                'is_village' => 1,
                'description' => 'Village rural reconnu pour son agriculture.',
                'population' => 3200,
                'chef_village' => 'Chef Tchuente',
                'chief_description' => 'Défenseur des terres agricoles de Bassap.',
            ]
        );

        // Quartier Bafang (is_village = 0)
        $quartierBafang1 = Village::firstOrCreate(
            ['name' => 'Quartier Administratif Bafang', 'village_group_id' => $groupementBafang->id],
            [
                'is_village' => 0,
                'description' => 'Quartier urbain abritant les services administratifs, sans chef coutumier propre (dépend du chef de groupement).',
                'population' => 8500,
                // Pas de chef défini pour un quartier
            ]
        );

        // Ajouts dans les villages Bafang
        Personality::firstOrCreate([
            'name' => 'Élite Babone',
            'village_id' => $villageBabone->id,
            'statut' => 'Homme d\'affaires',
            'description' => 'Grand promoteur du développement local.',
            'has_paid' => 1,
        ]);
        Activity::firstOrCreate([
            'name' => 'Visite des Chutes de la Mouénée',
            'village_id' => $villageBabone->id,
            'type' => 'Tourisme',
            'description' => 'Découverte des impressionnantes chutes d\'eau de Bafang.',
        ]);
        Event::firstOrCreate([
            'name' => 'Fête des récoltes Bafang',
            'village_id' => $villageBabone->id,
            'type' => 'Festival',
            'start_date' => '2026-11-15',
            'end_date' => '2026-11-20',
        ]);
        Professional::firstOrCreate([
            'name' => 'Coopérative Agricole Haut-Nkam',
            'village_id' => $villageBassap->id,
            'profession' => 'Agriculture',
        ]);

        // ==========================================
        // GROUPEMENT 2 : DSCHANG (Foréké-Dschang)
        // ==========================================
        $groupementDschang = VillageGroup::firstOrCreate(
            ['name' => 'Chefferie Supérieure Foréké-Dschang'],
            [
                'parent_id' => $arrDschang->id,
                'chef_groupement' => 'SM Djoumessi III Wamba',
                'description' => 'Groupement majeur de l\'arrondissement de Dschang, pôle historique et universitaire.',
                'histoire' => 'La chefferie Foréké-Dschang est un haut lieu d\'histoire et de culture de la Menoua. Elle abrite une partie de la ville universitaire de Dschang et est impliquée dans la Route des Chefferies.',
            ]
        );

        // Personnalités administratives Dschang
        PersonnaliteAdministrative::firstOrCreate(
            ['nom' => 'Maire', 'prenom' => 'Dschang'],
            [
                'role' => 'Maire',
                'village_group_id' => $groupementDschang->id,
                'biographie' => 'Maire de la Commune de Dschang, pôle d\'excellence.'
            ]
        );

        // Villages Dschang (is_village = 1)
        $villageAtochi = Village::firstOrCreate(
            ['name' => 'Atochi', 'village_group_id' => $groupementDschang->id],
            [
                'is_village' => 1,
                'description' => 'Village rattaché à Foréké-Dschang.',
                'population' => 2800,
                'chef_village' => 'Chef Tiogo',
                'chief_description' => 'Responsable coutumier d\'Atochi.',
            ]
        );
        $villageBanki = Village::firstOrCreate(
            ['name' => 'Banki', 'village_group_id' => $groupementDschang->id],
            [
                'is_village' => 1,
                'description' => 'Village vallonné de la Menoua.',
                'population' => 4100,
                'chef_village' => 'Chef Nkenfac',
                'chief_description' => 'Chef traditionnel de Banki.',
            ]
        );

        // Quartier Dschang (is_village = 0)
        $quartierTsen = Village::firstOrCreate(
            ['name' => 'Quartier Tsen (Centre Dschang)', 'village_group_id' => $groupementDschang->id],
            [
                'is_village' => 0,
                'description' => 'Quartier très actif proche du marché B et du musée des civilisations.',
                'population' => 12000,
            ]
        );

        // Ajouts dans les villages Dschang
        Personality::firstOrCreate([
            'name' => 'Professeur Menoua',
            'village_id' => $villageAtochi->id,
            'statut' => 'Universitaire',
            'description' => 'Enseignant-chercheur natif de Foréké, contribuant au rayonnement de l\'Université de Dschang.',
            'has_paid' => 1,
        ]);
        Activity::firstOrCreate([
            'name' => 'Musée des Civilisations',
            'village_id' => $quartierTsen->id,
            'type' => 'Culture',
            'description' => 'Découverte du riche patrimoine des chefferies de l\'Ouest situé sur les rives du lac municipal.',
        ]);
        Event::firstOrCreate([
            'name' => 'Festival Culturel Macabo',
            'village_id' => $villageBanki->id,
            'type' => 'Festival',
            'start_date' => '2026-12-10',
            'end_date' => '2026-12-15',
        ]);
        Professional::firstOrCreate([
            'name' => 'Artisanat du Bambou',
            'village_id' => $villageAtochi->id,
            'profession' => 'Artisan',
        ]);
    }
}
