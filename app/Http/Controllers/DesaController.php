<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\StoreDesaRequest;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;


// this JSON response following the JSend standard https://github.com/omniti-labs/jsend
// with additional http status code following https://api.stackexchange.com/docs/error-handling

class DesaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $desa = \Laravolt\Indonesia\Facade::paginateVillages($numRows = 15);

        $res = [
            'status' => 'success',
            'data' => [
                'desa' => $desa,
            ],
        ];

        return response()->json($res,200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if(!$request->isJson()){
            $res = [
                'status' => 'error',
                'message' => 'request body is not JSON'
            ];

            return response()->json($res,400);
        }

        $villages_table = config('laravolt.indonesia.table_prefix').'villages';

        $rules = [
            'code'          => ['required', 'string', 'unique:'.$villages_table, 'size:10'],
            'district_code' => ['required', 'string', 'max:7'],
            'name'          => ['required', 'string', 'max:255'],
            'lat'           => ['nullable', 'string'],
            'long'          => ['nullable', 'string'],
            'pos'           => ['nullable', 'string', 'size:5'],
        ];

        $validator = Validator::make($request->all(), $rules);

        if($validator->fails()){
            return response()->json($validator->errors(),400);
        }
        $validated = $validator->validated();

        $meta = [
            'lat' => $validated['lat'],
            'long' => $validated['long'],
            'pos' => $validated['pos'],
        ];

        $village = new \Laravolt\Indonesia\Models\Village;
        $village->code = $validated['code'];
        $village->district_code = $validated['district_code'];
        $village->name = $validated['name'];
        $village->meta = json_encode($meta);
        $village->created_at = Carbon::now();
        $village->updated_at = Carbon::now();

        if($village->save()){
            $res = [
                'status' => 'success',
                'data' => [
                    'desa' => $village,
                ],
            ];

            return response()->json($res, 201);
        }else {
            $res = [
                'status' => 'fail',
                'data' => [
                    'desa' => 'Failed to save data',
                ],
            ];

            return response()->json($res, 400);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $httpcode = 200;
        $status = 'success';

        $data = \Laravolt\Indonesia\Facade::findVillage($id);

        if($data == null) {
            $status = 'fail';
            $data = 'desa with id: '.$id.' is not found';
            $httpcode = 404;
        }

        $res = [
            'status' => $status,
            'data' => [
                'desa' => $data,
            ],
        ];

        return response()->json($res,$httpcode);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        if(!$request->isJson()){
            $res = [
                'status' => 'error',
                'message' => 'request body is not JSON'
            ];

            return response()->json($res,400);
        }

        $rules = [
            'code'          => ['nullable', 'string', 'size:10'],
            'district_code' => ['nullable', 'string', 'max:7'],
            'name'          => ['nullable', 'string', 'max:255'],
            'lat'           => ['nullable', 'string'],
            'long'          => ['nullable', 'string'],
            'pos'           => ['nullable', 'string', 'size:5'],
        ];

        $validator = Validator::make($request->all(), $rules);

        if($validator->fails()){
            return response()->json($validator->errors(),400);
        }
        $validated = $validator->validated();

        $meta = [
            'lat' => $validated['lat'],
            'long' => $validated['long'],
            'pos' => $validated['pos'],
        ];

        $village = \Laravolt\Indonesia\Models\Village::find($id);

        if($village == null) {
            $res = [
                'status' => 'fail',
                'data' => [
                    'desa' => 'Desa Not Found',
                ],
            ];

            return response()->json($res, 404);
        }

        $village->fill($validator->safe()->except(['lat', 'long', 'pos']));
        $village->meta = json_encode($meta);
        $village->created_at = Carbon::now();
        $village->updated_at = Carbon::now();

        if($village->update()){
            $res = [
                'status' => 'success',
                'data' => [
                    'desa' => $village,
                ],
            ];

            return response()->json($res, 200);
        }else {
            $res = [
                'status' => 'fail',
                'data' => [
                    'desa' => 'Failed to save data',
                ],
            ];

            return response()->json($res, 400);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
