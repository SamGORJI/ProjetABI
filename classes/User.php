<?php
/**
 * Classe User - Gestion des utilisateurs
 */

class User {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * Authentifier un utilisateur
     * @param string $email
     * @param string $password
     * @return array|false
     */
    public function login($email, $password) {
        $stmt = $this->db->prepare("SELECT * FROM utilisateurs WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['mot_de_passe'])) {
            // Ne pas retourner le mot de passe
            unset($user['mot_de_passe']);
            return $user;
        }
        
        return false;
    }
    
    /**
     * Créer un nouvel utilisateur
     * @param array $data
     * @return int|false
     */
    public function create($data) {
        $stmt = $this->db->prepare("
            INSERT INTO utilisateurs (nom, prenom, email, mot_de_passe, role) 
            VALUES (?, ?, ?, ?, ?)
        ");
        
        $hashedPassword = password_hash($data['mot_de_passe'], PASSWORD_DEFAULT);
        
        if ($stmt->execute([
            $data['nom'],
            $data['prenom'],
            $data['email'],
            $hashedPassword,
            $data['role'] ?? 'Commercial'
        ])) {
            return $this->db->lastInsertId();
        }
        
        return false;
    }
    
    /**
     * Obtenir tous les utilisateurs
     * @return array
     */
    public function getAll() {
        $stmt = $this->db->query("SELECT id, nom, prenom, email, role, date_creation FROM utilisateurs ORDER BY nom");
        return $stmt->fetchAll();
    }
    
    /**
     * Obtenir un utilisateur par ID
     * @param int $id
     * @return array|false
     */
    public function getById($id) {
        $stmt = $this->db->prepare("SELECT id, nom, prenom, email, role, date_creation FROM utilisateurs WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    /**
     * Mettre à jour un utilisateur
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update($id, $data) {
        $sql = "UPDATE utilisateurs SET nom = ?, prenom = ?, email = ?, role = ?";
        $params = [$data['nom'], $data['prenom'], $data['email'], $data['role']];
        
        // Si un nouveau mot de passe est fourni
        if (!empty($data['mot_de_passe'])) {
            $sql .= ", mot_de_passe = ?";
            $params[] = password_hash($data['mot_de_passe'], PASSWORD_DEFAULT);
        }
        
        $sql .= " WHERE id = ?";
        $params[] = $id;
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }
    
    /**
     * Supprimer un utilisateur
     * @param int $id
     * @return bool
     */
    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM utilisateurs WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
