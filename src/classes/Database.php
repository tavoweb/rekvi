<?php
// src/classes/Database.php

class Database {
    private string $host = DB_HOST;
    private string $user = DB_USER;
    private string $pass = DB_PASS;
    private string $dbname = DB_NAME;

    private PDO $dbh; // Database Handler
    private PDOStatement $stmt; // Statement
    private ?string $error = null;

    public function __construct() {
        $dsn = 'mysql:host=' . $this->host . ';dbname=' . $this->dbname . ';charset=utf8mb4';
        $options = [
            PDO::ATTR_PERSISTENT => true,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // Svarbu klaidų gaudymui
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];

        try {
            $this->dbh = new PDO($dsn, $this->user, $this->pass, $options);
        } catch (PDOException $e) {
            $this->error = $e->getMessage();
            // Realiame projekte čia reikėtų detalesnio klaidų log'inimo, o ne die()
            error_log("Database Connection Error: " . $this->error);
            die("Nepavyko prisijungti prie duomenų bazės. Prašome bandyti vėliau.");
        }
    }

    /**
     * Paruošia SQL užklausą vykdymui.
     * @param string $sql SQL užklausa.
     */
    public function query(string $sql): void {
        try {
            $this->stmt = $this->dbh->prepare($sql);
        } catch (PDOException $e) {
            $this->error = $e->getMessage();
            error_log("Database Query Preparation Error: " . $this->error . " | SQL: " . $sql);
            // Galima mesti išimtį toliau arba grąžinti false/null priklausomai nuo logikos
            throw $e; // Mesti išimtį, kad būtų galima pagauti aukštesniame lygyje
        }
    }

    /**
     * Susieja reikšmę su SQL užklausos parametru.
     * @param string|int $param Parametro pavadinimas (pvz., :name) arba pozicija (?).
     * @param mixed $value Reikšmė, kurią reikia susieti.
     * @param int|null $type PDO::PARAM_* konstanta (pasirinktinai).
     */
    public function bind($param, $value, ?int $type = null): void {
        if (is_null($type)) {
            switch (true) {
                case is_int($value):
                    $type = PDO::PARAM_INT;
                    break;
                case is_bool($value):
                    $type = PDO::PARAM_BOOL;
                    break;
                case is_null($value):
                    $type = PDO::PARAM_NULL;
                    break;
                default:
                    $type = PDO::PARAM_STR;
            }
        }
        $this->stmt->bindValue($param, $value, $type);
    }

    /**
     * Įvykdo paruoštą SQL užklausą.
     * @return bool True, jei sėkmingai, false - kitu atveju.
     */
    public function execute(): bool {
        try {
            return $this->stmt->execute();
        } catch (PDOException $e) { // Sugauta PDO išimtis vykdymo metu
            $this->error = $e->getMessage();
            // Pridedame detalesnį klaidų registravimą
            ob_start(); // Pradedame išvesties buferizavimą
            $this->stmt->debugDumpParams(); // Išvedame informaciją apie paruoštą užklausą ir susietus parametrus
            $dump = ob_get_clean(); // Gauname buferio turinį ir išvalome jį
            error_log("Database Execution Error: " . $this->error . "\nSQL Query (prepared): " . $this->stmt->queryString . "\nDebug Dump Params:\n" . $dump);
            // Svarstyti, ar mesti išimtį, ar grąžinti false
            // throw $e; // Jei norite, kad klaida būtų apdorojama aukščiau
            return false; // Arba tiesiog grąžinti false
        }
    }

    /**
     * Gauna visus rezultatus kaip asociatyvų masyvą.
     * @return array Rezultatų masyvas.
     */
    public function resultSet(): array {
        if ($this->execute()) { // Patikriname, ar įvykdymas buvo sėkmingas
            return $this->stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        return []; // Grąžinti tuščią masyvą, jei vykdymas nepavyko
    }

    /**
     * Gauna vieną įrašą kaip asociatyvų masyvą.
     * @return mixed Vienas įrašas arba false, jei nerasta.
     */
    public function single() {
        if ($this->execute()) { // Patikriname, ar įvykdymas buvo sėkmingas
            return $this->stmt->fetch(PDO::FETCH_ASSOC);
        }
        return false; // Grąžinti false, jei vykdymas nepavyko
    }

    /**
     * Gauna paveiktų eilučių skaičių (po INSERT, UPDATE, DELETE).
     * @return int Paveiktų eilučių skaičius.
     */
    public function rowCount(): int {
        return $this->stmt->rowCount();
    }

    /**
     * Gauna paskutinio įterpto įrašo ID.
     * @return string|false Paskutinio ID reikšmė arba false, jei nepavyko.
     */
    public function lastInsertId() {
        return $this->dbh->lastInsertId();
    }

    /**
     * Grąžina paskutinę įvykusią klaidą.
     * @return string|null Klaidos pranešimas.
     */
    public function getError(): ?string {
        return $this->error;
    }
}
?>