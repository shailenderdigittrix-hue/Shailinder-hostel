<?php

namespace App\Http\Controllers;

use App\Models\EmailTemplate;
use Illuminate\Http\Request;

class EmailTemplateController extends Controller
{
    // Show all templates
    public function index(){
        // Paginate 10 templates per page
        $templates = EmailTemplate::orderBy('created_at', 'desc')->paginate(10);

        return view('emails.templates.list', compact('templates'));
    }


    // Show form to create a new template
    public function create()
    {
        return view('emails.templates.add');
    }

    // Store new template
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:email_templates,name',
            'subject' => 'required',
            'body' => 'required',
        ]);

        $data = $request->all();
        $data['slug'] = Str::slug($request->name);
        EmailTemplate::create($data);
        return redirect()->route('email-templates.index')
                         ->with('success', 'Email template created successfully.');
    }

    // Show a single template
    public function show(EmailTemplate $emailTemplate)
    {
        return view('email_templates.show', compact('emailTemplate'));
    }

    // Show form to edit a template
    public function edit(EmailTemplate $emailTemplate)
    {
        return view('emails.templates.edit', compact('emailTemplate'));
    }

    // Update template
    public function update(Request $request, EmailTemplate $emailTemplate)
    {
        $request->validate([
            'name' => 'required|unique:email_templates,name,' . $emailTemplate->id,
            'subject' => 'required',
            'body' => 'required',
        ]);

        $emailTemplate->update($request->all());

        return redirect()->route('email-templates.index')
                         ->with('success', 'Email template updated successfully.');
    }

    // Delete template
    public function destroy(EmailTemplate $emailTemplate)
    {
        $emailTemplate->delete();
        return redirect()->route('email-templates.index')
                         ->with('success', 'Email template deleted successfully.');
    }
}
