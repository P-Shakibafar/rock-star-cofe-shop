<?php

namespace App\Exceptions;

use Throwable;
use Exception;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use function env;
use function strtolower;
use function class_basename;

class Handler extends ExceptionHandler {

    use ApiResponser;

    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        AuthorizationException::class,
        AuthenticationException::class,
        HttpException::class,
        ModelNotFoundException::class,
        ValidationException::class,
        NotFoundHttpException::class,
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
        //
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Throwable               $exception
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Throwable
     */
    private function renderHTTPResponse( Request $request, Throwable $exception )
    {
        if( $exception instanceof HttpException ) {
            $code    = $exception->getStatusCode();
            $message = Response::$statusTexts[$code];

            return $this->errorResponse( $message, $code );
        }
        if( $exception instanceof ModelNotFoundException ) {
            $model = strtolower( class_basename( $exception->getModel() ) );

            return $this->errorResponse( "There is no instance of {$model} with the given ID", Response::HTTP_NOT_FOUND );
        }
        if( $exception instanceof AuthorizationException ) {
            return $this->errorResponse( $exception->getMessage(), Response::HTTP_FORBIDDEN );
        }
        if( $exception instanceof AuthenticationException ) {
            return $this->errorResponse( $exception->getMessage(), Response::HTTP_UNAUTHORIZED );
        }
        if( $exception instanceof NotFoundHttpException ) {
            return $this->errorResponse( $exception->getMessage(), Response::HTTP_NOT_FOUND );
        }
        if( $exception instanceof ValidationException ) {
            $errors = $exception->validator->errors()->getMessages();

            return $this->errorResponse( $errors, Response::HTTP_UNPROCESSABLE_ENTITY );
        }
        if( env( 'APP_DEBUG', FALSE ) ) {
            return parent::render( $request, $exception );
        }

        return $this->errorResponse( $exception->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR );
    }

    public function render( $request, Throwable $e )
    {
        return $this->renderHTTPResponse( $request, $e );
    }
}
