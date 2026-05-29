<?php
// src/PermissionHelper.php
// Full RBAC implementation — role_name keyed to system_roles table

class PermissionHelper {
    private $pdo;
    private $user = null;
    private $permissions = [];
    private $role_name = '';

    public function __construct($pdo = null) {
        $this->pdo = $pdo ?: Database::connect();
        $this->loadUser();
    }

    private function loadUser() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (empty($_SESSION['user_id'])) {
            $this->user = ['id' => null, 'name' => 'Guest', 'role' => 'Guest'];
            $this->role_name = 'Guest';
            $this->permissions = [];
            return;
        }

        $this->user = [
            'id'   => $_SESSION['user_id'],
            'name' => $_SESSION['user_name'] ?? 'User',
            'role' => $_SESSION['role'] ?? 'Viewer',
        ];
        $this->role_name = $_SESSION['role'] ?? 'Viewer';
        $this->loadRolePermissions();
    }

    private function loadRolePermissions() {
        // Look up permissions from system_roles table
        $stmt = $this->pdo->prepare("SELECT permissions FROM system_roles WHERE role_name = ?");
        $stmt->execute([$this->role_name]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $perms = json_decode($row['permissions'], true);
            $this->permissions = is_array($perms) ? $perms : [];
        } else {
            // Fallback: Admin gets all, others get minimal
            if ($this->role_name === 'Admin') {
                $this->permissions = ['admin'];
            } else {
                $this->permissions = ['view_dashboard'];
            }
        }
    }

    public function getUser() {
        return $this->user;
    }

    public function getRole() {
        return $this->role_name;
    }

    public function getPermissions() {
        return $this->permissions;
    }

    public function hasPermission($permission) {
        // Admin role has unrestricted access
        if ($this->role_name === 'Admin') {
            return true;
        }
        // "admin" permission flag means full access
        if (in_array('admin', $this->permissions, true)) {
            return true;
        }
        return in_array($permission, $this->permissions, true);
    }

    public function requirePermission($permission) {
        if (!$this->hasPermission($permission)) {
            http_response_code(403);
            if (php_sapi_name() === 'cli' || headers_sent()) {
                echo json_encode(['success' => false, 'error' => 'Permission denied: ' . $permission]);
            } else {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'error' => 'Permission denied: ' . $permission]);
            }
            exit;
        }
    }

    // Static version — for use in template files that don't have DB connection
    public static function getCurrentUser() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (empty($_SESSION['user_id'])) {
            return ['id' => null, 'name' => 'Guest', 'role' => 'Guest'];
        }
        return [
            'id'   => $_SESSION['user_id'],
            'name' => $_SESSION['user_name'] ?? 'User',
            'role' => $_SESSION['role'] ?? 'Viewer',
        ];
    }

    // Check if current session user has a specific permission (static)
    public static function hasPermissionFor($permission, $role = null) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $role = $role ?: ($_SESSION['role'] ?? 'Guest');
        if ($role === 'Admin') {
            return true;
        }
        $pdo = Database::connect();
        $stmt = $pdo->prepare("SELECT permissions FROM system_roles WHERE role_name = ?");
        $stmt->execute([$role]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$row) {
            return false;
        }
        $perms = json_decode($row['permissions'], true) ?: [];
        return in_array('admin', $perms, true) || in_array($permission, $perms, true);
    }
}