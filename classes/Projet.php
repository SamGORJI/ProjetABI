<?php
/**
 * Classe Projet - Gestion des projets
 */

class Projet {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * Créer un nouveau projet
     * @param array $data
     * @return int|false
     */
    public function create($data) {
        $stmt = $this->db->prepare("
            INSERT INTO projets (nom, description, client_id, responsable_id, date_debut, date_fin, statut, budget) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        
        if ($stmt->execute([
            $data['nom'],
            $data['description'] ?? null,
            $data['client_id'] ?? null,
            $data['responsable_id'],
            $data['date_debut'] ?? null,
            $data['date_fin'] ?? null,
            $data['statut'] ?? 'Planifie',
            $data['budget'] ?? null
        ])) {
            return $this->db->lastInsertId();
        }
        
        return false;
    }
    
    /**
     * Obtenir tous les projets
     * @return array
     */
    public function getAll() {
        $stmt = $this->db->query("
            SELECT p.*, 
                   c.nom as client_nom, 
                   c.prenom as client_prenom,
                   c.entreprise as client_entreprise,
                   u.nom as responsable_nom,
                   u.prenom as responsable_prenom
            FROM projets p
            LEFT JOIN clients c ON p.client_id = c.id
            LEFT JOIN utilisateurs u ON p.responsable_id = u.id
            ORDER BY p.date_creation DESC
        ");
        return $stmt->fetchAll();
    }
    
    /**
     * Obtenir un projet par ID avec équipe
     * @param int $id
     * @return array|false
     */
    public function getById($id) {
        $stmt = $this->db->prepare("
            SELECT p.*, 
                   c.nom as client_nom, 
                   c.prenom as client_prenom,
                   c.entreprise as client_entreprise
            FROM projets p
            LEFT JOIN clients c ON p.client_id = c.id
            WHERE p.id = ?
        ");
        $stmt->execute([$id]);
        $projet = $stmt->fetch();
        
        if ($projet) {
            // Récupérer l'équipe
            $stmtEquipe = $this->db->prepare("
                SELECT e.*, u.nom, u.prenom, u.email
                FROM equipes e
                LEFT JOIN utilisateurs u ON e.utilisateur_id = u.id
                WHERE e.projet_id = ?
            ");
            $stmtEquipe->execute([$id]);
            $projet['equipe'] = $stmtEquipe->fetchAll();
        }
        
        return $projet;
    }
    
    /**
     * Mettre à jour un projet
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update($id, $data) {
        $stmt = $this->db->prepare("
            UPDATE projets 
            SET nom = ?, description = ?, client_id = ?, responsable_id = ?, 
                date_debut = ?, date_fin = ?, statut = ?, budget = ?
            WHERE id = ?
        ");
        
        return $stmt->execute([
            $data['nom'],
            $data['description'] ?? null,
            $data['client_id'] ?? null,
            $data['responsable_id'],
            $data['date_debut'] ?? null,
            $data['date_fin'] ?? null,
            $data['statut'] ?? 'Planifie',
            $data['budget'] ?? null,
            $id
        ]);
    }
    
    /**
     * Supprimer un projet
     * @param int $id
     * @return bool
     */
    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM projets WHERE id = ?");
        return $stmt->execute([$id]);
    }
    
    /**
     * Ajouter un membre à l'équipe
     * @param int $projetId
     * @param int $utilisateurId
     * @param string $role
     * @return bool
     */
    public function ajouterMembre($projetId, $utilisateurId, $role = 'Developpeur') {
        $stmt = $this->db->prepare("
            INSERT INTO equipes (projet_id, utilisateur_id, role_projet) 
            VALUES (?, ?, ?)
        ");
        return $stmt->execute([$projetId, $utilisateurId, $role]);
    }
    
    /**
     * Retirer un membre de l'équipe
     * @param int $projetId
     * @param int $utilisateurId
     * @return bool
     */
    public function retirerMembre($projetId, $utilisateurId) {
        $stmt = $this->db->prepare("DELETE FROM equipes WHERE projet_id = ? AND utilisateur_id = ?");
        return $stmt->execute([$projetId, $utilisateurId]);
    }
}
