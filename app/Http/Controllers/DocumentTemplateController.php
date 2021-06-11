<?php

namespace App\Http\Controllers;

use App\Models\DocumentTemplate;
use App\Models\DocumentTemplateVariable;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class DocumentTemplateController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (!Auth::user()->can('read document template')) {
            abort(403);
        }

        $documentTemplates = DocumentTemplate::all();

        return view('document_templates.index', compact('documentTemplates'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (!Auth::user()->can('create document template')) {
            abort(403);
        }

        $variables = DocumentTemplateVariable::all();

        return view('document_templates.create', compact('variables'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (!Auth::user()->can('create document template')) {
            abort(403);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|min:3',
            'file-upload' => 'required|max:204000|mimes:docx',
        ]);

        $validator->validate();

        $file = $request->file('file-upload');

        $ext =$file->getClientOriginalExtension();

        $user_id = Auth::user()->id;
        $file_name = md5(microtime() . rand(0, 9999)) . '.' . $ext;

        $path = Storage::putFileAs('document_templates', $file, $file_name);

        $documentTemplate = DocumentTemplate::create([
            'name' => $request->name,
            'description' => $request->description,
            'user_id' => $user_id,
            //'file_name' => $file_name 
        ]);

        $user_id = Auth::user()->id;

        Storage::move($path, "document_templates/$documentTemplate->id");

        $variables = DocumentTemplateVariable::find($request->variables); 

        $documentTemplate->variables()->attach($variables);

        return redirect()->route('document-templates.index');

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\DocumentTemplate  $documentTemplate
     * @return \Illuminate\Http\Response
     */
    public function show(DocumentTemplate $documentTemplate)
    {
        if (!Auth::user()->can('read document template')) {
            abort(403);
        }
        
        return Storage::download("document_templates/$documentTemplate->id", "$documentTemplate->name #$documentTemplate->id.docx");
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\DocumentTemplate  $documentTemplate
     * @return \Illuminate\Http\Response
     */
    public function edit(DocumentTemplate $documentTemplate)
    {
        if (!Auth::user()->can('update document template')) {
            abort(403);
        }

        $variables = [];
        foreach ($documentTemplate->variables()->get() as $item) {
            $variables[] = $item->id;
        }

        $allVariables = DocumentTemplateVariable::all();

        return view('document_templates.edit', compact('documentTemplate', 'variables', 'allVariables'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\DocumentTemplate  $documentTemplate
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, DocumentTemplate $documentTemplate)
    {
        if (!Auth::user()->can('update document template')) {
            abort(403);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|min:3',
            'file-upload' => 'max:204000|mimes:docx',
        ]);

        $validator->validate();

        $documentTemplate->name = $request->name;
        $documentTemplate->description = $request->description;
        $documentTemplate->save();

        $variables = DocumentTemplateVariable::find($request->variables); 
        $documentTemplate->variables()->detach();
        $documentTemplate->variables()->attach($variables);

        $file = $request->file('file-upload');

        if ($file){
            $user_id = Auth::user()->id;
            $file_name = md5(microtime() . rand(0, 9999));

            Storage::delete("document_templates/$documentTemplate->id.docx");

            Storage::putFileAs('document_templates', $file, $documentTemplate->id);
        }

        return redirect()->route('document-templates.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\DocumentTemplate  $documentTemplate
     * @return \Illuminate\Http\Response
     */
    public function destroy(DocumentTemplate $documentTemplate)
    {
        if (!Auth::user()->can('delete document template')) {
            abort(403);
        }

        Storage::delete("document_templates/$documentTemplate->id.docx");

        $documentTemplate->variables()->detach();
        $documentTemplate->delete();

        return redirect()->route('document-templates.index');
    }
}
