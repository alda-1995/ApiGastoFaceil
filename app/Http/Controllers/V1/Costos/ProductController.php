<?php

namespace App\Http\Controllers\V1\Costos;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{

    public function allProducts($idUser){
        $products = Product::where("user_id", $idUser)->limit(100)->get();
        return $products;
    }

    public function detailProduct($idProduct){
        try {
            $queryDetail = Product::where("product_id", $idProduct)
            ->with("gastos")
            ->first();
            return $queryDetail;
        } catch (Exception $ex) {
            return response()->json(['error' => "Error al obtener el producto"], 400);
        }
    }

    public function createProduct(Request $request)
    {
        try {
            $validatedData = Validator::make($request->all(), [
                'name' => 'required|string|max:800',
                'description' => 'required|string|max:1000',
                'user_id' => 'required|integer|exists:users,id',
            ]);
            if ($validatedData->fails()) {
                return response()->json(['error' => $validatedData->errors()->toArray()], 400);
            }
            $product = Product::create($request->all());
            return response()->json([
                'message' => "Se creo el producto exitosamente",
                'data' => $product
            ], Response::HTTP_OK);
        } catch (Exception $ex) {
            return response()->json(['error' => "Error al guardar el producto"], 400);
        }
    }

    public function updateProduct(Request $request, $idProduct)
    {
        try {
            $validatedData = Validator::make($request->all(), [
                'name' => 'required|string|max:800',
                'description' => 'required|string|max:1000',
            ]);
            if ($validatedData->fails()) {
                return response()->json(['error' => $validatedData->errors()->toArray()], 400);
            }
            $productUpdate = Product::find($idProduct);
            if (!$productUpdate) {
                return response()->json(['error' => "No existe el producto"], 400);
            }
            $productUpdate->update($request->all());
            return response()->json([
                'message' => "Se actualizo el producto exitosamente",
                'data' => $productUpdate
            ], Response::HTTP_OK);
        } catch (Exception $ex) {
            return response()->json(['error' => "Error al actualizar el producto"], 400);
        }
    }

    public function deleteProduct($idProduct)
    {
        try {
            $deleteProduct = Product::find($idProduct);
            if (!$deleteProduct) {
                return response()->json(['error' => "No existe el producto"], 400);
            }
            $existRelacionesParticipante = $deleteProduct->secureDelete("gastos");
            if ($existRelacionesParticipante) {

            }
            $deleteProduct->delete();
        } catch (Exception $ex) {
            return response()->json(['error' => "Error al eliminar el producto"], 400);
        }
    }
}
