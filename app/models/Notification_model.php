<?php

class Notification_model
{
    private $db;

    public function __construct()
    {
        $this->db = new Database;
    }

    public function getByUser(string $id_user): array
    {
        try {
            $this->db->query(
                "SELECT * FROM notifications
                 WHERE id_user = :id_user
                 ORDER BY created_at DESC
                 LIMIT 20"
            );
            $this->db->bind('id_user', $id_user);
            return $this->db->resultSet();
        } catch (PDOException $e) {
            return [];
        }
    }

    public function countUnread(string $id_user): int
    {
        try {
            $this->db->query(
                "SELECT COUNT(*) as total FROM notifications
                 WHERE id_user = :id_user AND is_read = 0"
            );
            $this->db->bind("id_user", $id_user);
            return $this->db->single()['total'] ?? 0;
        } catch (PDOException $e) {
            return 0;
        }
    }

    public function markAllRead(string $id_user): void
    {
        try {
            $this->db->query(
                "UPDATE notifications
                 SET is_read = 1, read_at = NOW()
                 WHERE id_user = :id_user AND is_read = 0"
            );
            $this->db->bind('id_user', $id_user);
            $this->db->execute();
        } catch (PDOException $e) {
        }
    }

    public function markOneRead(int $id_notifikasi): void
    {
        try {
            $this->db->query(
                "UPDATE notifications
                 SET is_read = 1, read_at = NOW()
                 WHERE id_notifikasi = :id_notifikasi"
            );
            $this->db->bind('id_notifikasi', $id_notifikasi);
            $this->db->execute();
        } catch (PDOException $e) {
            
        }
    }
}