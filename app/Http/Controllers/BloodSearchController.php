<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Repository\interfaces\BloodSearchRepositoryInterface;
use App\Http\Request\BloodSearchRequest;

class BloodSearchController extends Controller
{
    public function __construct(protected BloodSearchRepositoryInterface $bloodSearchRepo)
    {
    }

    /**
     * GET /api/blood-requests
     * List all blood requests of the authenticated user
     */
    public function index()
    {
        $userId = Auth::id();
        $requests = $this->bloodSearchRepo->getUserBloodRequests($userId);

        return response()->json([
            'success' => true,
            'data' => $requests
        ]);
    }

    /**
     * POST /api/blood-requests
     * Create a new blood request
     */
    public function store(BloodSearchRequest $request)
    {
        $user = Auth::user();

        //$validated = $this->bloodSearchRepo->validateBloodRequest($request->all());
        $validated= $request->validatedData();
        if (!$validated['success']) {
            return response()->json(['errors' => $validated['errors']], 422);
        }

        $data = $validated['data'];
        $data['user_id'] = $user->id;
        $data['user_name'] = $user->name;
        $data['email'] = $user->email;
        $data['phone'] = $user->phone;

        if ($request->hasFile('request_form')) {
            $data['request_form'] = $this->bloodSearchRepo->storeRequestForm($request->file('request_form'));
        }

        $bloodRequest = $this->bloodSearchRepo->createBloodRequest($data);

        return response()->json([
            'success' => true,
            'message' => 'Blood request created successfully.',
            'data' => $bloodRequest
        ], 201);
    }

    /**
     * GET /api/blood-requests/{id}
     * Show a single blood request
     */
    public function show($id)
    {
        $userId = Auth::id();
        $bloodRequest = $this->bloodSearchRepo->getUserBloodRequestById($userId, $id);

        if (!$bloodRequest) {
            return response()->json([
                'success' => false,
                'message' => 'Blood request not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $bloodRequest
        ]);
    }

    /**
     * PATCH /api/blood-requests/{id}
     * Update an existing blood request
     */
    public function update(Request $request, $id)
    {
        $userId = Auth::id();

        $bloodRequest = $this->bloodSearchRepo->getUserBloodRequestById($userId, $id);
        if (!$bloodRequest) {
            return response()->json([
                'success' => false,
                'message' => 'Blood request not found'
            ], 404);
        }

        if (!$bloodRequest->canEdit()) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot edit this request.'
            ], 403);
        }

        $validated = $this->bloodSearchRepo->validateBloodRequest($request->all(), $id);

        if (!$validated['success']) {
            return response()->json(['errors' => $validated['errors']], 422);
        }

        $data = $validated['data'];

        if ($request->hasFile('request_form')) {
            $data['request_form'] = $this->bloodSearchRepo->updateRequestForm($bloodRequest, $request->file('request_form'));
        }

        $updatedRequest = $this->bloodSearchRepo->updateBloodRequest($id, $data);

        return response()->json([
            'success' => true,
            'message' => 'Blood request updated successfully',
            'data' => $updatedRequest
        ]);
    }

    /**
     * DELETE /api/blood-requests/{id}
     * Delete a blood request
     */
    public function destroy($id)
    {
        $userId = Auth::id();

        $bloodRequest = $this->bloodSearchRepo->getUserBloodRequestById($userId, $id);
        if (!$bloodRequest) {
            return response()->json([
                'success' => false,
                'message' => 'Blood request not found'
            ], 404);
        }

        if (!$bloodRequest->canEdit()) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete this request.'
            ], 403);
        }

        $this->bloodSearchRepo->deleteBloodRequest($bloodRequest);

        return response()->json([
            'success' => true,
            'message' => 'Blood request deleted successfully'
        ]);
    }

    /**
     * POST /api/blood-requests/{id}/payment
     * Process payment for a blood request
     */
    public function processPayment(Request $request, $id)
    {
        $userId = Auth::id();
        $bloodRequest = $this->bloodSearchRepo->getUserBloodRequestById($userId, $id);

        if (!$bloodRequest) {
            return response()->json([
                'success' => false,
                'message' => 'Blood request not found'
            ], 404);
        }

        $validated = $request->validate([
            'payment' => 'required|numeric|min:0'
        ]);

        $updatedRequest = $this->bloodSearchRepo->processPayment($bloodRequest, $validated['payment']);

        return response()->json([
            'success' => true,
            'message' => 'Payment processed successfully',
            'data' => $updatedRequest
        ]);
    }
}
