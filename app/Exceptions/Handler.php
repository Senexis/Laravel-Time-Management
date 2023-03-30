<?php

namespace App\Exceptions;

use App\Jobs\PostGithubIssue;

use Exception;
use GitHub;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

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
     * Report or log an exception.
     *
     * @param  \Exception  $exception
     * @return void
     */
    public function report(Exception $exception)
    {
        if ($this->shouldntReport($exception) || env('APP_DEBUG') || !config('settings.github_error_reporting')) {
            return;
        }

        $hash = sha1($exception->getMessage());
        $now = CarbonImmutable::now();

        if (!Cache::has('exception_' . $hash)) {
            $title = $exception->getMessage();
            $body = "An unhandled exception occured for a user while the application was not in debug mode. All relevant details can be found below.\n\n**Timestamp:** `{$now}`\n**Code:** `{$exception->getCode()}`\n\n**Message:**\n```\n{$exception->getMessage()}\n```\n\n**Stack trace:**\n```\n{$exception->getFile()} at line {$exception->getLine()}\n\n{$exception->getTraceAsString()}\n```";
            $labels = ['Exception'];

            PostGithubIssue::dispatch($title, $body, $labels);
            Cache::put('exception_' . $hash, '1', now()->addMinute());
        }

        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $exception)
    {
        return parent::render($request, $exception);
    }

    /**
     * Render the given HttpException.
     *
     * @param  \Symfony\Component\HttpKernel\Exception\HttpExceptionInterface  $exception
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function renderHttpException(HttpExceptionInterface $exception)
    {
        $exception_status_code = $exception->getStatusCode();

        if (!view()->exists('errors.' . $exception_status_code)) {
            $exception_headers = $exception->getHeaders();

            $relations = [
                'exception' => $exception,
                'exception_status_code' => $exception_status_code,
                'exception_headers' => $exception_headers
            ];

            return response()->view('errors.default', $relations, $exception_status_code, $exception_headers);
        }

        return parent::renderHttpException($exception);
    }
}
