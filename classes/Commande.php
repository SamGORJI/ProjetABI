<?php
/**
 * Classe Commande - Gestion des commandes
 */

class Commande {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * Créer une nouvelle commande avec détails
     * @param array $data
     * @param array $details
     * @return int|false
     */
    public function create($data, $details = []) {
        try {
            $this->db->beginTransaction();
            
            // Créer la commande
            $stmt = $this->db->prepare("
                INSERT INTO commandes (client_id, utilisateur_id, statut, notes) 
                VALUES (?, ?, ?, ?)
            ");
            
            $stmt->execute([
                $data['client_id'],
                $data['utilisateur_id'],
                $data['statut'] ?? 'En attente',
                $data['notes'] ?? null
            ]);
            
            $commandeId = $this->db->lastInsertId();
            
            // Ajouter les détails de la commande
            $montantTotal = 0;
            if (!empty($details)) {
                $stmtDetail = $this->db->prepare("
                    INSERT INTO commande_details (commande_id, produit_id, quantite, prix_unitaire) 
                    VALUES (?, ?, ?, ?)
                ");
                
                foreach ($details as $detail) {
                    $stmtDetail->execute([
                        $commandeId,
                        $detail['produit_id'],
                        $detail['quantite'],
                        $detail['prix_unitaire']
                    ]);
                    $montantTotal += $detail['quantite'] * $detail['prix_unitaire'];
                }
            }
            
            // Mettre à jour le montant total
            $stmtUpdate = $this->db->prepare("UPDATE commandes SET montant_total = ? WHERE id = ?");
            $stmtUpdate->execute([$montantTotal, $commandeId]);
            
            $this->db->commit();
            return $commandeId;
            
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }
    
    /**
     * Obtenir toutes les commandes avec informations client
     * @return array
     */
    public function getAll() {
        $stmt = $this->db->query("
            SELECT c.*, 
                   cl.nom as client_nom, 
                   cl.prenom as client_prenom, 
                   cl.entreprise as client_entreprise,
                   u.nom as utilisateur_nom,
                   u.prenom as utilisateur_prenom
            FROM commandes c
            LEFT JOIN clients cl ON c.client_id = cl.id
            LEFT JOIN utilisateurs u ON c.utilisateur_id = u.id
            ORDER BY c.date_commande DESC
        ");
        return $stmt->fetchAll();
    }
    
    /**
     * Obtenir une commande par ID avec détails
     * @param int $id
     * @return array|false
     */
    public function getById($id) {
        $stmt = $this->db->prepare("
            SELECT c.*, 
                   cl.nom as client_nom, 
                   cl.prenom as client_prenom, 
                   cl.entreprise as client_entreprise
            FROM commandes c
            LEFT JOIN clients cl ON c.client_id = cl.id
            WHERE c.id = ?
        ");
        $stmt->execute([$id]);
        $commande = $stmt->fetch();
        
        if ($commande) {
            // Récupérer les détails
            $stmtDetails = $this->db->prepare("
                SELECT cd.*, p.nom as produit_nom
                FROM commande_details cd
                LEFT JOIN produits p ON cd.produit_id = p.id
                WHERE cd.commande_id = ?
            ");
            $stmtDetails->execute([$id]);
            $commande['details'] = $stmtDetails->fetchAll();
        }
        
        return $commande;
    }
    
    /**
     * Mettre à jour le statut d'une commande
     * @param int $id
     * @param string $statut
     * @return bool
     */
    public function updateStatut($id, $statut) {
        $stmt = $this->db->prepare("UPDATE commandes SET statut = ? WHERE id = ?");
        return $stmt->execute([$statut, $id]);
    }
    
    /**
     * Supprimer une commande
     * @param int $id
     * @return bool
     */
    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM commandes WHERE id = ?");
        return $stmt->execute([$id]);
    }
    
    /**
     * Obtenir les statistiques des commandes
     * @return array
     */
    public function getStatistiques() {
        $stmt = $this->db->query("
            SELECT 
                COUNT(*) as total_commandes,
                SUM(montant_total) as montant_total,
                AVG(montant_total) as montant_moyen,
                SUM(CASE WHEN statut = 'En attente' THEN 1 ELSE 0 END) as en_attente,
                SUM(CASE WHEN statut = 'En cours' THEN 1 ELSE 0 END) as en_cours,
                SUM(CASE WHEN statut = 'Livree' THEN 1 ELSE 0 END) as livrees
            FROM commandes
        ");
        return $stmt->fetch();
    }
}
