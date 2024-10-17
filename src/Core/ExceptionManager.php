<?php

namespace App\Core;

use App\Exceptions\BaseException;
use App\Facades\Logger;

use Throwable;

class ExceptionManager
{
    public function __construct()
    {
    }

    protected function to_html(string $view, int $status)
    {
        return response(
            view($view)->render(), 
            $status
        )
        ->header('Content-Type', 'text/html')
        ->send();
    }

    protected function to_xml(string $message, int $status, array $details = [])
    {
        $response = [
            'status' => $status,
            'message' => $message,
            'details' => $details,
            'timestamp' => date('Y-m-d\TH:i:s\Z'),
        ];

        $xml = new \SimpleXMLElement('<response/>');
        array_walk_recursive($response, function ($value, $key) use ($xml) {
            $xml->addChild($key, $value);
        });

        return response(
            $xml->asXML(),
            $status
        )
        ->header('Content-Type', 'application/xml')
        ->send();
    }

    protected function to_json(string $message, int $status, array $details = [])
    {
        $response = [
            'status' => $status,
            'message' => $message,
            'details' => $details,
            'timestamp' => date('Y-m-d\TH:i:s\Z'),
        ];

        return response(
            json_encode($response),
            $status
        )
        ->header('Content-Type', 'application/json')
        ->send();
    }

    protected function log_exception(Throwable $exception)
    {
        $message = sprintf(
            "[%s] %s: %s in %s:%d\nStack trace:\n%s",
            date('Y-m-d H:i:s'),
            get_class($exception),
            $exception->getMessage(),
            $exception->getFile(),
            $exception->getLine(),
            $exception->getTraceAsString()
        );

        Logger::error($message);
    }

    protected function is_api(): bool
    {
        $request = request();

        return $request->expects_json() ||
               (strpos($request->path(), 'api/') === 0);
    }

    // Funzione generica per gestire errori
    public function handle(Throwable $exception)
    {
        $this->log_exception($exception);

        if ($this->is_api()) {
            if ($exception instanceof BaseException) {
                return $this->to_json($exception->getMessage(), $exception->getStatusCode());
            }

            return $this->to_json('An unexpected error occurred.', 500);
        }

        // Se non è una richiesta API, restituisci una risposta HTML
        if ($exception instanceof BaseException) {
            return $this->to_html($exception->getView(), $exception->getStatusCode());
        }

        // Se non è un'eccezione gestita, ritorna messaggio di errore che l'ha generata
        return response($exception->getMessage(), 500)
            ->header('Content-Type', 'text/plain')
            ->send();
    }
}
