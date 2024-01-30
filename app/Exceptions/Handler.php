<?php

namespace App\Exceptions;

use Illuminate\Database\QueryException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->renderable(function (NotFoundHttpException $e, $request) {
            if ($request->is('api/v1/*')) {
                $setup_name = $request->segment(3);
                $message =  ($setup_name == null ? 'Record' : $setup_name) . ' not found';
                return response()->json($message, Response::HTTP_NOT_FOUND);
            }
        });

        $this->renderable(function (QueryException $e, $request) {
            if ($request->is('api/v1/*')) {
                $errorCode = $e->getCode();
                $errorDetail = $e->errorInfo[2]; // This should contain the detailed error message

                // Check if the error code indicates a data integrity violation
                if ($errorCode === '22001') {
                    // Extract the column name from the error detail (this may vary depending on the specific database error message format)
                    preg_match('/value too long for type .+ \((.+)\)/', $errorDetail, $matches);

                    // Check if the column name was extracted successfully and display it
                    if (isset($matches[1])) {
                        $columnName = $matches[1];
                        $error = "The column '$columnName' violated the database schema rules.";
                    } else {
                        $error = "A data integrity violation occurred, but the specific column could not be determined.";
                    }
                } else {
                    // Handle other types of exceptions
                    $error = "An error occurred: " . $e->getMessage();
                }
                info($e->getMessage());
                return response()->json([
                    'message' => 'Invalid query',
                    'errors' => $error,
                    'code' => Response::HTTP_INTERNAL_SERVER_ERROR
                ], Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        });
    }
}
