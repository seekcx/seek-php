<?php

namespace App\Exceptions;

use Exception;
use PDOException;
use Hashids\HashidsException;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Tymon\JWTAuth\Exceptions\TokenBlacklistedException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

trait FormatTrait
{
    /**
     * Recursirvely remove any empty replacement values in the response array.
     *
     * @param array $input
     *
     * @return array
     */
    protected function recursivelyRemoveEmptyReplacements(array $input)
    {
        foreach ($input as &$value) {
            if (is_array($value)) {
                $value = $this->recursivelyRemoveEmptyReplacements($value);
            }
        }

        return array_filter($input, function ($value) {
            if (is_string($value)) {
                return ! starts_with($value, ':');
            }

            return true;
        });
    }

    /**
     * Get the status code from the exception.
     *
     * @param \Exception $exception
     *
     * @return int
     */
    protected function getStatusCode(Exception $exception)
    {
        $statusCode = 500;

        if ($exception instanceof HttpExceptionInterface) {
            $statusCode = $exception->getStatusCode();
        }

        if ($exception instanceof TokenBlacklistedException) {
            return 401;
        }

        if ($exception instanceof ModelNotFoundException or
            $exception instanceof HashidsException) {
            $statusCode = 404;
        }

        if ($exception instanceof ValidationException) {
            $statusCode = 400;
        }

        return $statusCode;
    }

    /**
     * Get the headers from the exception.
     *
     * @param \Exception $exception
     *
     * @return array
     */
    protected function getHeaders(Exception $exception)
    {
        return $exception instanceof HttpExceptionInterface ? $exception->getHeaders() : [];
    }

    /**
     * Get the message from the exception.
     *
     * @param \Exception $exception
     *
     * @return string
     */
    protected function getMessage(Exception $exception)
    {
        if ($exception instanceof PDOException) {
            return 'Storage service exception';
        }

        if ($exception instanceof TokenBlacklistedException) {
            return 'Invalid session or expired';
        }

        if ($exception instanceof ModelNotFoundException or
            $exception instanceof HashidsException) {
            return 'Not found';
        }

        return $exception->getMessage();
    }

    /**
     * Prepare the replacements array by gathering the keys and values.
     *
     * @param \Exception $exception
     *
     * @return array
     */
    protected function prepareReplacements(Exception $exception)
    {
        $statusCode = $this->getStatusCode($exception);

        if (! $message = $this->getMessage($exception)) {
            $message = Response::$statusTexts[$statusCode];
        }

        $replacements = [
            ':message' => ucfirst(strtolower($message))
        ];

        if ($exception instanceof ValidationException) {
            $replacements[':errors'] = $exception->validator->getMessageBag();
        }

        if ($code = $exception->getCode()) {
            $replacements[':code'] = $code;
        }

        if (app()->environment('local')) {
            $replacements[':debug'] = [
                'line'  => $exception->getLine(),
                'file'  => $exception->getFile(),
                'class' => get_class($exception),
                'trace' => explode("\n", $exception->getTraceAsString()),
            ];
        }

        return $replacements;
    }
}
