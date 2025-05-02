<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\VendaController;
use App\Http\Controllers\EntidadeController;
use App\Http\Controllers\ProdutosController;
use App\Http\Controllers\EntidadesController;
use App\Http\Controllers\CategoriasController;
use App\Http\Controllers\ProdutoVendaController;


// Rotas de autenticação
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Proteja suas rotas existentes com o middleware 'auth'
Route::middleware(['auth'])->group(function () {
    Route::get('/', [HomeController::class, 'index'])->name('home');
    // Todas as outras rotas que exigem login...
    Route::get('/produtos', [ProdutosController::class, 'index'])->name('produtos.index');
    // ...


Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/produtos', [ProdutosController::class , 'index'])->name('produtos.index');

Route::get('/cadastrar', [ProdutosController::class, 'create'])->name('produtos.cadastrar');
Route::post('/store-produtos', [ProdutosController::class, 'store'])->name('produtos.store');
Route::get('/atualizar-produtos/{id}', [ProdutosController::class, 'edit'])->name('produtos.edit');
Route::post('/update-produtos/{id}', [ProdutosController::class, 'update'])->name('produtos.update');

Route::get('/visualizar-produtos/{id}', [ProdutosController::class, 'show'])->name('produtos.show');
Route::delete('/produtos/{id}', [ProdutosController::class, 'destroy'])->name('produtos.destroy');
Route::get('/categorias', [CategoriasController::class , 'index'])->name('categorias.index');
Route::get('/cadastrar-categoria', [CategoriasController::class, 'create'])->name('categorias.cadastrar');
Route::post('/store-categorias', [CategoriasController::class, 'store'])->name('categorias.store');
Route::get('/atualizar-categoria/{id}', [CategoriasController::class, 'edit'])->name('categorias.edit');
Route::post('/update-categorias/{id}', [CategoriasController::class, 'update'])->name('categorias.update');
Route::get('/visualizar-categorias/{id}', [CategoriasController::class, 'show'])->name('categorias.show');
Route::delete('/categorias/{id}', [CategoriasController::class, 'destroy'])->name('categorias.destroy');

Route::get('/planejamentos', [VendaController::class, 'index'])->name('vendas.index');
Route::get('/produtos/{id}', [ProdutosController::class, 'getProduto']);
Route::post('/store-vendas', [VendaController::class, 'store'])->name('vendas.store');

Route::get('/vendas/download-pdf', [ProdutoVendaController::class, 'downloadPdf'])->name('vendas.downloadPdf');
Route::get('/vendas/buscar', [ProdutoVendaController::class, 'buscarVendaProduto'])->name('vendas.buscar');
Route::post('/vendas/pdf-produto', [ProdutoVendaController::class, 'PdfProduto'])->name('vendas.PdfProduto');
Route::get('/vendas/pdf/{venda_id}', [ProdutoVendaController::class, 'PdfVenda'])->name('vendas.PdfVenda');

Route::prefix('entidades')->group(function () {
    Route::get('/', [EntidadesController::class, 'index'])->name('entidades.index');
    Route::get('/cadastrar', [EntidadesController::class, 'create'])->name('entidades.create');
    Route::post('/store', [EntidadesController::class, 'store'])->name('entidades.store');
    Route::get('/editar/{entidade}', [EntidadesController::class, 'edit'])->name('entidades.edit');
    Route::put('/update/{entidade}', [EntidadesController::class, 'update'])->name('entidades.update');
    Route::delete('/excluir/{entidade}', [EntidadesController::class, 'destroy'])->name('entidades.destroy');
    Route::get('/{entidade?}/vendas', [EntidadeController::class, 'vendas'])
     ->name('entidades.vendas')
     ->where('entidade', '[0-9]+');
});

Route::get('/vendas/{venda}/edit', [VendaController::class, 'edit'])->name('vendas.edit');
Route::put('/vendas/{venda}', [VendaController::class, 'update'])->name('vendas.update');
Route::delete('/vendas/{venda}', [VendaController::class, 'destroy'])->name('vendas.destroy');
Route::get('/vendas/listar', [VendaController::class, 'listar'])->name('vendas.listar');
Route::get('/relatorios/vendas-produtos', [ProdutoVendaController::class, 'buscarVendaProduto'])
     ->name('vendas.buscarVendaProduto');

});
