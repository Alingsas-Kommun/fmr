<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TermTaxonomy extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'term_taxonomy';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'term_taxonomy_id';

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
        'term_id',
        'taxonomy',
        'description',
        'parent',
        'count',
    ];

    /**
     * Get the term associated with this taxonomy.
     */
    public function term()
    {
        return $this->belongsTo(Term::class, 'term_id', 'term_id');
    }

    /**
     * Get the parent term taxonomy.
     */
    public function parent()
    {
        return $this->belongsTo(TermTaxonomy::class, 'parent', 'term_taxonomy_id');
    }

    /**
     * Get the child term taxonomies.
     */
    public function children()
    {
        return $this->hasMany(TermTaxonomy::class, 'parent', 'term_taxonomy_id');
    }
}
