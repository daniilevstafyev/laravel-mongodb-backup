<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Throwable $exception)
    {
        $statusCode = $this->isHttpException($exception) ? $exception->getStatusCode() : $exception->getCode();
        switch ($statusCode) {
            case 404:
                return response(view('errors.404'),$statusCode);
                break;
            default:
                // if ($this->debugMode) {
                //     dd($exception);
                // }
                return response(view('connect', [
                    'error' => 'Connection Failed Or Something went wrong.',
                ]), 500);
                break;
        }
    }
}
