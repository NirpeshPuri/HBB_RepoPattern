<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Repository\interfaces\BloodSearchRepositoryInterface;

class BloodSearchController extends Controller
{
    protected BloodSearchRepositoryInterface $repo;

    public function __construct(BloodSearchRepositoryInterface $repo)
    {
        $this->repo = $repo;
    }

    // GET /blood-requests
    public function index()
    {
        $userId = Auth::id();
        $requests = $this->repo->getUserBloodRequests($userId);

        return response()->json([
            'success' => true,
            'data' => $requests,
        ]);
    }

    // GET /blood-requests/{id}
    public function show($id)
    {
        $request = $this->repo->getUserBloodRequestById(Auth::id(), $id);

        if (!$request) {
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Not found',
                ],
                404,
            );
        }

        return response()->json([
            'success' => true,
            'data' => $request,
        ]);
    }

    // POST /blood-requests
    public function store(Request $request)
    {
        $user = Auth::user();

        // Validate request using repository
        $validated = $this->repo->validateBloodRequest($request->all());
        if (!$validated['success']) {
            return response()->json(
                [
                    'success' => false,
                    'errors' => $validated['errors'],
                ],
                422,
            );
        }

        $data = $validated['data'];
        $data['user_id'] = $user->id;
        $data['user_name'] = $user->name;
        $data['email'] = $user->email;
        $data['phone'] = $user->phone;

        // Handle file upload safely
        if ($request->hasFile('request_form')) {
            try {
                $data['request_form'] = $this->repo->storeRequestForm($request->file('request_form'));
            } catch (\Exception $e) {
                return response()->json(
                    [
                        'success' => false,
                        'message' => 'File upload failed',
                        'error' => $e->getMessage(),
                    ],
                    500,
                );
            }
        }

        // Create the blood request
        try {
            $bloodRequest = $this->repo->createBloodRequest($data);
        } catch (\Exception $e) {
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Failed to create blood request',
                    'error' => $e->getMessage(),
                ],
                500,
            );
        }

        return response()->json(
            [
                'success' => true,
                'message' => 'Blood request submitted successfully',
                'data' => $bloodRequest,
            ],
            201,
        );
    }

    // PUT /blood-requests/{id}
    public function update(Request $request, $id)
    {
        $validated = $this->repo->validateBloodRequest($request->all(), true);
        if (!$validated['success']) {
            return response()->json(
                [
                    'success' => false,
                    'errors' => $validated['errors'],
                ],
                422,
            );
        }

        $updatedRequest = $this->repo->updateBloodRequest($id, $validated['data']);
        if (!$updatedRequest) {
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Not found',
                ],
                404,
            );
        }

        return response()->json([
            'success' => true,
            'data' => $updatedRequest,
        ]);
    }

    // DELETE /blood-requests/{id}
    public function destroy($id)
    {
        if (!$this->repo->deleteBloodRequest($id)) {
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Not found',
                ],
                404,
            );
        }

        return response()->json([
            'success' => true,
            'message' => 'Deleted successfully',
        ]);
    }

    // POST /blood-requests/nearby-admins
    public function nearbyAdmins(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        $admins = $this->repo->findNearbyAdmins($request->latitude, $request->longitude);

        return response()->json([
            'success' => true,
            'data' => $admins,
        ]);
    }
}
