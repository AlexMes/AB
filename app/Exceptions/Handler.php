<?php

namespace App\Exceptions;

use App\AdsBoard;
use App\Binom\Exceptions\BinomReponseException;
use FacebookAds\Http\Exception\EmptyResponseException;
use FacebookAds\Http\Exception\ServerException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        BinomReponseException::class,
        EmptyResponseException::class,
        ServerException::class,
        AccessDeniedHttpException::class,
        ValidationException::class,
        AuthenticationException::class,
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
     * Report or log an exception.
     *
     * @param \Throwable $exception
     *
     * @throws \Throwable
     *
     * @return void
     */
    public function report(Throwable $exception)
    {
        if (
            app()->environment('production')
            && ! app()->runningInConsole()
            && ! in_array(get_class($exception), $this->dontReport)
        ) {
            AdsBoard::report(
                'Request time: ' . now()->toDateTimeString() . PHP_EOL .
                    'Request IP: ' . request()->getClientIp() . PHP_EOL .
                    'Path: ' . request()->fullUrl() . PHP_EOL .
                    'Method: ' . request()->method() . PHP_EOL .
                    'User agent: ' . request()->userAgent() . PHP_EOL .
                    'User:  '. optional(auth()->user())->name . PHP_EOL .
                    'Error: '. $exception->getMessage() ?? 'No message'
            );
        }

        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Throwable               $exception
     *
     * @throws \Throwable
     *
     * @return \Illuminate\Http\Response|\Symfony\Component\HttpFoundation\Response
     */
    public function render($request, Throwable $exception)
    {
        return parent::render($request, $exception);
    }
}
