<?php

namespace App\Controllers;

use App\Config\App;
use App\Services\Logger;
use App\Services\Auth;
use App\Services\Validator;
use App\Services\Response;

abstract class BaseController
{
    protected $auth;
    protected $logger;
    protected $validator;
    protected $response;
    
    public function __construct()
    {
        $this->auth = new Auth();
        $this->logger = new Logger();
        $this->validator = new Validator();
        $this->response = new Response();
    }
    
    /**
     * Render view with data
     */
    protected function view($view, $data = [])
    {
        // Extract data to variables
        extract($data);
        
        // Get view path
        $viewPath = $this->getViewPath($view);
        
        if (!file_exists($viewPath)) {
            throw new \Exception("View not found: {$view}");
        }
        
        // Start output buffering
        ob_start();
        
        // Include view file
        include $viewPath;
        
        // Get content and clean buffer
        $content = ob_get_clean();
        
        return $content;
    }
    
    /**
     * Render view with layout
     */
    protected function render($view, $data = [], $layout = 'app')
    {
        // Render view content
        $content = $this->view($view, $data);
        
        // Render layout with content
        $layoutData = array_merge($data, ['content' => $content]);
        
        echo $this->view("layouts/{$layout}", $layoutData);
    }
    
    /**
     * Get view file path
     */
    private function getViewPath($view)
    {
        return dirname(__DIR__) . "/Views/{$view}.php";
    }
    
    /**
     * Redirect to URL
     */
    protected function redirect($url, $code = 302)
    {
        if (!headers_sent()) {
            http_response_code($code);
            header("Location: {$url}");
        }
        exit();
    }
    
    /**
     * Redirect with message
     */
    protected function redirectWithMessage($url, $message, $type = 'success')
    {
        $_SESSION['flash_message'] = [
            'message' => $message,
            'type' => $type
        ];
        
        $this->redirect($url);
    }
    
    /**
     * Get flash message
     */
    protected function getFlashMessage()
    {
        if (isset($_SESSION['flash_message'])) {
            $message = $_SESSION['flash_message'];
            unset($_SESSION['flash_message']);
            return $message;
        }
        return null;
    }
    
    /**
     * Return JSON response
     */
    protected function json($data, $code = 200)
    {
        return $this->response->json($data, $code);
    }
    
    /**
     * Return success JSON response
     */
    protected function jsonSuccess($data = null, $message = 'Success', $code = 200)
    {
        return $this->json([
            'success' => true,
            'message' => $message,
            'data' => $data
        ], $code);
    }
    
    /**
     * Return error JSON response
     */
    protected function jsonError($message = 'Error', $code = 400, $errors = null)
    {
        return $this->json([
            'success' => false,
            'message' => $message,
            'errors' => $errors
        ], $code);
    }
    
    /**
     * Validate request data
     */
    protected function validate($data, $rules)
    {
        return $this->validator->validate($data, $rules);
    }
    
    /**
     * Check if user is authenticated
     */
    protected function requireAuth()
    {
        if (!$this->auth->check()) {
            $this->redirectWithMessage('/auth/login', 'Please login to continue', 'error');
        }
    }
    
    /**
     * Check if user has specific role
     */
    protected function requireRole($role)
    {
        $this->requireAuth();
        
        if (!$this->auth->hasRole($role)) {
            $this->redirectWithMessage('/dashboard', 'Access denied', 'error');
        }
    }
    
    /**
     * Check if user has any of the specified roles
     */
    protected function requireAnyRole($roles)
    {
        $this->requireAuth();
        
        if (!$this->auth->hasAnyRole($roles)) {
            $this->redirectWithMessage('/dashboard', 'Access denied', 'error');
        }
    }
    
    /**
     * Get current user
     */
    protected function user()
    {
        return $this->auth->user();
    }
    
    /**
     * Get request method
     */
    protected function getMethod()
    {
        return $_SERVER['REQUEST_METHOD'];
    }
    
    /**
     * Check if request is POST
     */
    protected function isPost()
    {
        return $this->getMethod() === 'POST';
    }
    
    /**
     * Check if request is GET
     */
    protected function isGet()
    {
        return $this->getMethod() === 'GET';
    }
    
    /**
     * Check if request is AJAX
     */
    protected function isAjax()
    {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
               strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }
    
    /**
     * Get request data
     */
    protected function getInput($key = null, $default = null)
    {
        $input = array_merge($_GET, $_POST);
        
        if ($key === null) {
            return $input;
        }
        
        return $input[$key] ?? $default;
    }
    
    /**
     * Get POST data
     */
    protected function getPost($key = null, $default = null)
    {
        if ($key === null) {
            return $_POST;
        }
        
        return $_POST[$key] ?? $default;
    }
    
    /**
     * Get GET data
     */
    protected function getQuery($key = null, $default = null)
    {
        if ($key === null) {
            return $_GET;
        }
        
        return $_GET[$key] ?? $default;
    }
    
    /**
     * Get uploaded files
     */
    protected function getFiles($key = null)
    {
        if ($key === null) {
            return $_FILES;
        }
        
        return $_FILES[$key] ?? null;
    }
    
    /**
     * Log message
     */
    protected function log($message, $level = 'info', $context = [])
    {
        $this->logger->log($level, $message, $context);
    }
    
    /**
     * Handle 404 error
     */
    protected function notFound($message = 'Page not found')
    {
        http_response_code(404);
        
        if ($this->isAjax()) {
            return $this->jsonError($message, 404);
        }
        
        $this->render('errors/404', ['message' => $message]);
        exit();
    }
    
    /**
     * Handle 403 error
     */
    protected function forbidden($message = 'Access denied')
    {
        http_response_code(403);
        
        if ($this->isAjax()) {
            return $this->jsonError($message, 403);
        }
        
        $this->render('errors/403', ['message' => $message]);
        exit();
    }
    
    /**
     * Handle 500 error
     */
    protected function serverError($message = 'Internal server error')
    {
        http_response_code(500);
        
        if ($this->isAjax()) {
            return $this->jsonError($message, 500);
        }
        
        $this->render('errors/500', ['message' => $message]);
        exit();
    }
}
