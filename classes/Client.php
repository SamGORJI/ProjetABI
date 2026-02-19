<?php
/**
 * Classe Client - Gestion des clients
 */

class Client {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * Créer un nouveau client
     * @param array $data
     * @return int|false
     */
    public function create($data) {
        $stmt = $this->db->prepare("
            INSERT INTO clients (nom, prenom, entreprise, email, telephone, adresse, ville, code_postal, pays) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        
        if ($stmt->execute([
            $data['nom'],
            $data['prenom'],
            $data['entreprise'] ?? null,
            $data['email'],
            $data['telephone'] ?? null,
            $data['adresse'] ?? null,
            $data['ville'] ?? null,
            $data['code_postal'] ?? null,
            $data['pays'] ?? 'France'
        ])) {
            return $this->db->lastInsertId();
        }
        
        return false;
    }
    
    /**
     * Obtenir tous les clients
     * @return array
     */
    public function getAll() {
        $stmt = $this->db->query("SELECT * FROM clients ORDER BY nom, prenom");
        return $stmt->fetchAll();
    }
    
    /**
     * Obtenir un client par ID
     * @param int $id
     * @return array|false
     */
    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM clients WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    /**
     * Mettre à jour un client
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update($id, $data) {
        $stmt = $this->db->prepare("
            UPDATE clients 
            SET nom = ?, prenom = ?, entreprise = ?, email = ?, telephone = ?, 
                adresse = ?, ville = ?, code_postal = ?, pays = ?
            WHERE id = ?
        ");
        
        return $stmt->execute([
            $data['nom'],
            $data['prenom'],
            $data['entreprise'] ?? null,
            $data['email'],
            $data['telephone'] ?? null,
            $data['adresse'] ?? null,
            $data['ville'] ?? null,
            $data['code_postal'] ?? null,
            $data['pays'] ?? 'France',
            $id
        ]);
    }
    
    /**
     * Supprimer un client
     * @param int $id
     * @return bool
     */
    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM clients WHERE id = ?");
        return $stmt->execute([$id]);
    }
    
    /**
     * Rechercher des clients
     * @param string $search
     * @return array
     */
    public function search($search) {
        $stmt = $this->db->prepare("
            SELECT * FROM clients 
            WHERE nom LIKE ? OR prenom LIKE ? OR entreprise LIKE ? OR email LIKE ?
            ORDER BY nom, prenom
        ");
        $searchTerm = "%{$search}%";
        $stmt->execute([$searchTerm, $searchTerm, $searchTerm, $searchTerm]);
        return $stmt->fetchAll();
    }
}
