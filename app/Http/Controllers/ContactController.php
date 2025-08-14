<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repository\interfaces\ContactRepositoryInterface;

class ContactController extends Controller
{
    protected $contactRepo;
    public function __construct(ContactRepositoryInterface $contactRepo)
    {
        $this->contactRepo = $contactRepo;
    }

    /**
     * POST: Submit contact form (RESTful).
     */
    public function submitForm(Request $request)
    {
        // Validate the form data
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'message' => 'required|string',
        ]);

        try {
            // Store the contact message using repository
            $this->contactRepo->create($validated);

            // Return JSON response (RESTful)
            return response()->json([
                'success' => true,
                'message' => 'Your message has been saved successfully!',
            ]);
        } catch (\Exception $e) {
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Something went wrong. Please try again.',
                ],
                500,
            );
        }
    }
}
