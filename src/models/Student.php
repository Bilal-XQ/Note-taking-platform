<?php
class Student {
    private $db;
    public function __construct() {
        $cfg = require __DIR__ . '/../../config/database.php';
        $this->db = new mysqli($cfg['host'], $cfg['user'], $cfg['pass'], $cfg['dbname']);
    }
    public function all() {
        $result = $this->db->query('SELECT * FROM students');
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }
    public function get($id) {
        $stmt = $this->db->prepare('SELECT * FROM students WHERE id = ?');
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $res = $stmt->get_result();
        return $res->fetch_assoc();
    }
    public function add($data) {
        $stmt = $this->db->prepare('INSERT INTO students (full_name, username, password, admin_id) VALUES (?, ?, ?, ?)');
        $stmt->bind_param('sssi', $data['full_name'], $data['username'], $data['password'], $_SESSION['admin_id']);
        $stmt->execute();
    }
    public function edit($id, $data) {
        $stmt = $this->db->prepare('UPDATE students SET full_name=?, username=?, password=? WHERE id=?');
        $stmt->bind_param('sssi', $data['full_name'], $data['username'], $data['password'], $id);
        $stmt->execute();
    }
    public function delete($id) {
        $stmt = $this->db->prepare('DELETE FROM students WHERE id=?');
        $stmt->bind_param('i', $id);
        $stmt->execute();
    }
} 