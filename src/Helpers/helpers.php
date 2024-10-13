<?php

use App\Core\ServiceContainer;

use App\Services\AssetsService;

use App\HTTP\ {
    View,
    Response,
    Redirect,
    Request
};

use App\Core\ {
    Flash,
    Session
};


/**
 * return request object from the container
 */
if (!function_exists('request')) {
    function request(): Request
    {
        $request_ob = (object) ServiceContainer::get_container()->getLazy(Request::class);
        return $request_ob;
    }
}

/**
 * return response object from the container
 */
if (!function_exists('response')) {
    function response($content = '', $status = 200, array $headers = []): Response
    {
        $response_ob = (object) ServiceContainer::get_container()->getLazy(Response::class);
        if ($content) {
            return $response_ob->make($content, $status, $headers);
        }
        return $response_ob;
    }
}

/**
 * return view object from the container
 */
if (!function_exists('view')) {
    function view($view): View
    {
        if (!$view) {
            throw new \InvalidArgumentException('View not set.');
        }

        $view_ob = (object) ServiceContainer::get_container()->getLazy(View::class);
        
        return $view_ob->make($view);
    }
}

/**
 * return redirect object from the container
 */
if (!function_exists('redirect')) {
    function redirect($path = null, $status = 302, $headers = [], $secure = false): Redirect
    {
        $redirect_ob = (object) ServiceContainer::get_container()->getLazy(Redirect::class);

        if ($path) {
            return $redirect_ob->to($path, $status, $headers, $secure);
        }

        return $redirect_ob;
    }
}

if (!function_exists('assets')) {
    function assets($view): array
    {
        if (!$view) {
            throw new \InvalidArgumentException('Assets view not set.');
        }

        $assets_ob = (object) ServiceContainer::get_container()->getLazy(AssetsService::class);
        
        return $assets_ob->load($view);
    }
}

if (!function_exists('flash')) {
    function flash(string $name = '', string $message = '', string $type = '', array $options = []): Flash
    {
        $flash_ob = (object) ServiceContainer::get_container()->getLazy(Flash::class);
        $flash_ob->flash($name, $message, $type, $options);
        return $flash_ob;
    }
}


if (!function_exists('session')) {
    function session($key = null, $value = null): Session
    {
        $session_ob = (object) ServiceContainer::get_container()->getLazy(Session::class);
        if ($key && !$value) {
            return $session_ob->get($key, $value);
        } elseif ($key && $value) {
            return $session_ob->set($key, $value);
        }
        return $session_ob;
    }
}





/**
 * Return a CSRF field
 *
 * @return string
 */
function csrf_field(): string
{
    if (!session()->token() || session()->token_time() < time()) {
        session()->regenerate_token();
    }

    $token = session()->token();
    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token) . '">';
}

function ewt_field($tk): string
{
    return '<input type="hidden" name="ewt" value="' . htmlspecialchars($tk) . '">';
}

function public_path(): string
{
        // Ottieni il percorso della directory corrente
        $currentDir = __DIR__;

        // Definisci il target (cartella public)
        $targetDir = 'public';
    
        // Scopri la root del progetto (o la base directory)
        $baseDir = realpath(__DIR__ . '/..');  // Assumi che il progetto sia strutturato con una root comune
    
        // Trova la directory pubblica
        $targetPath = $baseDir . DIRECTORY_SEPARATOR . $targetDir;
    
        // Conta quanti livelli devi risalire per arrivare alla root del progetto
        $relativePath = '';
        $currentPathParts = explode(DIRECTORY_SEPARATOR, $currentDir);
        $basePathParts = explode(DIRECTORY_SEPARATOR, $baseDir);
    
        $upLevels = count($currentPathParts) - count($basePathParts);
    
        // Risali di $upLevels per arrivare alla root
        for ($i = 0; $i < $upLevels; $i++) {
            $relativePath .= '../';
        }
    
        // Torna alla cartella public dalla base directory
        return $relativePath . $targetDir . '/';
}

/**
 * Return the error class if error is found in the array $errors
 *
 * @param array $errors
 * @param string $field
 * @return string
 */
function error_class(array $errors, string $field): string
{
    return isset($errors[$field]) ? 'error' : '';
}


/**
 * Flash data specified by $keys from the $_SESSION
 * @param ...$keys
 * @return array
 */
function session_flash(...$keys): array
{
    $data = [];
    foreach ($keys as $key) {
        if (session()->has($key)) {
            $data[] = session()->get($key);
            session()->remove($key);
        } else {
            $data[] = [];
        }
    }
    return $data;
}
