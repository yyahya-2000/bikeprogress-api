<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ContactController extends Controller
{
    public function index()
    {
        return Contact::query()
            ->leftJoin('purchases', 'contacts.id', '=', 'purchases.contact_id')
            ->leftJoin('certificates', 'contacts.id', '=', 'certificates.contact_id')
            ->select(
                [
                    'contacts.id',
                    'contacts.firstname',
                    'contacts.lastname',
                    'contacts.contact_name',
                    'contacts.patronymic',
                    'contacts.phone_number',
                    'contacts.extra_phone_number',
                    'contacts.email',
                    'contacts.extra_email',
                    'contacts.loyalty',
                    'contacts.note',
                    DB::raw('COALESCE(SUM(purchases.price),0) as cost'),
                    DB::raw('COUNT(purchases.id) as booking_number')
                ]
            )
            ->groupBy(
                'contacts.id',
                'contacts.firstname',
                'contacts.lastname',
                'contacts.contact_name',
                'contacts.patronymic',
                'contacts.phone_number',
                'contacts.extra_phone_number',
                'contacts.email',
                'contacts.extra_email',
                'contacts.loyalty',
                'contacts.note'
            )->get();
    }

    public function create(Request $request)
    {
        $rules = array(
            'contact_name' => 'required',
            'firstname' => 'required',
            'lastname' => 'required',
            'phone_number' => [
                'required',
                'unique:contacts',
                'regex:/^(\+7|7|8)?[\s\-]?\(?[489][0-9]{2}\)?[\s\-]?[0-9]{3}[\s\-]?[0-9]{2}[\s\-]?[0-9]{2}$/'
            ],
            'extra_phone_number' => [
                'unique:contacts',
                'regex:/^(\+7|7|8)?[\s\-]?\(?[489][0-9]{2}\)?[\s\-]?[0-9]{3}[\s\-]?[0-9]{2}[\s\-]?[0-9]{2}$/'
            ],
            'email' => 'required|unique:contacts',
            'extra_email' => 'unique:contacts',
            'loyalty' => 'required|digits:11|unique:contacts',
        );
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json($validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $contact = new Contact([
            'contact_name' => $request->input('contact_name'),
            'firstname' => $request->input('firstname'),
            'lastname' => $request->input('lastname'),
            'patronymic' => $request->input('patronymic'),
            'phone_number' => $request->input('phone_number'),
            'extra_phone_number' => $request->input('extra_phone_number'),
            'email' => $request->input('email'),
            'extra_email' => $request->input('extra_email'),
            'loyalty' => $request->input('loyalty'),
            'note' => $request->input('note'),
        ]);

        if ($contact->save()) {
            return $contact;
        } else {
            return response()->json([
                'message' => 'couldn\'t create the contact',
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    public function show($id)
    {
        $contact = Contact::query()->where('id', $id)->first();
        if (is_null($contact)) {
            return response()->json([
                'message' => 'couldn\'t show the contact',
            ], Response::HTTP_BAD_REQUEST);
        }
        return $contact;
    }

    public function update(Request $request)
    {
        $affected = 0;
        try {
            $id = $request->input('id');
            $rules = array(
                'contact_name' => 'required',
                'firstname' => 'required',
                'lastname' => 'required',
                'phone_number' => [
                    'required',
                    'unique:contacts',
                    Rule::unique('contacts')->ignore($id),
                    'regex:/^(\+7|7|8)?[\s\-]?\(?[489][0-9]{2}\)?[\s\-]?[0-9]{3}[\s\-]?[0-9]{2}[\s\-]?[0-9]{2}$/'
                ],
                'extra_phone_number' => [
                    'unique:contacts',
                    Rule::unique('contacts')->ignore($id),
                    'regex:/^(\+7|7|8)?[\s\-]?\(?[489][0-9]{2}\)?[\s\-]?[0-9]{3}[\s\-]?[0-9]{2}[\s\-]?[0-9]{2}$/'
                ],
                'email' => [
                    'required',
                    'unique:contacts',
                    Rule::unique('contacts')->ignore($id)
                ],
                'extra_email' => [
                    'unique:contacts',
                    Rule::unique('contacts')->ignore($id)
                ],
                'loyalty' => [
                    'required',
                    'digits:11',
                    'unique:contacts',
                    Rule::unique('contacts')->ignore($id)
                ]
            );

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json($validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            $affected = Contact::query()->where('id', $id)->update([
                'contact_name' => $request->input('contact_name'),
                'firstname' => $request->input('firstname'),
                'lastname' => $request->input('lastname'),
                'patronymic' => $request->input('patronymic'),
                'phone_number' => $request->input('phone_number'),
                'extra_phone_number' => $request->input('extra_phone_number'),
                'email' => $request->input('email'),
                'extra_email' => $request->input('extra_email'),
                'loyalty' => $request->input('loyalty'),
                'note' => $request->input('note'),
            ]);
        } catch (Exception $e) {
        }
        return $affected > 0 ?
            response()->json(['message' => 'success'], Response::HTTP_OK) :
            response()->json(['message' => 'couldn\'t update the contact'], Response::HTTP_BAD_REQUEST);
    }

    public function destroy(Request $request)
    {
        try {
            if ($idContact = $request->input('id')) {
                Contact::query()->where('id', $idContact)->delete();
                return response()->json([
                    'message' => 'success',
                ], Response::HTTP_OK);
            }
        } catch (Exception $e) {
        }
        return response()->json([
            'message' => 'couldn\'t delete the contact',
        ], Response::HTTP_BAD_REQUEST);
    }
}
