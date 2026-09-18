<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;

abstract class Controller
{
    /**
     * Sejak Laravel 11, controller dasar tidak lagi membawa trait apa pun.
     * AuthorizesRequests diikutkan di sini supaya seluruh controller modul
     * bisa memanggil $this->authorize() dan memeriksa policy langsung di
     * titik masuk aksinya.
     */
    use AuthorizesRequests, ValidatesRequests;
}
