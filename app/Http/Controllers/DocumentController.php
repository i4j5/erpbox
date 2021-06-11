<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\DocumentTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use App\Actions\WordTemplate\CreateNewDocument;

class DocumentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (!Auth::user()->can('read document')) {
            abort(403);
        }

        $documents = Document::all();

        return view('documents.index', compact('documents'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (!Auth::user()->can('create document')) {
            abort(403);
        }

        $templates = DocumentTemplate::all();

        return view('documents.create', compact('templates'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (!Auth::user()->can('create document')) {
            abort(403);
        }

        $request->validate([
            'title' => 'required|min:3',
            'template_id' => 'required',
        ]);

        $template_id = $request->template_id;
        $user_id = Auth::user()->id;

        $data = json_decode($request->data, true);
        $data = $data ? $data : [];

        if ( Storage::has("document_templates/$template_id") ) {
            
            $newdocument = new CreateNewDocument(storage_path('app') . "/document_templates/$template_id");
            
            $document = Document::create([
                'title' => $request->title,
                'user_id' => $user_id,
                'template_id' => $template_id
            ]);

            // Создать файл
            $newdocument->execute(storage_path('app') . "/documents/$document->id", $data); 
        }

        return redirect()->route('documents.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Document  $document
     * @return \Illuminate\Http\Response
     */
    public function show(Document $document)
    {
        if (!Auth::user()->can('read document')) {
            abort(403);
        }

        return Storage::download("documents/$document->id", "$document->title #$document->id.docx");
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Document  $document
     * @return \Illuminate\Http\Response
     */
    public function edit(Document $document)
    {
        if (!Auth::user()->can('update document')) {
            abort(403);
        }

        return view('documents.edit', compact('document'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Document  $document
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Document $document)
    {
        if (!Auth::user()->can('update document')) {
            abort(403);
        }

        $document->update($request->validated());

        return redirect()->route('documents.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Document  $document
     * @return \Illuminate\Http\Response
     */
    public function destroy(Document $document)
    {
        if (!Auth::user()->can('delete document')) {
            abort(403);
        }

        Storage::delete("documents/$document->id");

        $document->delete();

        return redirect()->route('documents.index');
    }

    private function num2str($num) {
        $nul = 'ноль';

        $ten = [
            ['','один','два','три','четыре','пять','шесть','семь', 'восемь','девять'],
            ['','одна','две','три','четыре','пять','шесть','семь', 'восемь','девять'],
        ];

        $a20 = [
            'десять',
            'одиннадцать',
            'двенадцать',
            'тринадцать',
            'четырнадцать',
            'пятнадцать',
            'шестнадцать',
            'семнадцать',
            'восемнадцать',
            'девятнадцать'
        ];

        $tens = [
            2 => 'двадцать',
            'тридцать',
            'сорок',
            'пятьдесят',
            'шестьдесят',
            'семьдесят',
            'восемьдесят',
            'девяносто'
        ];

        $hundred =['','сто','двести','триста','четыреста','пятьсот','шестьсот', 'семьсот','восемьсот','девятьсот'];
        
        $unit = [
            ['копейка' ,'копейки' ,'копеек',	 1],
            ['рубль'   ,'рубля'   ,'рублей'    ,0],
            ['тысяча'  ,'тысячи'  ,'тысяч'     ,1],
            ['миллион' ,'миллиона','миллионов' ,0],
            ['миллиард','милиарда','миллиардов',0],
        ];

        list($rub,$kop) = explode('.',sprintf("%015.2f", floatval($num)));

        $out = [];

        if (intval($rub)>0) {
            foreach(str_split($rub,3) as $uk=>$v) { // by 3 symbols
                if (!intval($v)) continue;
                $uk = sizeof($unit)-$uk-1; // unit key
                $gender = $unit[$uk][3];
                list($i1,$i2,$i3) = array_map('intval',str_split($v,1));
                // mega-logic
                $out[] = $hundred[$i1]; # 1xx-9xx
                if ($i2>1) $out[]= $tens[$i2].' '.$ten[$gender][$i3]; # 20-99
                else $out[]= $i2>0 ? $a20[$i3] : $ten[$gender][$i3]; # 10-19 | 1-9
                // units without rub & kop
                if ($uk>1) $out[] = $this->morph($v,$unit[$uk][0],$unit[$uk][1],$unit[$uk][2]);
            }
        }
        else $out[] = $nul;
        $out[] = $this->morph(intval($rub), $unit[1][0],$unit[1][1],$unit[1][2]); // rub
        $out[] = $kop.' '.$this->morph($kop,$unit[0][0],$unit[0][1],$unit[0][2]); // kop
        return trim(preg_replace('/ {2,}/', ' ', join(' ',$out)));
    }

    /**
     * Склоняем словоформу
     */
    private function morph($n, $f1, $f2, $f5) {
        $n = abs(intval($n)) % 100;
        if ($n>10 && $n<20) return $f5;
        $n = $n % 10;
        if ($n>1 && $n<5) return $f2;
        if ($n==1) return $f1;
        return $f5;
    }
}
