<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repository\interfaces\BloodBankRepositoryInterface;

class BloodBankController extends Controller
{
    private $bloodBankRepo;

    /**
     * Inject the BloodBankRepositoryInterface.
     */
    public function __construct(BloodBankRepositoryInterface $bloodBankRepo)
    {
        $this->bloodBankRepo = $bloodBankRepo;
        $this->middleware('auth:admin');
    }

    /**
     * GET: Show the current admin's blood bank.
     */
    public function show()
    {
        try {
            $bloodBank = $this->bloodBankRepo->getCurrentAdminBank();
            return response()->json([
                'success' => true,
                'data' => $bloodBank
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * GET: Show the update stock form (return current stock as JSON).
     */
    public function updateStockForm()
    {
        try {
            $bloodBank = $this->bloodBankRepo->getCurrentAdminBank();
            return response()->json([
                'success' => true,
                'data' => $bloodBank
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * POST: Update blood stock (RESTful).
     */
    public function updateStock(Request $request)
    {
        $validated = $request->validate([
            'operation' => 'required|in:add,remove',
            'blood_type' => 'required|in:A+,A-,B+,B-,AB+,AB-,O+,O-',
            'quantity' => 'required|integer|min:1'
        ]);

        try {
            $quantity = $validated['operation'] === 'add' 
                ? $validated['quantity'] 
                : -$validated['quantity'];

            $success = $this->bloodBankRepo->updateStock($validated['blood_type'], $quantity);

            if (!$success) {
                return response()->json([
                    'success' => false,
                    'message' => 'Not enough blood to deduct.'
                ], 400);
            }

            return response()->json([
                'success' => true,
                'message' => 'Blood stock updated successfully.'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update stock. ' . $e->getMessage()
            ], 400);
        }
    }
}
