<?php

namespace App\Http\Controllers;

use App\Models\Certificate;
use App\Models\Contact;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

class CertificateController extends Controller
{

    public function index(Request $request)
    {
        try {
            $contact_id = $request->input('contact_id');
            return Certificate::query()->where('contact_id', $contact_id)->get();
        } catch (Exception $e) {
            return response()->json(['message' => 'couldn\'t read the certificates'], Response::HTTP_BAD_REQUEST);
        }
    }

    public function add(Request $request)
    {
        $rules = array(
            'contact_id' => 'required|integer',
            'service_name' => 'required',
            'price' => 'required|numeric',
            'status' => 'required|boolean',
        );
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json($validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            $contact_id = $request->input('contact_id');

            $certificate = new Certificate([
                'service_name' => $request->input('service_name'),
                'price' => $request->input('price'),
                'status' => $request->input('status')
            ]);
            $certificate->contact()->associate(Contact::query()->find($contact_id));
            if ($certificate->save()) {
                return $certificate;
            } else {
                return response()->json([
                    'message' => 'couldn\'t create the certificate',
                ], Response::HTTP_BAD_REQUEST);
            }
        } catch (Exception $e) {
        }

        return response()->json([
            'message' => 'couldn\'t add the certificate',
        ], Response::HTTP_BAD_REQUEST);
    }

    public function show($id)
    {
        $certificate = Certificate::query()->where('id', $id)->first();
        if (is_null($certificate)) {
            return response()->json([
                'message' => 'couldn\'t show the certificate',
            ], Response::HTTP_BAD_REQUEST);
        }

        return $certificate;
    }

    public function update(Request $request)
    {
        $affected = 0;
        try {
            $id = $request->input('id');

            $rules = array(
                'service_name' => 'required',
                'price' => 'required|numeric',
                'status' => 'required|boolean',
            );
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return response()->json($validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            $affected = Certificate::query()->where('id', $id)->update([
                'service_name' => $request->input('service_name'),
                'price' => $request->input('price'),
                'status' => $request->input('status')
            ]);
        } catch (Exception $e) {
        }

        return $affected > 0 ?
            response()->json(['message' => 'success'], Response::HTTP_OK) :
            response()->json(['message' => 'couldn\'t update the certificate'], Response::HTTP_BAD_REQUEST);
    }

    public function destroy(Request $request)
    {
        try {
            if ($idCertificate = $request->input('id')) {
                Certificate::query()->where('id', $idCertificate)->delete();
                return response()->json([
                    'message' => 'success',
                ], Response::HTTP_OK);
            }
        } catch (Exception $e) {
        }

        return response()->json([
            'message' => 'couldn\'t delete the certificate',
        ], Response::HTTP_BAD_REQUEST);
    }
}
