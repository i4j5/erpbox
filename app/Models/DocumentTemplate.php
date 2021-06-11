<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\DocumentTemplateVariable;

class DocumentTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'user_id'
    ];

    public function variables()
    {
        return $this->belongsToMany(DocumentTemplateVariable::class);
    }
}
