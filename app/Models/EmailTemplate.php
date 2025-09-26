<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EmailTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'name',
        'subject',
        'body_html',
        'body_text',
        'variables',
        'is_active',
    ];

    protected $casts = [
        'variables' => 'array',
        'is_active' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function getAvailableVariables()
    {
        return $this->variables ?? [];
    }

    public function renderSubject($data = [])
    {
        return $this->renderTemplate($this->subject, $data);
    }

    public function renderBody($data = [])
    {
        return $this->renderTemplate($this->body_html, $data);
    }

    private function renderTemplate($template, $data)
    {
        foreach ($data as $key => $value) {
            $template = str_replace("{{$key}}", $value, $template);
        }
        return $template;
    }
}
