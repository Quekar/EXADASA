<?php

class Notification extends Controller
{
    public function getNotifications()
    {
        header('Content-Type: application/json');

        if (!isset($_SESSION['user'])) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }

        $id_user = (int) ($_SESSION['user']['id'] ?? 0);

        if ($id_user === 0) {
            echo json_encode(['success' => false, 'message' => 'id_user tidak ditemukan di session']);
            exit;
        }

        $model  = $this->model('Notification_model');
        $notifs = $model->getByUser($id_user);
        $unread = $model->countUnread($id_user);

        echo json_encode([
            'success' => true,
            'unread'  => $unread,
            'notifs'  => $notifs,
        ]);
        exit;
    }

    public function markAllRead()
    {
        header('Content-Type: application/json');

        if (!isset($_SESSION['user'])) {
            echo json_encode(['success' => false]);
            exit;
        }

        $id_user = (int) ($_SESSION['user']['id'] ?? 0);
        if ($id_user > 0) {
            $this->model('Notification_model')->markAllRead($id_user);
        }

        echo json_encode(['success' => true]);
        exit;
    }

    public function markRead()
    {
        header('Content-Type: application/json');

        if (!isset($_SESSION['user'])) {
            echo json_encode(['success' => false]);
            exit;
        }

        $body = json_decode(file_get_contents('php://input'), true);
        $id   = (int) ($body['id_notifikasi'] ?? 0);

        if ($id > 0) {
            $this->model('Notification_model')->markOneRead($id);
        }

        echo json_encode(['success' => true]);
        exit;
    }

    public function unreadCount()
    {
        header('Content-Type: application/json');

        if (!isset($_SESSION['user'])) {
            echo json_encode(['count' => 0]);
            exit;
        }

        $id_user = (int) ($_SESSION['user']['id'] ?? 0);
        $count   = $id_user > 0
            ? $this->model('Notification_model')->countUnread($id_user)
            : 0;

        echo json_encode(['count' => $count]);
        exit;
    }
}