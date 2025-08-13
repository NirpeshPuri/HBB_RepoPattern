<?php

namespace App\Http\Controllers;

use App\Repository\interfaces\ReceiverStatusRepositoryInterface;
use Illuminate\Http\Request;

class ReceiverStatusController extends Controller
{
    protected $repo;

    public function __construct(ReceiverStatusRepositoryInterface $repo)
    {
        $this->repo = $repo;
    }

    public function index()
    {
        $requests = $this->repo->index();
        return response()->json($requests);
    }

    public function edit($id)
    {
        $data = $this->repo->edit($id);
        if (!$data) {
            return response()->json(['success' => false, 'message' => 'Cannot edit this request.'], 403);
        }
        return response()->json($data);
    }

    public function update(Request $request, $id)
    {
        $result = $this->repo->update($request, $id);
        return response()->json($result);
    }

    public function destroy($id)
    {
        $result = $this->repo->destroy($id);
        return response()->json($result);
    }
}
