<?php

namespace App\Services;

use App\Exceptions\DemoContentException;
use App\Models\Assignment;
use App\Models\DecisionAuthority;
use Faker\Factory;
use Faker\Generator;
use Throwable;
use WP_Term;

class DemoContentService
{
    public const MANIFEST_OPTION = 'fmr_demo_manifest';

    public const DEMO_META_KEY = '_fmr_demo';

    public const FAKER_SEED = 2026;

    /**
     * IDs created during the current seed run.
     *
     * @var array{posts: array<int>, terms: array<int>, decision_authorities: array<int>, assignments: array<int>}
     */
    private array $created = [
        'posts' => [],
        'terms' => [],
        'decision_authorities' => [],
        'assignments' => [],
    ];

    /**
     * @var array{persons: int, parties: int, boards: int}
     */
    private array $counts = [
        'persons' => 0,
        'parties' => 0,
        'boards' => 0,
    ];

    private ?int $authorId = null;

    private bool $renamedSite = false;

    private mixed $previousBlogname = null;

    private mixed $previousBlogdescription = null;

    /**
     * People grouped by party short code and cohort.
     *
     * @var array<string, array{current: array<int, array<string, mixed>>, previous: array<int, array<string, mixed>>}>
     */
    private array $pools = [];

    /**
     * @var array<int, array<string, mixed>>
     */
    private array $persons = [];

    /**
     * @var array<string, int>
     */
    private array $roleIds = [];

    /**
     * @var array<string, array<string, int>>
     */
    private array $authorities = [];

    /**
     * @var array<int, true>
     */
    private array $visibleEmailIds = [];

    /**
     * Create the FMR Demo register.
     *
     * @return array{persons: int, parties: int, boards: int, decision_authorities: int, assignments: int}
     */
    public function seed(bool $force = false): array
    {
        $this->assertCanSeed($force);
        $this->resetRun();
        $this->authorId = $this->resolveAuthorId();

        try {
            $catalog = $this->catalog();
            $this->assertCatalog($catalog);
            $this->seedRegister($catalog);
            $this->storeManifest($catalog);

            return [
                'persons' => $this->counts['persons'],
                'parties' => $this->counts['parties'],
                'boards' => $this->counts['boards'],
                'decision_authorities' => count($this->created['decision_authorities']),
                'assignments' => count($this->created['assignments']),
            ];
        } catch (Throwable $exception) {
            $this->rollback();

            if ($exception instanceof DemoContentException) {
                throw $exception;
            }

            throw new DemoContentException($exception->getMessage(), 0, $exception);
        }
    }

    /**
     * Remove demo content recorded in the manifest.
     */
    public function purge(): bool
    {
        $manifest = get_option(self::MANIFEST_OPTION);

        if (! is_array($manifest) || $manifest === []) {
            return false;
        }

        $this->deleteRecords([
            'posts' => $manifest['posts'] ?? [],
            'terms' => $manifest['terms'] ?? [],
            'decision_authorities' => $manifest['decision_authorities'] ?? [],
            'assignments' => $manifest['assignments'] ?? [],
        ], true);

        if (array_key_exists('blogname', $manifest)) {
            update_option('blogname', $manifest['blogname']);
        }

        if (array_key_exists('blogdescription', $manifest)) {
            update_option('blogdescription', $manifest['blogdescription']);
        }

        delete_option(self::MANIFEST_OPTION);

        return true;
    }

    private function assertCanSeed(bool $force): void
    {
        if ($this->demoAlreadyInstalled()) {
            throw new DemoContentException(
                __('Demo content is already installed. Run `wp acorn fmr:demo-purge` before seeding again.', 'fmr')
            );
        }

        if ($force || ! $this->hasUnmarkedContent()) {
            return;
        }

        throw new DemoContentException(
            __('The site already has register data. Re-run with --force to add demo content alongside it.', 'fmr')
        );
    }

    private function demoAlreadyInstalled(): bool
    {
        $manifest = get_option(self::MANIFEST_OPTION);

        return is_array($manifest) && $manifest !== [];
    }

    private function hasUnmarkedContent(): bool
    {
        $postIds = get_posts([
            'post_type' => ['person', 'party', 'board'],
            'post_status' => 'publish',
            'posts_per_page' => 1,
            'fields' => 'ids',
            'suppress_filters' => true,
            'meta_query' => [
                'relation' => 'OR',
                [
                    'key' => self::DEMO_META_KEY,
                    'compare' => 'NOT EXISTS',
                ],
                [
                    'key' => self::DEMO_META_KEY,
                    'value' => '1',
                    'compare' => '!=',
                ],
            ],
        ]);

        if ($postIds !== []) {
            return true;
        }

        foreach (['type', 'role'] as $taxonomy) {
            $terms = get_terms([
                'taxonomy' => $taxonomy,
                'hide_empty' => false,
            ]);

            if (is_wp_error($terms)) {
                continue;
            }

            foreach ($terms as $term) {
                if (get_term_meta($term->term_id, self::DEMO_META_KEY, true) !== '1') {
                    return true;
                }
            }
        }

        return DecisionAuthority::query()->exists() || Assignment::query()->exists();
    }

    /**
     * @param  array<string, mixed>  $catalog
     */
    private function seedRegister(array $catalog): void
    {
        $typeIds = $this->seedTerms($catalog['types'], 'type');
        $this->roleIds = $this->seedRoles($catalog['roles']);
        $partyIds = $this->seedParties($catalog);
        $this->pools = $this->seedPersons($catalog, $partyIds);
        $boardIds = $this->seedBoards($catalog, $typeIds);
        $this->seedAuthorities($catalog, $boardIds);
        $this->applyResignations($catalog);
        $this->syncPersonStatus();
    }

    /**
     * @param  array<int, string>  $names
     * @return array<string, int>
     */
    private function seedTerms(array $names, string $taxonomy): array
    {
        $ids = [];

        foreach ($names as $name) {
            $ids[$name] = $this->ensureTerm($name, $taxonomy);
        }

        return $ids;
    }

    /**
     * @param  array<int, array{name: string, key: string}>  $roles
     * @return array<string, int>
     */
    private function seedRoles(array $roles): array
    {
        $ids = [];

        foreach ($roles as $role) {
            $ids[$role['key']] = $this->ensureTerm($role['name'], 'role');
        }

        return $ids;
    }

    /**
     * @param  array<string, mixed>  $catalog
     * @return array<string, int>
     */
    private function seedParties(array $catalog): array
    {
        $ids = [];

        foreach ($catalog['parties'] as $party) {
            $postId = $this->insertPost($party['name'], 'party');
            $short = strtolower($party['short']);

            $this->writeMeta($postId, [
                'party_shortening' => $party['short'],
                'party_description' => $party['description'],
                'party_address' => $catalog['street'],
                'party_zip' => $catalog['zip'],
                'party_city' => $catalog['city'],
                'party_website' => $catalog['website'].'/partier/'.$short,
                'party_email' => $short.'@'.$catalog['email_domain'],
                'party_email_visibility' => '1',
                'party_phone' => $catalog['phone'],
                'party_phone_visibility' => '0',
            ]);

            $ids[$party['short']] = $postId;
            $this->counts['parties']++;
        }

        return $ids;
    }

    /**
     * @param  array<string, mixed>  $catalog
     * @param  array<string, int>  $partyIds
     * @return array<string, array{current: array<int, array<string, mixed>>, previous: array<int, array<string, mixed>>}>
     */
    private function seedPersons(array $catalog, array $partyIds): array
    {
        $faker = Factory::create('sv_SE');
        $faker->seed(self::FAKER_SEED);

        $usedNames = [];
        $usedEmails = [];
        $pools = [];

        foreach ($catalog['parties'] as $party) {
            $alumniCount = $this->alumniCount((int) $party['seats']);
            $total = (int) $party['seats'] + $alumniCount;
            $people = [];

            for ($index = 0; $index < $total; $index++) {
                $people[] = $this->createPerson(
                    $faker,
                    $catalog,
                    $partyIds[$party['short']],
                    $party['short'],
                    $index === 0,
                    $usedNames,
                    $usedEmails
                );
            }

            $current = array_slice($people, 0, (int) $party['seats']);
            $alumni = array_slice($people, (int) $party['seats'], $alumniCount);
            $previous = array_merge($alumni, array_slice($current, $alumniCount));

            $pools[$party['short']] = [
                'current' => $current,
                'previous' => $previous,
            ];

            if (isset($current[0])) {
                $this->visibleEmailIds[$current[0]['id']] = true;
            }
        }

        return $pools;
    }

    /**
     * @param  array<string, mixed>  $catalog
     * @param  array<string, true>  $usedNames
     * @param  array<string, true>  $usedEmails
     * @return array<string, mixed>
     */
    private function createPerson(
        Generator $faker,
        array $catalog,
        int $partyId,
        string $partyShort,
        bool $groupLeader,
        array &$usedNames,
        array &$usedEmails
    ): array {
        $name = $this->uniqueName($faker, $usedNames);
        $email = $this->uniqueEmail($name['first'], $name['last'], $catalog['email_domain'], $usedEmails);
        $title = $name['first'].' '.$name['last'];
        $postId = $this->insertPost($title, 'person');

        $this->writeMeta($postId, [
            'person_firstname' => $name['first'],
            'person_lastname' => $name['last'],
            'person_party' => (string) $partyId,
            'person_active' => '0',
            'person_group_leader' => $groupLeader ? '1' : '0',
            'person_home_address' => $faker->streetAddress(),
            'person_home_address_visibility' => '0',
            'person_home_zip' => $catalog['zip'],
            'person_home_zip_visibility' => '0',
            'person_home_city' => $catalog['city'],
            'person_home_city_visibility' => '0',
            'person_home_email' => $email,
            'person_home_email_visibility' => '0',
            'person_home_phone' => $faker->numerify('07########'),
            'person_home_phone_visibility' => '0',
        ]);

        $person = [
            'id' => $postId,
            'party' => $partyShort,
            'firstname' => $name['first'],
            'lastname' => $name['last'],
            'email' => $email,
        ];

        $this->persons[] = $person;
        $this->counts['persons']++;

        return $person;
    }

    /**
     * @param  array<string, true>  $usedNames
     * @return array{first: string, last: string}
     */
    private function uniqueName(Generator $faker, array &$usedNames): array
    {
        for ($attempt = 0; $attempt < 100; $attempt++) {
            $first = $faker->firstName();
            $last = $faker->lastName();
            $key = mb_strtolower($first.' '.$last);

            if (isset($usedNames[$key])) {
                continue;
            }

            $usedNames[$key] = true;

            return [
                'first' => $first,
                'last' => $last,
            ];
        }

        throw new DemoContentException(__('Could not generate enough unique demo names.', 'fmr'));
    }

    /**
     * @param  array<string, true>  $usedEmails
     */
    private function uniqueEmail(string $first, string $last, string $domain, array &$usedEmails): string
    {
        $local = str_replace('-', '.', sanitize_title($first.' '.$last));
        $email = $local.'@'.$domain;
        $suffix = 2;

        while (isset($usedEmails[$email])) {
            $email = $local.'.'.$suffix.'@'.$domain;
            $suffix++;
        }

        $usedEmails[$email] = true;

        return $email;
    }

    /**
     * @param  array<string, mixed>  $catalog
     * @param  array<string, int>  $typeIds
     * @return array<string, int>
     */
    private function seedBoards(array $catalog, array $typeIds): array
    {
        $ids = [];

        foreach ($catalog['boards'] as $board) {
            $postId = $this->insertPost($board['name'], 'board');
            $short = strtolower($board['short']);

            $this->writeMeta($postId, [
                'board_shortening' => $board['short'],
                'board_category' => (string) $typeIds[$board['type']],
                'board_visiting_address' => $catalog['street'],
                'board_address' => $catalog['street'],
                'board_zip' => $catalog['zip'],
                'board_city' => $catalog['city'],
                'board_website' => $catalog['website'],
                'board_email' => $short.'@'.$catalog['email_domain'],
                'board_email_visibility' => '1',
                'board_phone' => $catalog['phone'],
                'board_phone_visibility' => '0',
            ]);

            $ids[$board['short']] = $postId;
            $this->counts['boards']++;
        }

        return $ids;
    }

    /**
     * @param  array<string, mixed>  $catalog
     * @param  array<string, int>  $boardIds
     */
    private function seedAuthorities(array $catalog, array $boardIds): void
    {
        foreach ($catalog['boards'] as $boardIndex => $board) {
            foreach ($catalog['periods'] as $period) {
                $authority = DecisionAuthority::query()->create([
                    'board_id' => $boardIds[$board['short']],
                    'title' => $period['title'],
                    'author_id' => $this->authorId,
                    'start_date' => $period['start'],
                    'end_date' => $period['end'],
                ]);

                $authorityId = (int) $authority->id;
                $this->created['decision_authorities'][] = $authorityId;
                $this->authorities[$board['short']][$period['key']] = $authorityId;

                $poolKey = ! empty($period['current']) ? 'current' : 'previous';
                $used = $this->assignOrdinary($catalog, $board, (int) $boardIndex, $poolKey, $authorityId, $period);
                $this->assignSubstitutes($catalog, $board, (int) $boardIndex, $poolKey, $authorityId, $period, $used);

                if (($board['kind'] ?? '') === 'council') {
                    $this->assertCouncilSize($authorityId, (int) $board['seats'], $period['title']);
                }
            }
        }
    }

    /**
     * @param  array<string, mixed>  $catalog
     * @param  array<string, mixed>  $board
     * @param  array<string, mixed>  $period
     * @return array<int>
     */
    private function assignOrdinary(
        array $catalog,
        array $board,
        int $boardIndex,
        string $poolKey,
        int $authorityId,
        array $period
    ): array {
        $allocation = ($board['kind'] ?? '') === 'council'
            ? $this->seatMap($catalog)
            : $this->allocateSeats($this->seatMap($catalog), (int) $board['seats']);

        $special = [
            $catalog['chair_short'] => 'chair',
            $catalog['first_vice_short'] => 'first_vice',
            $catalog['second_vice_short'] => 'second_vice',
        ];

        $used = [];

        foreach ($allocation as $short => $count) {
            $picked = $this->pickPeople($this->pools[$short][$poolKey], (int) $count, $boardIndex, $used);

            foreach ($picked as $index => $person) {
                $roleKey = ($index === 0 && isset($special[$short])) ? $special[$short] : 'member';
                $this->createAssignment($authorityId, (int) $person['id'], $roleKey, $period['start'], $period['end']);
                $used[] = (int) $person['id'];

                if ($roleKey === 'chair') {
                    $this->visibleEmailIds[(int) $person['id']] = true;
                }
            }
        }

        return $used;
    }

    /**
     * @param  array<string, mixed>  $catalog
     * @param  array<string, mixed>  $board
     * @param  array<string, mixed>  $period
     * @param  array<int>  $used
     */
    private function assignSubstitutes(
        array $catalog,
        array $board,
        int $boardIndex,
        string $poolKey,
        int $authorityId,
        array $period,
        array $used
    ): void {
        $substitutes = (int) $board['substitutes'];

        if ($substitutes < 1) {
            return;
        }

        $allocation = $this->allocateSeats($this->seatMap($catalog), $substitutes);
        $shortfall = 0;

        foreach ($allocation as $short => $count) {
            $picked = $this->pickPeople($this->pools[$short][$poolKey], (int) $count, $boardIndex + 3, $used);
            $shortfall += (int) $count - count($picked);

            foreach ($picked as $person) {
                $this->createAssignment($authorityId, (int) $person['id'], 'substitute', $period['start'], $period['end']);
                $used[] = (int) $person['id'];
            }
        }

        if ($shortfall < 1) {
            return;
        }

        foreach ($this->fillShortfall($catalog, $poolKey, $shortfall, $used) as $person) {
            $this->createAssignment($authorityId, (int) $person['id'], 'substitute', $period['start'], $period['end']);
        }
    }

    /**
     * @param  array<string, mixed>  $catalog
     */
    private function applyResignations(array $catalog): void
    {
        foreach ($catalog['resignations'] as $resignation) {
            $authorityId = $this->authorities[$resignation['board']][$resignation['period']] ?? null;

            if (! $authorityId) {
                continue;
            }

            $assignments = Assignment::query()
                ->where('decision_authority_id', $authorityId)
                ->where('role_term_id', $this->roleIds['member'])
                ->orderBy('id')
                ->get();

            foreach ($assignments as $assignment) {
                $person = $this->personById((int) $assignment->person_id);

                if ($person === null) {
                    continue;
                }

                $used = Assignment::query()
                    ->where('decision_authority_id', $authorityId)
                    ->pluck('person_id')
                    ->map(fn ($id) => (int) $id)
                    ->all();

                $replacement = $this->firstAvailable($this->pools[$person['party']]['current'], $used);

                if ($replacement === null) {
                    continue;
                }

                $assignment->period_end = $resignation['end'];
                $assignment->save();

                $this->createAssignment(
                    $authorityId,
                    (int) $replacement['id'],
                    'member',
                    $resignation['replacement_start'],
                    $this->periodEnd($catalog, $resignation['period'])
                );

                break;
            }
        }
    }

    private function syncPersonStatus(): void
    {
        $today = now()->toDateString();
        $activeIds = Assignment::query()
            ->whereIn('id', $this->created['assignments'])
            ->whereDate('period_start', '<=', $today)
            ->whereDate('period_end', '>=', $today)
            ->pluck('person_id')
            ->map(fn ($id) => (int) $id)
            ->all();

        $activeLookup = array_fill_keys($activeIds, true);

        foreach ($this->persons as $person) {
            $personId = (int) $person['id'];
            $isActive = isset($activeLookup[$personId]);

            update_post_meta($personId, 'person_active', $isActive ? '1' : '0');
            update_post_meta(
                $personId,
                'person_home_email_visibility',
                isset($this->visibleEmailIds[$personId]) ? '1' : '0'
            );
        }
    }

    /**
     * @param  array<string, mixed>  $catalog
     */
    private function assertCatalog(array $catalog): void
    {
        $councilSeats = null;

        foreach ($catalog['boards'] as $board) {
            if (($board['kind'] ?? '') === 'council') {
                $councilSeats = (int) $board['seats'];
                break;
            }
        }

        $partySeats = array_sum(array_column($catalog['parties'], 'seats'));

        if ($councilSeats === null || $partySeats !== $councilSeats) {
            throw new DemoContentException(__('The demo catalog seat counts do not match the council size.', 'fmr'));
        }
    }

    private function assertCouncilSize(int $authorityId, int $expected, string $title): void
    {
        $count = Assignment::query()->where('decision_authority_id', $authorityId)->count();

        if ($count === $expected) {
            return;
        }

        throw new DemoContentException(sprintf(
            /* translators: 1: expected assignments, 2: period title, 3: created assignments */
            __('Expected %1$d council assignments for %2$s, created %3$d.', 'fmr'),
            $expected,
            $title,
            $count
        ));
    }

    /**
     * @param  array<string, mixed>  $catalog
     * @return array<string, int>
     */
    private function seatMap(array $catalog): array
    {
        $map = [];

        foreach ($catalog['parties'] as $party) {
            $map[$party['short']] = (int) $party['seats'];
        }

        return $map;
    }

    /**
     * Distribute seats with the d'Hondt method. Earlier parties win ties.
     *
     * @param  array<string, int>  $partySeats
     * @return array<string, int>
     */
    private function allocateSeats(array $partySeats, int $seats): array
    {
        $allocated = [];

        foreach ($partySeats as $short => $votes) {
            $allocated[$short] = 0;
        }

        for ($seat = 0; $seat < $seats; $seat++) {
            $winner = null;
            $best = -1.0;

            foreach ($partySeats as $short => $votes) {
                $quotient = $votes / ($allocated[$short] + 1);

                if ($quotient > $best) {
                    $best = $quotient;
                    $winner = $short;
                }
            }

            if ($winner === null) {
                break;
            }

            $allocated[$winner]++;
        }

        return array_filter($allocated, fn (int $count) => $count > 0);
    }

    /**
     * @param  array<int, array<string, mixed>>  $people
     * @param  array<int>  $excludeIds
     * @return array<int, array<string, mixed>>
     */
    private function pickPeople(array $people, int $count, int $offset, array $excludeIds): array
    {
        if ($count < 1) {
            return [];
        }

        $available = [];

        foreach ($people as $person) {
            if (! in_array((int) $person['id'], $excludeIds, true)) {
                $available[] = $person;
            }
        }

        if ($available === []) {
            return [];
        }

        $length = count($available);
        $start = $offset % $length;
        $rotated = array_merge(array_slice($available, $start), array_slice($available, 0, $start));

        return array_slice($rotated, 0, min($count, $length));
    }

    /**
     * @param  array<string, mixed>  $catalog
     * @param  array<int>  $used
     * @return array<int, array<string, mixed>>
     */
    private function fillShortfall(array $catalog, string $poolKey, int $shortfall, array $used): array
    {
        $parties = $catalog['parties'];

        usort($parties, function (array $left, array $right) {
            return $right['seats'] <=> $left['seats'];
        });

        $picked = [];

        foreach ($parties as $party) {
            if ($shortfall < 1) {
                break;
            }

            $available = [];

            foreach ($this->pools[$party['short']][$poolKey] as $person) {
                if (! in_array((int) $person['id'], $used, true)) {
                    $available[] = $person;
                }
            }

            foreach ($available as $person) {
                if ($shortfall < 1) {
                    break;
                }

                $picked[] = $person;
                $used[] = (int) $person['id'];
                $shortfall--;
            }
        }

        return $picked;
    }

    /**
     * @param  array<int, array<string, mixed>>  $people
     * @param  array<int>  $excludeIds
     * @return array<string, mixed>|null
     */
    private function firstAvailable(array $people, array $excludeIds): ?array
    {
        foreach ($people as $person) {
            if (! in_array((int) $person['id'], $excludeIds, true)) {
                return $person;
            }
        }

        return null;
    }

    /**
     * @return array<string, mixed>|null
     */
    private function personById(int $personId): ?array
    {
        foreach ($this->persons as $person) {
            if ((int) $person['id'] === $personId) {
                return $person;
            }
        }

        return null;
    }

    private function createAssignment(
        int $authorityId,
        int $personId,
        string $roleKey,
        string $start,
        string $end
    ): void {
        $assignment = Assignment::query()->create([
            'decision_authority_id' => $authorityId,
            'person_id' => $personId,
            'role_term_id' => $this->roleIds[$roleKey],
            'author_id' => $this->authorId,
            'period_start' => $start,
            'period_end' => $end,
        ]);

        $this->created['assignments'][] = (int) $assignment->id;
    }

    /**
     * @param  array<string, mixed>  $catalog
     */
    private function periodEnd(array $catalog, string $key): string
    {
        foreach ($catalog['periods'] as $period) {
            if ($period['key'] === $key) {
                return $period['end'];
            }
        }

        throw new DemoContentException(__('The demo catalog is missing a mandate period.', 'fmr'));
    }

    private function alumniCount(int $seats): int
    {
        return max(1, (int) floor(($seats * 0.25) + 0.5));
    }

    private function ensureTerm(string $name, string $taxonomy): int
    {
        $existing = term_exists($name, $taxonomy);

        if (is_array($existing)) {
            return (int) $existing['term_id'];
        }

        if (is_numeric($existing)) {
            return (int) $existing;
        }

        $created = wp_insert_term($name, $taxonomy);

        if (is_wp_error($created)) {
            throw new DemoContentException($created->get_error_message());
        }

        $termId = (int) $created['term_id'];
        update_term_meta($termId, self::DEMO_META_KEY, '1');
        $this->created['terms'][] = $termId;

        return $termId;
    }

    private function insertPost(string $title, string $postType): int
    {
        $postId = wp_insert_post([
            'post_title' => $title,
            'post_name' => sanitize_title($title),
            'post_status' => 'publish',
            'post_type' => $postType,
            'post_author' => $this->authorId ?? 0,
            'post_content' => '',
            'comment_status' => 'closed',
            'ping_status' => 'closed',
        ], true);

        if (is_wp_error($postId)) {
            throw new DemoContentException($postId->get_error_message());
        }

        $postId = (int) $postId;
        update_post_meta($postId, self::DEMO_META_KEY, '1');
        $this->created['posts'][] = $postId;

        return $postId;
    }

    /**
     * @param  array<string, mixed>  $meta
     */
    private function writeMeta(int $postId, array $meta): void
    {
        foreach ($meta as $key => $value) {
            update_post_meta($postId, $key, $value);
        }
    }

    /**
     * @param  array<string, mixed>  $catalog
     */
    private function storeManifest(array $catalog): void
    {
        $manifest = $this->created;
        $manifest['blogname'] = get_option('blogname');
        $manifest['blogdescription'] = get_option('blogdescription');
        $this->previousBlogname = $manifest['blogname'];
        $this->previousBlogdescription = $manifest['blogdescription'];

        update_option('blogname', $catalog['municipality']);
        update_option('blogdescription', $catalog['tagline']);
        $this->renamedSite = true;

        update_option(self::MANIFEST_OPTION, $manifest, false);
    }

    private function rollback(): void
    {
        if ($this->renamedSite) {
            update_option('blogname', $this->previousBlogname);
            update_option('blogdescription', $this->previousBlogdescription);
            $this->renamedSite = false;
        }

        delete_option(self::MANIFEST_OPTION);
        $this->deleteRecords($this->created, false);
        $this->resetRun();
    }

    /**
     * @param  array{posts?: array<int>, terms?: array<int>, decision_authorities?: array<int>, assignments?: array<int>}  $ids
     */
    private function deleteRecords(array $ids, bool $requireFlag): void
    {
        $assignmentIds = array_map('intval', $ids['assignments'] ?? []);
        $authorityIds = array_map('intval', $ids['decision_authorities'] ?? []);

        if ($assignmentIds !== []) {
            Assignment::query()->whereIn('id', $assignmentIds)->delete();
        }

        if ($authorityIds !== []) {
            Assignment::query()->whereIn('decision_authority_id', $authorityIds)->delete();
            DecisionAuthority::query()->whereIn('id', $authorityIds)->delete();
        }

        foreach ($ids['posts'] ?? [] as $postId) {
            $postId = (int) $postId;

            if ($requireFlag && get_post_meta($postId, self::DEMO_META_KEY, true) !== '1') {
                continue;
            }

            wp_delete_post($postId, true);
        }

        foreach ($ids['terms'] ?? [] as $termId) {
            $termId = (int) $termId;

            if ($requireFlag && get_term_meta($termId, self::DEMO_META_KEY, true) !== '1') {
                continue;
            }

            $term = get_term($termId);

            if (! $term instanceof WP_Term) {
                continue;
            }

            wp_delete_term($termId, $term->taxonomy);
        }
    }

    private function resolveAuthorId(): ?int
    {
        $ids = get_users([
            'role' => 'administrator',
            'number' => 1,
            'fields' => 'ID',
            'orderby' => 'ID',
            'order' => 'ASC',
        ]);

        if (! isset($ids[0])) {
            return null;
        }

        $id = (int) $ids[0];

        return $id > 0 ? $id : null;
    }

    /**
     * @return array<string, mixed>
     */
    private function catalog(): array
    {
        $path = base_path('database/demo/catalog.php');

        if (! is_file($path)) {
            throw new DemoContentException(__('The demo catalog is missing.', 'fmr'));
        }

        $catalog = require $path;

        if (! is_array($catalog)) {
            throw new DemoContentException(__('The demo catalog is invalid.', 'fmr'));
        }

        return $catalog;
    }

    private function resetRun(): void
    {
        $this->created = [
            'posts' => [],
            'terms' => [],
            'decision_authorities' => [],
            'assignments' => [],
        ];
        $this->counts = [
            'persons' => 0,
            'parties' => 0,
            'boards' => 0,
        ];
        $this->pools = [];
        $this->persons = [];
        $this->roleIds = [];
        $this->authorities = [];
        $this->visibleEmailIds = [];
        $this->renamedSite = false;
        $this->previousBlogname = null;
        $this->previousBlogdescription = null;
    }
}
