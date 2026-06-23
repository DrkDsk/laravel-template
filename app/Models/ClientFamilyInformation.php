<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClientFamilyInformation extends Model
{
    protected $table = 'client_family_information';

    protected $fillable = [
        'client_id',
        'has_spouse',
        'minor_or_student_children_count',
        'parents_count',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'has_spouse' => 'boolean',
            'minor_or_student_children_count' => 'integer',
            'parents_count' => 'integer',
        ];
    }

    /**
     * @return BelongsTo<Client, $this>
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }
}
