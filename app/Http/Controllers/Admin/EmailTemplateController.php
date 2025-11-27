<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmailTemplate;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EmailTemplateController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $query = EmailTemplate::latest();

        // Filter by type
        if ($request->has('type') && $request->type !== 'all') {
            $query->where('type', $request->type);
        }

        // Filter by status
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('is_active', $request->status === 'active');
        }

        // Search
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('subject', 'like', "%{$search}%");
            });
        }

        $templates = $query->paginate(20)->withQueryString();

        return view('admin.email-templates.index', compact('templates'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('admin.email-templates.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:email_templates,name'],
            'subject' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string'],
            'variables' => ['nullable'],
            'variables_string' => ['nullable', 'string'],
            'type' => ['required', 'string', 'in:email,notification,sms'],
            'is_active' => ['boolean'],
        ]);

        // Handle variables from string or array
        if ($request->has('variables_string') && $request->variables_string) {
            $variables = array_filter(array_map('trim', explode(',', $request->variables_string)));
            $validated['variables'] = $variables;
        } elseif ($request->has('variables') && is_string($request->variables)) {
            $validated['variables'] = json_decode($request->variables, true);
        }

        unset($validated['variables_string']);

        EmailTemplate::create($validated);

        return redirect()->route('admin.email-templates.index')
            ->with('success', 'Template email berhasil dibuat.');
    }

    /**
     * Display the specified resource.
     */
    public function show(EmailTemplate $emailTemplate): View
    {
        return view('admin.email-templates.show', compact('emailTemplate'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(EmailTemplate $emailTemplate): View
    {
        return view('admin.email-templates.edit', compact('emailTemplate'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, EmailTemplate $emailTemplate): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:email_templates,name,' . $emailTemplate->id],
            'subject' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string'],
            'variables' => ['nullable'],
            'variables_string' => ['nullable', 'string'],
            'type' => ['required', 'string', 'in:email,notification,sms'],
            'is_active' => ['boolean'],
        ]);

        // Handle variables from string or array
        if ($request->has('variables_string') && $request->variables_string) {
            $variables = array_filter(array_map('trim', explode(',', $request->variables_string)));
            $validated['variables'] = $variables;
        } elseif ($request->has('variables') && is_string($request->variables)) {
            $validated['variables'] = json_decode($request->variables, true);
        }

        unset($validated['variables_string']);

        $emailTemplate->update($validated);

        return redirect()->route('admin.email-templates.index')
            ->with('success', 'Template email berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(EmailTemplate $emailTemplate): RedirectResponse
    {
        $emailTemplate->delete();

        return redirect()->route('admin.email-templates.index')
            ->with('success', 'Template email berhasil dihapus.');
    }

    /**
     * Preview template with sample data.
     */
    public function preview(EmailTemplate $emailTemplate): View
    {
        // Generate sample variables
        $sampleVariables = [];
        if ($emailTemplate->variables) {
            foreach ($emailTemplate->variables as $var) {
                $sampleVariables[$var] = "Sample {$var}";
            }
        }

        $rendered = $emailTemplate->render($sampleVariables);

        return view('admin.email-templates.preview', compact('emailTemplate', 'rendered', 'sampleVariables'));
    }
}
