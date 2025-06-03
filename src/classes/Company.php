<?php

// src/classes/Company.php

declare(strict_types=1);



class Company
{

    private Database $db;

    private string $companiesTable;



    public function __construct(Database $db)
    {

        $this->db = $db;

        $this->companiesTable = defined('TABLE_COMPANIES') ? TABLE_COMPANIES : 'imones_rekvizitai';

    }



    public function getAllCompanies(?string $searchTerm = null, int $page = 1, int $limit = 100): array
    {

        // Parenkame tik reikiamus stulpelius sąrašo rodymui, kad būtų efektyviau

        $sql = "SELECT id, pavadinimas, imones_kodas, pvm_kodas, logotipas FROM " . $this->companiesTable;



        $trimmedSearchTerm = null;

        if ($searchTerm !== null) {

            $trimmedSearchTerm = trim($searchTerm);

        }



        if ($trimmedSearchTerm && !empty($trimmedSearchTerm)) { // Tikriname, ar po trim() liko ne tuščia reikšmė

            // Jei yra paieškos terminas, pridedame WHERE sąlygą

            // Naudojame skirtingus placeholderių pavadinimus

            $sql .= " WHERE pavadinimas LIKE :searchPavadinimas OR imones_kodas LIKE :searchImonesKodas";

        }



        $sql .= " ORDER BY pavadinimas ASC"; // Rūšiuojame pagal pavadinimą

        // Pridedame LIMIT ir OFFSET SQL užklausai
        $offset = ($page - 1) * $limit;
        $sql .= " LIMIT :limit OFFSET :offset";

        $this->db->query($sql);



        if ($trimmedSearchTerm && !empty($trimmedSearchTerm)) { // Susiejame parametrus, jei jie naudojami SQL

            $searchTermWildcard = '%' . $trimmedSearchTerm . '%';

            $this->db->bind(':searchPavadinimas', $searchTermWildcard);

            $this->db->bind(':searchImonesKodas', $searchTermWildcard);

        }

        // Susiejame :limit ir :offset parametrus
        $this->db->bind(':limit', $limit, \PDO::PARAM_INT);
        $this->db->bind(':offset', $offset, \PDO::PARAM_INT);

        return $this->db->resultSet();

    }



    public function getCompanyById(int $id)
    {

        $this->db->query("SELECT * FROM " . $this->companiesTable . " WHERE id = :id");

        $this->db->bind(':id', $id);

        return $this->db->single();

    }

    public function getTotalCompaniesCount(): int
    {
        try {
            $sql = "SELECT COUNT(*) as total FROM " . $this->companiesTable;
            $this->db->query($sql);
            $result = $this->db->single();
            return (int)($result['total'] ?? 0);
        } catch (PDOException $e) {
            error_log("Error getting total companies count: " . $e->getMessage());
            return 0;
        }
    }



    /**

     * Ieško įmonės pagal jos kodą.

     * @param string $code Įmonės kodas.

     * @return mixed Įmonės duomenys (masyvas) arba false, jei nerasta.

     */

    public function findCompanyByCode(string $code)
    {

        $this->db->query("SELECT id FROM " . $this->companiesTable . " WHERE imones_kodas = :imones_kodas");

        $this->db->bind(':imones_kodas', $code);

        return $this->db->single();

    }

    /**

     * Sukuria naują įmonę.

     * @param array $data Duomenys iš formos. Papildomai tikisi 'logotipas_filename' jei logotipas buvo įkeltas.

     * @return bool True, jei sėkmingai, false - kitu atveju.

     */

    public function createCompany(array $data): bool
    {

        if (empty($data['pavadinimas']) || empty($data['imones_kodas'])) {

            return false;

        }



        try {

            $sql = "INSERT INTO " . $this->companiesTable . " 
                    (pavadinimas, imones_kodas, pvm_kodas, adresas_salis, adresas_miestas, adresas_gatve, adresas_pasto_kodas, 
                    telefonas, el_pastas, banko_pavadinimas, banko_saskaita, kontaktinis_asmuo, pastabos,
                    logotipas, vadovas_vardas_pavarde, tinklalapis, darbo_laikas)
                    VALUES 
                    (:pavadinimas, :imones_kodas, :pvm_kodas, :adresas_salis, :adresas_miestas, :adresas_gatve, :adresas_pasto_kodas, 
                    :telefonas, :el_pastas, :banko_pavadinimas, :banko_saskaita, :kontaktinis_asmuo, :pastabos,
                    :logotipas, :vadovas_vardas_pavarde, :tinklalapis, :darbo_laikas)";

            $this->db->query($sql);



            $this->db->bind(':pavadinimas', $data['pavadinimas']);

            $this->db->bind(':imones_kodas', $data['imones_kodas']);

            $this->db->bind(':pvm_kodas', $data['pvm_kodas'] ?? null);

            $this->db->bind(':adresas_salis', $data['adresas_salis'] ?? null);

            $this->db->bind(':adresas_miestas', $data['adresas_miestas'] ?? null);

            $this->db->bind(':adresas_gatve', $data['adresas_gatve'] ?? null);

            $this->db->bind(':adresas_pasto_kodas', $data['adresas_pasto_kodas'] ?? null);

            $this->db->bind(':telefonas', $data['telefonas'] ?? null);

            $this->db->bind(':el_pastas', $data['el_pastas'] ?? null);

            $this->db->bind(':banko_pavadinimas', $data['banko_pavadinimas'] ?? null);

            $this->db->bind(':banko_saskaita', $data['banko_saskaita'] ?? null);

            $this->db->bind(':kontaktinis_asmuo', $data['kontaktinis_asmuo'] ?? null);

            $this->db->bind(':pastabos', $data['pastabos'] ?? null);



            // Nauji laukai

            $this->db->bind(':logotipas', $data['logotipas_filename'] ?? null); // Tikimės, kad failo vardas bus perduotas čia

            $this->db->bind(':vadovas_vardas_pavarde', $data['vadovas_vardas_pavarde'] ?? null);

            $this->db->bind(':tinklalapis', $data['tinklalapis'] ?? null);

            $this->db->bind(':darbo_laikas', $data['darbo_laikas'] ?? null);



            return $this->db->execute();

        } catch (PDOException $e) {

            error_log("Error creating company: " . $e->getMessage() . " Data: " . print_r($data, true));

            return false;

        }

    }



    /**

     * Atnaujina esamos įmonės duomenis.

     * @param int $id Įmonės ID.

     * @param array $data Duomenys iš formos. Papildomai tikisi 'logotipas_filename' jei logotipas buvo įkeltas/pakeistas.

     * @return bool True, jei sėkmingai, false - kitu atveju.

     */

    public function updateCompany(int $id, array $data): bool
    {

        if (empty($data['pavadinimas']) || empty($data['imones_kodas'])) {

            return false;

        }

        try {

            // Dinamiškai formuojame SET dalį, kad logotipas būtų atnaujintas tik jei pateiktas naujas

            $set_parts = [

                "pavadinimas = :pavadinimas",

                "imones_kodas = :imones_kodas",

                "pvm_kodas = :pvm_kodas",

                "adresas_salis = :adresas_salis",

                "adresas_miestas = :adresas_miestas",

                "adresas_gatve = :adresas_gatve",

                "adresas_pasto_kodas = :adresas_pasto_kodas",

                "telefonas = :telefonas",

                "el_pastas = :el_pastas",

                "banko_pavadinimas = :banko_pavadinimas",

                "banko_saskaita = :banko_saskaita",

                "kontaktinis_asmuo = :kontaktinis_asmuo",

                "pastabos = :pastabos",

                "vadovas_vardas_pavarde = :vadovas_vardas_pavarde",

                "tinklalapis = :tinklalapis",

                "darbo_laikas = :darbo_laikas"

            ];

            // Jei 'logotipas_filename' yra $data masyve (net jei null, pvz., trynimui), įtraukiame jį

            if (array_key_exists('logotipas_filename', $data)) {

                $set_parts[] = "logotipas = :logotipas";

            }



            $sql = "UPDATE " . $this->companiesTable . " SET " . implode(", ", $set_parts) . " WHERE id = :id";

            $this->db->query($sql);



            $this->db->bind(':id', $id);

            $this->db->bind(':pavadinimas', $data['pavadinimas']);

            $this->db->bind(':imones_kodas', $data['imones_kodas']);

            $this->db->bind(':pvm_kodas', $data['pvm_kodas'] ?? null);

            $this->db->bind(':adresas_salis', $data['adresas_salis'] ?? null);

            $this->db->bind(':adresas_miestas', $data['adresas_miestas'] ?? null);

            $this->db->bind(':adresas_gatve', $data['adresas_gatve'] ?? null);

            $this->db->bind(':adresas_pasto_kodas', $data['adresas_pasto_kodas'] ?? null);

            $this->db->bind(':telefonas', $data['telefonas'] ?? null);

            $this->db->bind(':el_pastas', $data['el_pastas'] ?? null);

            $this->db->bind(':banko_pavadinimas', $data['banko_pavadinimas'] ?? null);

            $this->db->bind(':banko_saskaita', $data['banko_saskaita'] ?? null);

            $this->db->bind(':kontaktinis_asmuo', $data['kontaktinis_asmuo'] ?? null);

            $this->db->bind(':pastabos', $data['pastabos'] ?? null);



            // Nauji laukai

            if (array_key_exists('logotipas_filename', $data)) {

                $this->db->bind(':logotipas', $data['logotipas_filename']); // Gali būti null, jei logotipas pašalintas

            }

            $this->db->bind(':vadovas_vardas_pavarde', $data['vadovas_vardas_pavarde'] ?? null);

            $this->db->bind(':tinklalapis', $data['tinklalapis'] ?? null);

            $this->db->bind(':darbo_laikas', $data['darbo_laikas'] ?? null);



            return $this->db->execute();

        } catch (PDOException $e) {

            error_log("Error updating company ID {$id}: " . $e->getMessage() . " Data: " . print_r($data, true));

            return false;

        }

    }



    public function deleteCompany(int $id): bool
    {

        // Prieš trinant įrašą iš DB, reikėtų ištrinti ir susijusį logotipo failą

        $company = $this->getCompanyById($id);

        if ($company && !empty($company['logotipas'])) {

            $logoPath = __DIR__ . '/../../public/uploads/logos/' . $company['logotipas'];

            if (file_exists($logoPath)) {

                unlink($logoPath); // Būtina pasirūpinti teisėmis

            }

        }



        try {

            $this->db->query("DELETE FROM " . $this->companiesTable . " WHERE id = :id");

            $this->db->bind(':id', $id);

            return $this->db->execute();

        } catch (PDOException $e) {

            error_log("Error deleting company ID {$id}: " . $e->getMessage());

            return false;

        }

    }

    public function searchSuggestions(string $query): array
    {
        $sql = "SELECT id, pavadinimas FROM " . $this->companiesTable . " WHERE pavadinimas LIKE :query LIMIT 10";
        try {
            $this->db->query($sql);
            $this->db->bind(':query', '%' . $query . '%');
            return $this->db->resultSet();
        } catch (PDOException $e) {
            // Log error, but return empty array to the client
            error_log("Error searching suggestions: " . $e->getMessage());
            return [];
        }
    }
}

?>
