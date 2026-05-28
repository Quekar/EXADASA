<?php

class Notification_model
{
    private $db;

    public function __construct()
    {
        $this->db = new Database;
    }

    public function getByUser(int $id_user): array
    {
        try {
            $safe_id = intval($id_user);
            $this->db->query(
                "SELECT * FROM notifications
                 WHERE id_user = $safe_id
                 ORDER BY created_at DESC
                 LIMIT 20"
            );
            return $this->db->resultSet() ?: [];
        } catch (PDOException $e) {
            return [];
        }
    }

    public function countUnread(int $id_user): int
    {
        try {
            $safe_id = intval($id_user);
            $this->db->query(
                "SELECT COUNT(*) as total FROM notifications
                 WHERE id_user = $safe_id AND is_read = 0"
            );
            return (int) ($this->db->single()['total'] ?? 0);
        } catch (PDOException $e) {
            return 0;
        }
    }

    public function markAllRead(int $id_user): void
    {
        try {
            $safe_id = intval($id_user);
            $this->db->query(
                "UPDATE notifications
                 SET is_read = 1, read_at = NOW()
                 WHERE id_user = $safe_id AND is_read = 0"
            );
            $this->db->execute();
        } catch (PDOException $e) {

        }
    }

    public function markOneRead(int $id_notifikasi): void
    {
        try {
            $safe_id = intval($id_notifikasi);
            $this->db->query(
                "UPDATE notifications
                 SET is_read = 1, read_at = NOW()
                 WHERE id_notifikasi = $safe_id"
            );
            $this->db->execute();
        } catch (PDOException $e) {
            
        }
    }
}