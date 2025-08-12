<?php
/**
 * Base Controller Class
 */

class Controller
{
    protected $db;
    protected $security;
    protected $router;
    protected $data = [];

    public function __construct()
    {
        $this->db = Database::getInstance();
        $this->security = new Security();
        $this->router = new Router();
    }

    protected function view($viewPath, $data = [])
    {
        // Merge controller data with passed data
        $data = array_merge($this->data, $data);
        
        // Extract variables for use in view
        extract($data);
        
        // Start output buffering
        ob_start();
        
        // Include the view file
        $viewFile = $this->findViewFile($viewPath);
        if ($viewFile && file_exists($viewFile)) {
            include $viewFile;
        } else {
            throw new Exception("View not found: $viewPath");
        }
        
        // Get the content
        $content = ob_get_clean();
        
        // If this is an AJAX request, return content directly
        if ($this->isAjaxRequest()) {
            echo $content;
            return;
        }
        
        // Otherwise, wrap in layout
        $this->renderWithLayout($content, $data);
    }

    private function findViewFile($viewPath)
    {
        // Extract module from current controller
        $controllerClass = get_class($this);
        $moduleName = strtolower(str_replace('Controller', '', $controllerClass));
        
        $possiblePaths = [
            MODULES_PATH . '/' . $moduleName . '/views/' . $viewPath . '.php',
            SHARED_PATH . '/views/' . $viewPath . '.php'
        ];

        foreach ($possiblePaths as $path) {
            if (file_exists($path)) {
                return $path;
            }
        }

        return null;
    }

    private function renderWithLayout($content, $data = [])
    {
        // Default layout data
        $layoutData = array_merge([
            'title' => SITE_NAME,
            'content' => $content,
            'user' => $this->security->getCurrentUser(),
            'csrf_token' => $this->security->generateCSRFToken(),
            'current_language' => $_SESSION['language'] ?? DEFAULT_LANGUAGE,
            'current_theme' => $_SESSION['theme'] ?? DEFAULT_THEME
        ], $data);

        extract($layoutData);

        // Include layout
        $layoutFile = SHARED_PATH . '/layouts/main.php';
        if (file_exists($layoutFile)) {
            include $layoutFile;
        } else {
            // Fallback: output content directly
            echo $content;
        }
    }

    protected function json($data, $statusCode = 200)
    {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    protected function redirect($path, $statusCode = 302)
    {
        $this->router->redirect($path, $statusCode);
    }

    protected function back()
    {
        $this->router->back();
    }

    protected function isAjaxRequest()
    {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
               strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
    }

    protected function validateCSRF()
    {
        $token = $_POST[CSRF_TOKEN_NAME] ?? $_GET[CSRF_TOKEN_NAME] ?? '';
        if (!$this->security->validateCSRFToken($token)) {
            if ($this->isAjaxRequest()) {
                $this->json(['error' => 'Invalid CSRF token'], 403);
            } else {
                die('Invalid CSRF token');
            }
        }
    }

    protected function requireLogin()
    {
        if (!$this->security->isLoggedIn()) {
            if ($this->isAjaxRequest()) {
                $this->json(['error' => 'Authentication required'], 401);
            } else {
                $this->redirect('/login');
            }
        }
    }

    protected function requireAdmin()
    {
        $this->requireLogin();
        $this->security->requireAdmin();
    }

    protected function getInput($key = null, $default = null)
    {
        $input = array_merge($_GET, $_POST);
        $input = $this->security->sanitizeInput($input);
        
        if ($key === null) {
            return $input;
        }
        
        return $input[$key] ?? $default;
    }

    protected function validate($rules, $data = null)
    {
        if ($data === null) {
            $data = $this->getInput();
        }

        $errors = [];

        foreach ($rules as $field => $rule) {
            $value = $data[$field] ?? null;
            $ruleList = explode('|', $rule);

            foreach ($ruleList as $singleRule) {
                if ($singleRule === 'required' && empty($value)) {
                    $errors[$field] = ucfirst($field) . ' is required';
                    break;
                }

                if ($singleRule === 'email' && !empty($value) && !$this->security->validateEmail($value)) {
                    $errors[$field] = ucfirst($field) . ' must be a valid email';
                    break;
                }

                if ($singleRule === 'password' && !empty($value) && !$this->security->validatePassword($value)) {
                    $errors[$field] = ucfirst($field) . ' must be at least 8 characters with uppercase, lowercase and number';
                    break;
                }

                if (strpos($singleRule, 'min:') === 0) {
                    $min = (int)substr($singleRule, 4);
                    if (!empty($value) && strlen($value) < $min) {
                        $errors[$field] = ucfirst($field) . " must be at least $min characters";
                        break;
                    }
                }

                if (strpos($singleRule, 'max:') === 0) {
                    $max = (int)substr($singleRule, 4);
                    if (!empty($value) && strlen($value) > $max) {
                        $errors[$field] = ucfirst($field) . " must not exceed $max characters";
                        break;
                    }
                }
            }
        }

        return $errors;
    }

    protected function setFlash($type, $message)
    {
        $_SESSION['flash'][$type] = $message;
    }

    protected function getFlash($type = null)
    {
        if ($type === null) {
            $flash = $_SESSION['flash'] ?? [];
            unset($_SESSION['flash']);
            return $flash;
        }

        $message = $_SESSION['flash'][$type] ?? null;
        unset($_SESSION['flash'][$type]);
        return $message;
    }

    protected function csrf()
    {
        $token = $this->security->generateCSRFToken();
        return '<input type="hidden" name="' . CSRF_TOKEN_NAME . '" value="' . $token . '">';
    }

    protected function setFlashMessage($message, $type = 'info')
    {
        $_SESSION['flash'][$type] = $message;
    }
}
