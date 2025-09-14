<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Term extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'terms';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'term_id';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'name',
        'slug',
        'term_group',
    ];

    /**
     * Get the term taxonomy relationship.
     */
    public function termTaxonomy()
    {
        return $this->hasOne(TermTaxonomy::class, 'term_id', 'term_id');
    }

    /**
     * Get the term taxonomy for a specific taxonomy.
     */
    public function termTaxonomyFor($taxonomy)
    {
        return $this->hasOne(TermTaxonomy::class, 'term_id', 'term_id')
                    ->where('taxonomy', $taxonomy);
    }
}
