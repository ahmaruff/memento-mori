<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

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
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $httpcode = 200;
        $status = 'success';
        $data = '';

        $desa = \Laravolt\Indonesia\Facade::findVillage($id);

        if($desa == null) {
            $status = 'fail';
            $data = 'desa with id: '.$id.' is not found';
            $httpcode = 404;
        } else {
            $data = $desa;
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
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
