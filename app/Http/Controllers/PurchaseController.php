<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

class PurchaseController extends Controller
{
    public function index(Request $request)
    {
        try {
            $contact_id = $request->input('contact_id');
            return Purchase::query()->where('contact_id', $contact_id)->get();
        } catch (Exception $e) {
            return response()->json(['message' => 'couldn\'t read the purchase'], Response::HTTP_BAD_REQUEST);
        }
    }

    public function add(Request $request)
    {
        $rules = array(
            'contact_id' => 'required|integer',
            'price' => 'required|numeric',
        );
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json($validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            $contact_id = $request->input('contact_id');

            $purchase = new Purchase([
                'price' => $request->input('price'),
            ]);

            $purchase->contact()->associate(Purchase::query()->find($contact_id));
            if ($purchase->save()) {
                return $purchase;
            } else {
                return response()->json([
                    'message' => 'couldn\'t create the purchase',
                ], Response::HTTP_BAD_REQUEST);
            }
        } catch (Exception $e) {
        }

        return response()->json([
            'message' => 'couldn\'t add the purchase',
        ], Response::HTTP_BAD_REQUEST);
    }

    public function show($id)
    {
        $purchase = Purchase::query()->where('id', $id)->first();
        if (is_null($purchase)) {
            return response()->json([
                'message' => 'couldn\'t show the purchase',
            ], Response::HTTP_BAD_REQUEST);
        }

        return $purchase;
    }

    public function update(Request $request)
    {
        $affected = 0;
        try {
            $id = $request->input('id');

            $rules = array(
                'price' => 'required|numeric',
            );
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return response()->json($validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            $affected = Purchase::query()->where('id', $id)->update([
                'price' => $request->input('price'),
            ]);
        } catch (Exception $e) {
        }

        return $affected > 0 ?
            response()->json(['message' => 'success'], Response::HTTP_OK) :
            response()->json(['message' => 'couldn\'t update the purchase'], Response::HTTP_BAD_REQUEST);
    }

    public function destroy(Request $request)
    {
        try {
            if ($idPurchase = $request->input('id')) {
                Purchase::query()->where('id', $idPurchase)->delete();
                return response()->json([
                    'message' => 'success',
                ], Response::HTTP_OK);
            }
        } catch (Exception $e) {
        }

        return response()->json([
            'message' => 'couldn\'t delete the purchase',
        ], Response::HTTP_BAD_REQUEST);
    }
}
