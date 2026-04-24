<?php
/**
 * Dynamic Menu System
 * Generates menu items based on user roles and permissions
 * Self-referencing: Highlights current page
 */

class Menu {
    private static $menuItems = [
        // Public menu items (visible to everyone)
        'public' => [
            [
                'title' => 'Home',
                'url' => 'index.php',
                'icon' => 'fa-home',
                'roles' => ['guest', 'student', 'admin']
            ],
            [
                'title' => 'Login',
                'url' => 'auth/login.php',
                'icon' => 'fa-sign-in-alt',
                'roles' => ['guest']
            ],
            [
                'title' => 'Register',
                'url' => 'auth/register.php',
                'icon' => 'fa-user-plus',
                'roles' => ['guest']
            ]
        ],
        
        // Student menu items
        'student' => [
            [
                'title' => 'Dashboard',
                'url' => 'student/dashboard.php',
                'icon' => 'fa-home',
                'roles' => ['student', 'admin']
            ],
            [
                'title' => 'Profile',
                'url' => 'student/profile.php',
                'icon' => 'fa-user',
                'roles' => ['student', 'admin']
            ]
        ],
        
        // Admin menu items
        'admin' => [
            [
                'title' => 'Dashboard',
                'url' => 'admin/dashboard.php',
                'icon' => 'fa-tachometer-alt',
                'roles' => ['admin']
            ],
            [
                'title' => 'Courses',
                'url' => 'admin/courses.php',
                'icon' => 'fa-book',
                'roles' => ['admin']
            ]
        ],
        
        // User dropdown menu items
        'user_menu' => [
            [
                'title' => 'Profile',
                'url' => 'student/profile.php',
                'icon' => 'fa-user',
                'roles' => ['student']
            ],
            [
                'title' => 'View Student Dashboard',
                'url' => 'student/dashboard.php',
                'icon' => 'fa-eye',
                'roles' => ['admin']
            ],
            [
                'title' => 'Logout',
                'url' => 'auth/logout.php',
                'icon' => 'fa-sign-out-alt',
                'roles' => ['student', 'admin']
            ]
        ]
    ];
    
    /**
     * Get current user role
     */
    private static function getUserRole() {
        if (!isLoggedIn()) {
            return 'guest';
        }
        return isAdmin() ? 'admin' : 'student';
    }
    
    /**
     * Check if user has permission for menu item
     */
    private static function hasPermission($item, $userRole) {
        return in_array($userRole, $item['roles']);
    }
    
    /**
     * Get current page URL (relative to root)
     */
    private static function getCurrentPage() {
        $scriptPath = $_SERVER['SCRIPT_NAME'];
        
        // Get the full path from document root
        $documentRoot = $_SERVER['DOCUMENT_ROOT'];
        $scriptPathFull = str_replace($documentRoot, '', $scriptPath);
        $scriptPathFull = str_replace('\\', '/', $scriptPathFull);
        $scriptPathFull = ltrim($scriptPathFull, '/');
        
        // Extract just the filename and directory
        $pathParts = explode('/', $scriptPathFull);
        
        // If we're in a subdirectory (admin, student, auth), include it
        if (count($pathParts) > 1) {
            $dir = $pathParts[count($pathParts) - 2];
            $file = $pathParts[count($pathParts) - 1];
            
            if (in_array($dir, ['admin', 'student', 'auth'])) {
                return $dir . '/' . $file;
            }
        }
        
        // For root files
        return basename($scriptPathFull);
    }
    
    /**
     * Check if menu item is active (current page)
     */
    private static function isActive($itemUrl, $currentPage) {
        // Normalize URLs for comparison - remove any path prefixes
        $itemUrl = str_replace(['../', './'], '', $itemUrl);
        $itemUrl = trim($itemUrl, '/');
        $currentPage = str_replace(['../', './'], '', $currentPage);
        $currentPage = trim($currentPage, '/');
        
        // Direct match
        if ($itemUrl === $currentPage) {
            return true;
        }
        
        // Extract just the filename for comparison
        $itemFile = basename($itemUrl);
        $currentFile = basename($currentPage);
        
        if ($itemFile === $currentFile) {
            return true;
        }
        
        // Check if current page directory matches item URL directory
        $itemDir = dirname($itemUrl);
        $currentDir = dirname($currentPage);
        
        if ($itemDir !== '.' && $currentDir !== '.' && $itemDir === $currentDir) {
            // Same directory, check if it's a related page
            if (strpos($currentFile, $itemFile) !== false || strpos($itemFile, $currentFile) !== false) {
                return true;
            }
        }
        
        // Special cases
        if ($itemUrl === 'index.php' && ($currentPage === 'index.php' || $currentPage === '' || basename($currentPage) === 'index.php')) {
            return true;
        }
        
        return false;
    }
    
    /**
     * Get menu items for current user
     */
    public static function getMenuItems($menuType = 'main') {
        $userRole = self::getUserRole();
        $currentPage = self::getCurrentPage();
        $items = [];
        
        // Get appropriate menu items based on type
        $menuSource = [];
        if ($menuType === 'main') {
            $menuSource = array_merge(
                self::$menuItems['public'] ?? [],
                ($userRole === 'admin' ? self::$menuItems['admin'] : self::$menuItems['student']) ?? []
            );
        } elseif ($menuType === 'user') {
            $menuSource = self::$menuItems['user_menu'] ?? [];
        }
        
        // Filter items based on permissions
        foreach ($menuSource as $item) {
            if (self::hasPermission($item, $userRole)) {
                $item['active'] = self::isActive($item['url'], $currentPage);
                $items[] = $item;
            }
        }
        
        return $items;
    }
    
    /**
     * Render main navigation menu
     */
    public static function renderMainMenu() {
        $items = self::getMenuItems('main');
        $output = '';
        
        if (empty($items)) {
            return $output;
        }
        
        foreach ($items as $item) {
            $activeClass = $item['active'] ? 'active' : '';
            $url = getPath($item['url']);
            
            $output .= '<li class="nav-item">';
            $output .= '<a class="nav-link ' . $activeClass . '" href="' . htmlspecialchars($url) . '"';
            if ($item['active']) {
                $output .= ' aria-current="page"';
            }
            $output .= ' data-menu-item="' . htmlspecialchars($item['url']) . '"';
            $output .= '>';
            $output .= '<i class="fas ' . htmlspecialchars($item['icon']) . ' me-1"></i>' . htmlspecialchars($item['title']);
            if ($item['active']) {
                $output .= ' <span class="badge bg-light text-primary ms-1" style="font-size: 0.6rem;">●</span>';
            }
            $output .= '</a>';
            $output .= '</li>';
        }
        
        return $output;
    }
    
    /**
     * Render user dropdown menu
     */
    public static function renderUserMenu() {
        if (!isLoggedIn()) {
            return '';
        }
        
        $items = self::getMenuItems('user');
        $output = '';
        
        $output .= '<li class="nav-item dropdown">';
        $output .= '<a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">';
        $output .= '<i class="fas fa-user-circle me-1"></i>' . htmlspecialchars($_SESSION['user_name'] ?? 'User');
        $output .= '</a>';
        $output .= '<ul class="dropdown-menu">';
        
        foreach ($items as $item) {
            $url = getPath($item['url']);
            $output .= '<li><a class="dropdown-item" href="' . htmlspecialchars($url) . '">';
            $output .= '<i class="fas ' . htmlspecialchars($item['icon']) . ' me-1"></i>' . htmlspecialchars($item['title']);
            $output .= '</a></li>';
        }
        
        $output .= '</ul>';
        $output .= '</li>';
        
        return $output;
    }
    
    /**
     * Get home URL based on user role
     */
    public static function getHomeUrl() {
        if (!isLoggedIn()) {
            return getPath('index.php');
        }
        return getPath(isAdmin() ? 'admin/dashboard.php' : 'student/dashboard.php');
    }
    
    /**
     * Add custom menu item programmatically
     * Useful for plugins or dynamic menu additions
     */
    public static function addMenuItem($menuType, $item) {
        if (!isset(self::$menuItems[$menuType])) {
            self::$menuItems[$menuType] = [];
        }
        self::$menuItems[$menuType][] = $item;
    }
    
    /**
     * Get all available menu items (for debugging or admin purposes)
     */
    public static function getAllMenuItems() {
        return self::$menuItems;
    }
}

