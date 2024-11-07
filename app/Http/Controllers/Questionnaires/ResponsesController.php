<?php

namespace App\Http\Controllers\Questionnaires;

use App\Models\Response;
use Illuminate\Http\Request;

class ResponsesController extends Controller
{
    public function index()
    {
        return Response::with(['questionnaire', 'user'])->get();
    }

    public function store(Request $request)
    {
        $request->validate([
            'questionnaire_id' => 'required|exists:questionnaires,id',
            'user_id' => 'required|exists:users,id'
        ]);
        return Response::create($request->all());
    }

    public function show($id)
    {
        return Response::with(['questionnaire', 'user'])->findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $response = Response::findOrFail($id);
        $response->update($request->all());
        return $response;
    }

    public function destroy($id)
    {
        Response::destroy($id);
        return response()->noContent();
    }
}
