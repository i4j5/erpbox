<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\DocumentTemplate;

class DocumentTemplateVariable extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'name',
        'description',
        'type_id',
        'default'
    ];

    public function templates()
    {
        return $this->belongsToMany(DocumentTemplate::class);
    }
}
