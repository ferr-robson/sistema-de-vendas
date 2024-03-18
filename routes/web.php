<?php

use Illuminate\Support\Facades\Route;

use Barryvdh\DomPDF\Facade\Pdf;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/pdf-venda/{id}', function ($id) {
    $controlador = new App\Http\Controllers\VendaController;
    $item = App\Models\Venda::findOrFail($id);
    $jsonResponse = $controlador->show($item);
    $data = json_decode($jsonResponse->content(), true);
    $pdf = Pdf::loadView('pdf_vendas', $data);
    return $pdf->stream();
});
