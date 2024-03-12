<?php

use App\Http\Controllers\ParcelaController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VendaController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\ProdutoController;
use App\Http\Controllers\ItemVendaController;
use App\Http\Controllers\FormaPagamentoController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::resource('usuario', UserController::class);

Route::middleware('auth:sanctum')->group(function () {
    Route::resource('cliente', ClienteController::class);
    Route::resource('produto', ProdutoController::class);
    Route::resource('venda', VendaController::class);
    Route::resource('forma-pagamento', FormaPagamentoController::class);
    Route::resource('item-venda', ItemVendaController::class);
    Route::resource('parcela', ParcelaController::class);
});

Route::post('/login', function (Request $request) {
    if(Auth::attempt(['email'=> $request->email,'password'=> $request->password])) {
        $user = Auth::user();
        $token = $user->createToken('jwt_gamifica_tarefas');
        return response()->json($token->plainTextToken, 200);
    }
    return response()->json('Usuário invalido', 401);
});

/*
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
*/
