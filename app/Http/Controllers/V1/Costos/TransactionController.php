<?php

namespace App\Http\Controllers\V1\Costos;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\TransactionProduct;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class TransactionController extends Controller
{
    public function allTransactions($idUser)
    {
        try {
            $queryAll = Transaction::where("user_id", $idUser)
                ->select("transactions.*")
                ->limit(100)
                ->get()->map(function ($transaction) {
                    $transaction->income = ($transaction->income) ? "Ingreso" : "Gasto";
                    return $transaction;
                });
            return $queryAll;
        } catch (Exception $ex) {
            return response()->json(['error' => "Error al consultar tus costos"], 500);
        }
    }

    public function detailTransaction($idTransaction)
    {
        try {
            $queryDetail = Transaction::find($idTransaction);
            $queryDetail->products = Transaction::find($idTransaction)->products->pluck('product_id');
            return $queryDetail;
        } catch (Exception $ex) {
            return response()->json(['error' => "Error al obtener el costo"], 500);
        }
    }

    public function createTransaction(Request $request)
    {
        try {
            $validatedData = Validator::make($request->all(), [
                'amount' => ['required', 'regex:/^\d+(\.\d{1,2})?$/'],
                'description' => 'nullable|string|max:1000',
                'currentDate' => 'required|date',
                'income' => 'required|boolean',
                'user_id' => 'required|integer|exists:users,id',
                'products' => 'nullable|array',
            ]);
            if ($validatedData->fails()) {
                return response()->json(['error' => $validatedData->errors()->toArray()], 400);
            }
            $newTransaction = Transaction::create($request->all());

            if (!empty($request->products)) {
                foreach ($request->products as $product) {
                    TransactionProduct::firstOrCreate([
                        'transaction_id_foreign' => $newTransaction->transaction_id,
                        'product_id_foreign' => $product
                    ]);
                }
            }
            return response()->json([
                'message' => "Se guardo el costo exitosamente",
                'data' => $newTransaction
            ], Response::HTTP_OK);
        } catch (Exception $ex) {
            return response()->json(['error' => "Error al crear el costo"], 500);
        }
    }

    public function updateTransaction(Request $request, $idTransaction)
    {
        try {
            $validatedData = Validator::make($request->all(), [
                'amount' => ['required', 'regex:/^\d+(\.\d{1,2})?$/'],
                'description' => 'nullable|string|max:1000',
                'currentDate' => 'required|date',
                'income' => 'required|boolean',
                'products' => 'nullable|array'
            ]);
            if ($validatedData->fails()) {
                return response()->json(['error' => $validatedData->errors()->toArray()], 400);
            }
            $transactionUpdate = Transaction::find($idTransaction);
            if (!$transactionUpdate) {
                return response()->json(['error' => "No existe el costo"], 400);
            }
            $transactionUpdate->update($request->all());
            $deleteTransactionProducts = TransactionProduct::where('transaction_id_foreign', $idTransaction)
                ->whereNotIn('product_id_foreign', $request->products)->get()->pluck('transaction_products_id');
            TransactionProduct::destroy(collect($deleteTransactionProducts));

            if (!empty($request->products)) {
                foreach ($request->products as $product) {
                    TransactionProduct::firstOrCreate([
                        'transaction_id_foreign' => $idTransaction,
                        'product_id_foreign' => $product
                    ]);
                }
            }
            return response()->json([
                'message' => "Se guardo el costo exitosamente",
                'data' => $transactionUpdate
            ], Response::HTTP_OK);
        } catch (Exception $ex) {
            return response()->json(['error' => "Error al guardar el costo"], 500);
        }
    }

    public function deleteSpent($idSpent)
    {
        try {
            $deleteQuery = Transaction::find($idSpent);
            if (!$deleteQuery) {
                return response()->json(['error' => "No existe el costo"], 400);
            }
            // $existRelacionesParticipante = $deleteProduct->secureDelete("gastos");
            // if ($existRelacionesParticipante) {

            // }
            $deleteQuery->delete();
            return response()->json([
                'message' => "Se eliminó el costo exitosamente",
            ], Response::HTTP_OK);
        } catch (Exception $ex) {
            return response()->json(['error' => $ex->getMessage()], 500);
        }
    }
}
