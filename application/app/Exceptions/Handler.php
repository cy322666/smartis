<?php

namespace App\Exceptions;

use App\Services\Telegram;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Support\Facades\Log;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
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
     *
     * @return void
     */
    public function register()
    {
//        $this->reportable(function (Throwable $e) {
//
//            Log::error(__METHOD__, [$e->getFile().' : '.$e->getLine(), $e->getMessage()]);
//
//            $msg   = str_replace(['*', '_', '&', '@'], '', substr($e->getMessage(), 0, 150));
//            $title = $e->getFile().' : '.$e->getLine();
//
//            try {
//
//                Telegram::send('*Ошибка в коде!* '."\n"."*Где:* $title"."\n"."*Текст:* $msg", env('TG_CHAT_DEBUG'), env('TG_TOKEN_DEBUG'), []);
//
//            } catch (Throwable $e) {
//
//                Telegram::send('REPORT ERROR : '.$title, env('TG_CHAT_DEBUG'), env('TG_TOKEN_DEBUG'), []);
//            }
//        });
    }
}
