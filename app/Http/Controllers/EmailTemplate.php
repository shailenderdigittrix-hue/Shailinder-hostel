<?php

namespace App\Http\Controllers;

use App\Models\EmailTemplate;
use Illuminate\Http\Request;

class EmailTemplateController extends Controller
{
    /**
     * Display a listing of the email templates.
     */
    public function index()
    {
        $templates = EmailTemplate::all();
        return response()->json([
            'success' => true,
            'data' => $templates
        ]);
    }

    /**
     * Show the form for creating a new email template.
     */
    public function create()
    {
        // For APIs, this may not be necessary unless you return a structure.
        return response()->json([
            'success' => true,
            'message' => 'Display create form structure here if needed.'
        ]);
    }

    /**
     * Store a newly created email template in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:email_templates,name',
            'subject' => 'required|string|max:255',
            'body' => 'required|string',
        ]);

        $template = EmailTemplate::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Email template created successfully.',
            'data' => $template
        ], 201);
    }

    /**
     * Display the specified email template.
     */
    public function show(string $id)
    {
        $template = EmailTemplate::find($id);

        if (!$template) {
            return response()->json([
                'success' => false,
                'message' => 'Email template not found.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $template
        ]);
    }

    /**
     * Show the form for editing the specified email template.
     */
    public function edit(string $id)
    {
        $template = EmailTemplate::find($id);

        if (!$template) {
            return response()->json([
                'success' => false,
                'message' => 'Email template not found.'
            ], 404);
        }

        // Similar to `create()`, return structure or data for editing
        return response()->json([
            'success' => true,
            'data' => $template
        ]);
    }

    /**
     * Update the specified email template in storage.
     */
    public function update(Request $request, string $id)
    {
        $template = EmailTemplate::find($id);

        if (!$template) {
            return response()->json([
                'success' => false,
                'message' => 'Email template not found.'
            ], 404);
        }

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255|unique:email_templates,name,' . $template->id,
            'subject' => 'sometimes|required|string|max:255',
            'body' => 'sometimes|required|string',
        ]);

        $template->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Email template updated successfully.',
            'data' => $template
        ]);
    }

    /**
     * Remove the specified email template from storage.
     */
    public function destroy(string $id)
    {
        $template = EmailTemplate::find($id);

        if (!$template) {
            return response()->json([
                'success' => false,
                'message' => 'Email template not found.'
            ], 404);
        }

        $template->delete();

        return response()->json([
            'success' => true,
            'message' => 'Email template deleted successfully.'
        ]);
    }
}
