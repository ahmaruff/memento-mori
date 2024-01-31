<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\StoreDesaRequest;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;


// this JSON response following the JSend standard https://github.com/omniti-labs/jsend
// with additional http status code following https://api.stackexchange.com/docs/error-handling

class DesaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $desa = \Laravolt\Indonesia\Facade::paginateVillages($numRows = 15);

            $res = [
                'status' => 'success',
                'code' => Response::HTTP_OK,
                'data' => [
                    'desa' => $desa,
                ],
            ];

            return response()->json($res,Response::HTTP_OK);

        } catch (\Throwable $th) {
            $res = [
                'status' => 'error',
                'code' => Response::HTTP_BAD_REQUEST,
                'message' => $th->getMessage()
            ];

            return response()->json($res,Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if(!$request->isJson()){
            $res = [
                'status' => 'error',
                'code' => Response::HTTP_BAD_REQUEST,
                'message' => 'Request body is not JSON'
            ];

            return response()->json($res,Response::HTTP_BAD_REQUEST);
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
            $res = [
                'status' => 'fail',
                'code' => Response::HTTP_BAD_REQUEST,
                'data' => [
                    'validation' => $validator->errors(),
                ],
            ];

            return response()->json($res,Response::HTTP_BAD_REQUEST);
        }


        $validated = $validator->validated();
        $meta = [
            'lat' => $validated['lat'],
            'long' => $validated['long'],
            'pos' => $validated['pos'],
        ];

        try {
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
                    'code' => Response::HTTP_CREATED,
                    'data' => [
                        'desa' => $village,
                    ],
                ];

                return response()->json($res, Response::HTTP_CREATED);
            }else {
                $res = [
                    'status' => 'fail',
                    'code' => Response::HTTP_BAD_REQUEST,
                    'data' => [
                        'desa' => 'Failed to save data',
                    ],
                ];

                return response()->json($res, Response::HTTP_BAD_REQUEST);
            }
        } catch (\Throwable $th) {
            $res = [
                'status' => 'error',
                'code' => Response::HTTP_BAD_REQUEST,
                'message' => $th->getMessage()
            ];

            return response()->json($res,Response::HTTP_BAD_REQUEST);
        }


    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $desa = \Laravolt\Indonesia\Facade::findVillage($id);

            if($desa != null) {
                $res = [
                    'status' => 'success',
                    'code' => Response::HTTP_OK,
                    'data' => [
                        'desa' => $desa,
                    ],
                ];

                return response()->json($res,Response::HTTP_OK);
            } else {
                $res = [
                    'status' => 'fail',
                    'code' => Response::HTTP_NOT_FOUND,
                    'data' => [
                        'desa' => 'Desa not found!'
                    ],
                ];

                return response()->json($res, Response::HTTP_NOT_FOUND);
            }

        } catch (\Throwable $th) {
            $res = [
                'status' => 'error',
                'code' => Response::HTTP_BAD_REQUEST,
                'message' => $th->getMessage()
            ];

            return response()->json($res,Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        if(!$request->isJson()){
            $res = [
                'status' => 'error',
                'code' => Response::HTTP_BAD_REQUEST,
                'message' => 'request body is not JSON'
            ];

            return response()->json($res,Response::HTTP_BAD_REQUEST);
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
            $res = [
                'status' => 'fail',
                'code' => Response::HTTP_BAD_REQUEST,
                'data' => [
                    'validation' => $validator->errors(),
                ],
            ];

            return response()->json($res,Response::HTTP_BAD_REQUEST);
        }

        $validated = $validator->validated();

        $meta = [
            'lat' => $validated['lat'],
            'long' => $validated['long'],
            'pos' => $validated['pos'],
        ];

        try {
            $village = \Laravolt\Indonesia\Models\Village::find($id);

            if($village != null){
                $village->fill($validator->safe()->except(['lat', 'long', 'pos']));
                $village->meta = json_encode($meta);
                $village->created_at = Carbon::now();
                $village->updated_at = Carbon::now();

                if($village->update()){
                    $res = [
                        'status' => 'success',
                        'code' => Response::HTTP_OK,
                        'data' => [
                            'desa' => $village,
                        ],
                    ];

                    return response()->json($res,Response::HTTP_OK);
                }else {
                    $res = [
                        'status' => 'fail',
                        'code' => Response::HTTP_BAD_REQUEST,
                        'data' => [
                            'desa' => 'Failed to save data',
                        ],
                    ];

                    return response()->json($res, Response::HTTP_BAD_REQUEST);
                }
            }else {
                $res = [
                    'status' => 'fail',
                    'code' => Response::HTTP_NOT_FOUND,
                    'data' => [
                        'desa' => 'Desa not found!'
                    ],
                ];

                return response()->json($res, Response::HTTP_NOT_FOUND);
            }
        } catch (\Throwable $th) {
            $res = [
                'status' => 'error',
                'code' => Response::HTTP_BAD_REQUEST,
                'message' => $th->getMessage()
            ];

            return response()->json($res,Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {

        try {
            $village = \Laravolt\Indonesia\Models\Village::find($id);

            if($village != null) {
                try {
                    $village->delete();
                    $res = [
                        'status' => 'success',
                        'code' => Response::HTTP_OK,
                        'data' => [
                            'desa' => ''
                        ],
                    ];

                    return response()->json($res, Response::HTTP_OK);
                } catch (\Throwable $th) {
                    $res = [
                        'status' => 'error',
                        'code' => Response::HTTP_BAD_REQUEST,
                        'message' => $th->getMessage()
                    ];

                    return response()->json($res,Response::HTTP_BAD_REQUEST);
                }
            }else{
                $res = [
                    'status' => 'fail',
                    'code' => Response::HTTP_NOT_FOUND,
                    'data' => [
                        'desa' => 'Desa not found!'
                    ],
                ];

                return response()->json($res, Response::HTTP_NOT_FOUND);
            }
        } catch (\Throwable $th) {
            $res = [
                'status' => 'error',
                'code' => Response::HTTP_BAD_REQUEST,
                'message' => $th->getMessage()
            ];

            return response()->json($res,Response::HTTP_BAD_REQUEST);
        }
    }
}
