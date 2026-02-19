<?php
/**
 * Classe Produit - Gestion des produits
 */

class Produit {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * Créer un nouveau produit
     * @param array $data
     * @return int|false
     */
    public function create($data) {
        $stmt = $this->db->prepare("
            INSERT INTO produits (nom, description, prix, categorie, stock) 
            VALUES (?, ?, ?, ?, ?)
        ");
        
        if ($stmt->execute([
            $data['nom'],
            $data['description'] ?? null,
            $data['prix'],
            $data['categorie'] ?? null,
            $data['stock'] ?? 0
        ])) {
            return $this->db->lastInsertId();
        }
        
        return false;
    }
    
    /**
     * Obtenir tous les produits
     * @return array
     */
    public function getAll() {
        $stmt = $this->db->query("SELECT * FROM produits ORDER BY nom");
        return $stmt->fetchAll();
    }
    
    /**
     * Obtenir un produit par ID
     * @param int $id
     * @return array|false
     */
    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM produits WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    /**
     * Mettre à jour un produit
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update($id, $data) {
        $stmt = $this->db->prepare("
            UPDATE produits 
            SET nom = ?, description = ?, prix = ?, categorie = ?, stock = ?
            WHERE id = ?
        ");
        
        return $stmt->execute([
            $data['nom'],
            $data['description'] ?? null,
            $data['prix'],
            $data['categorie'] ?? null,
            $data['stock'] ?? 0,
            $id
        ]);
    }
    
    /**
     * Supprimer un produit
     * @param int $id
     * @return bool
     */
    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM produits WHERE id = ?");
        return $stmt->execute([$id]);
    }
    
    /**
     * Obtenir les produits par catégorie
     * @param string $categorie
     * @return array
     */
    public function getByCategorie($categorie) {
        $stmt = $this->db->prepare("SELECT * FROM produits WHERE categorie = ? ORDER BY nom");
        $stmt->execute([$categorie]);
        return $stmt->fetchAll();
    }
    
    /**
     * Obtenir toutes les catégories
     * @return array
     */
    public function getCategories() {
        $stmt = $this->db->query("SELECT DISTINCT categorie FROM produits WHERE categorie IS NOT NULL ORDER BY categorie");
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
}
