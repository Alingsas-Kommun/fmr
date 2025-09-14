<?php

namespace App\View\Composers;

use App\Models\Post;
use Roots\Acorn\View\Composer;
use Illuminate\Support\Str;

class Person extends Composer
{
    /**
     * List of views served by this composer.
     *
     * @var array
     */
    protected static $views = [
        'partials.post-types.content-single-person',
        'partials.post-types.content-person',
    ];

    /**
     * List of meta fields to be passed via the person object.
     *
     * @var array
     */
    protected static $metaFields = [
        'person_firstname',
        'person_lastname',
        'person_birth_date',
        'person_ssn',
        'person_kilometers',
        'person_group_leader',
        'person_listing',
        'person_home_visiting_address',
        'person_home_address',
        'person_home_zip',
        'person_home_city',
        'person_home_webpage',
        'person_home_email',
        'person_home_phone',
        'person_home_mobile',
        'person_work_visiting_address',
        'person_work_address',
        'person_work_zip',
        'person_work_city',
        'person_work_webpage',
        'person_work_email',
        'person_work_phone',
        'person_work_mobile',
        
    ];

    /**
     * Data to be passed to view before rendering.
     *
     * @return array
     */
    public function with()
    {
        return [
            'person' => $this->personWithMeta(),
            'party' => $this->party(),
            'assignments' => $this->assignments(),
            'thumbnail' => $this->thumbnail(),
        ];
    }

    /**
     * Retrieve the person object.
     */
    public function person()
    {
        $personId = get_the_ID(); // @phpstan-ignore-line
        
        if (!$personId) {
            return null;
        }

        return Post::find($personId);
    }

    /**
     * Get the party associated with this person.
     */
    public function party()
    {
        $person = $this->person();
        
        if (!$person) {
            return null;
        }

        $partyId = $person->getMeta('person_party');
        
        if (!$partyId) {
            return null;
        }

        $party = Post::find($partyId);
        
        if (!$party || $party->post_type !== 'party') {
            return null;
        }

        return $party;
    }

    /**
     * Retrieve the person object with aggregated meta fields.
     */
    public function personWithMeta()
    {
        $person = $this->person();
        
        if (!$person) {
            return null;
        }

        $metaValues = $this->personMeta();
        
        foreach ($metaValues as $key => $value) {
            $propertyName = Str::camel(Str::replace('person_', '', $key)); 
            $person->$propertyName = $value;
        }

        return $person;
    }

    /**
     * Get active assignments for the person.
     */
    public function assignments()
    {
        $person = $this->person();
        
        if (!$person) {
            return collect();
        }

        return $person->activeAssignments;
    }

    /**
     * Get the thumbnail for the person.
     */
    public function thumbnail()
    {
        $person = $this->person();
        
        if (!$person) {
            return null;
        }

        return $person->thumbnail();
    }

    /**
     * Get all visible person meta fields in a single query.
     */
    public function personMeta()
    {
        $person = $this->person();
        
        if (!$person) {
            return [];
        }

        return $person->getVisibleMetaValues(static::$metaFields);
    }
}