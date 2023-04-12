<?php namespace App\Util;

class Installer
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function up()
    {
        $this->db->schema()->create('options', function ($table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('key', 35)->unique();
            $table->text('value')->nullable();
            $table->string('type'); //integer, string, text, hidden
            $table->string('group');
            $table->boolean('autoload');
            $table->timestamps();
        });
        $this->db->schema()->create('categories', function ($table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('name');
            $table->text('description');
        });
        $this->db->schema()->create('districts', function ($table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('name');
        });
        $this->db->schema()->create('neighbourhoods', function ($table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('name');
            $table->integer('district_id')->unsigned();
            $table->foreign('district_id')->references('id')->on('districts')->onDelete('cascade');
        });
        $this->db->schema()->create('citizens', function ($table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('table')->nullable();
            $table->integer('orden')->unsigned()->nullable();
            $table->string('dni', 15);
            $table->integer('year');
            $table->string('data')->nullable();
            $table->string('genre')->nullable();
            $table->boolean('voted')->default(false);
            $table->index('dni');
        });
        $this->db->schema()->create('subjects', function ($table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('display_name');
            $table->integer('img_type')->unsigned();
            $table->string('img_hash');
            $table->integer('points')->default(0);
            $table->string('type');
            $table->string('trace')->nullable();
            $table->integer('citizen_id')->unsigned()->nullable();
            $table->foreign('citizen_id')->references('id')->on('citizens')->onDelete('cascade');
            $table->integer('neighbourhood_id')->unsigned()->nullable();
            $table->foreign('neighbourhood_id')->references('id')->on('neighbourhoods')->onDelete('cascade');
        });
        $this->db->schema()->create('roles', function ($table) {
            $table->engine = 'InnoDB';
            $table->string('id', 50)->primary();
            $table->string('name', 25)->unique();
            $table->boolean('show_badge');
            $table->string('icon')->nullable();
            $table->text('meta')->nullable();
            $table->timestamps();
        });
        $this->db->schema()->create('subject_role', function ($table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->timestamp('expiration')->nullable();
            $table->integer('subject_id')->unsigned();
            $table->string('role_id', 50);
            $table->foreign('subject_id')->references('id')->on('subjects')->onDelete('cascade');
            $table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade');
        });
        $this->db->schema()->create('users', function ($table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('email')->nullable();
            $table->string('password')->nullable();
            $table->string('names');
            $table->string('surnames');
            $table->string('dni');
            $table->dateTime('birthday')->nullable();
            $table->string('gender')->nullable();
            $table->string('telephone')->nullable();
            $table->text('bio')->nullable();
            $table->string('pending_email')->nullable();
            $table->string('token')->nullable();
            $table->timestamp('token_expiration')->nullable();
            $table->timestamp('ban_expiration')->nullable();
            $table->string('trace')->nullable();
            $table->integer('subject_id')->unsigned()->nullable();
            $table->foreign('subject_id')->references('id')->on('subjects')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
            $table->index('email');
        });
        $this->db->schema()->create('pending_users', function ($table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('provider', 50);
            $table->string('identifier', 50);
            $table->string('token');
            $table->text('fields')->nullable();
            $table->timestamps();
            $table->unique(['provider', 'identifier']);
            $table->index('token');
        });
        $this->db->schema()->create('projects', function ($table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('code')->nullable();
            $table->string('name');
            $table->string('type');
            $table->integer('edition')->default(2000);
            $table->string('slug')->nullable();
            $table->integer('district_id')->unsigned();
            $table->foreign('district_id')->references('id')->on('districts');
            $table->text('description')->nullable();
            $table->text('objective')->nullable();
            $table->text('benefited_population')->nullable();
            $table->text('community_contributions')->nullable();
            $table->text('budget')->nullable();
            $table->decimal('total_budget',12,2)->default(0);
            $table->string('author_names');
            $table->string('author_surnames');
            $table->string('author_phone')->nullable();
            $table->string('author_email')->nullable();
            $table->string('author_dni')->nullable();
            $table->string('organization_name')->nullable();
            $table->string('organization_legal_entity')->nullable();
            $table->string('organization_address')->nullable();
            $table->string('organization_cuit')->nullable();
            $table->string('organization_nro_personeria')->nullable();
            $table->text('feasibility')->nullable();
            $table->text('journal')->nullable();
            $table->text('notes')->nullable();
            $table->string('picture_name')->nullable();
            $table->string('youtube_id')->nullable();
            $table->boolean('public')->default(false);
            $table->boolean('feasible')->nullable();
            $table->boolean('selected')->default(false);
            $table->integer('likes')->default(0);
            $table->string('monitoringStatus',255)->nullable();
            $table->text('monitoringComment')->nullable();
            $table->text('monitoringJournal')->nullable();
            $table->string('trace')->nullable();
            $table->integer('author_id')->unsigned()->nullable();
            $table->foreign('author_id')->references('id')->on('users');
            $table->timestamps();
            $table->softDeletes();
            $table->index('name');
        });
        $this->db->schema()->create('documents', function ($table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('name');
            $table->string('filename')->nullable();
            $table->text('description')->nullable();
            $table->integer('project_id')->unsigned();
            $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');
            $table->timestamps();
        });
        $this->db->schema()->create('actions', function ($table) {
            $table->engine = 'InnoDB';
            $table->string('id', 50)->primary();
            $table->string('group');
            $table->string('allowed_roles');
            $table->string('allowed_relations');
            $table->string('allowed_proxies');
            $table->timestamps();
        });
        $this->db->schema()->create('offline_ballots', function ($table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('order');
            $table->boolean('invalid')->default(true);
            $table->timestamps();
        });
        $this->db->schema()->create('offline_votes', function ($table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('project_id')->unsigned();
            $table->integer('ballot_id')->unsigned();
            $table->foreign('project_id')->references('id')->on('projects');
            $table->foreign('ballot_id')->references('id')->on('offline_ballots')->onDelete('cascade');
        });
        $this->db->schema()->create('online_ballots', function ($table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('code', 10)->nullable();
            $table->timestamp('created_at');
            $table->boolean('sent')->nullable(); // false -> invalid
        });
        $this->db->schema()->create('online_votes', function ($table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('hash');
            $table->integer('project_id')->unsigned();
            $table->foreign('project_id')->references('id')->on('projects');
        });

        $this->db->schema()->create('statistical_ballots', function ($table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('type'); // user, link, tablet, paper
            $table->string('gender')->nullable;
            $table->integer('age')->nullable();
            $table->timestamps();
        });
        $this->db->schema()->create('audit_trails', function ($table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('file_name');
            $table->string('state');
            $table->string('description');
            $table->text('extra')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        $this->db->schema()->dropAllTables();
    }

    public function populate()
    {
        $this->db->table('roles')->insert([
            [
                'id' => 'user',
                'name' => 'Usuario',
                'show_badge' => false,
            ], [
                'id' => 'verified',
                'name' => 'Verificado',
                'show_badge' => false,
            ], [
                'id' => 'admin',
                'name' => 'Admnistrador',
                'show_badge' => true,
            ], [
                'id' => 'group',
                'name' => 'Grupo',
                'show_badge' => false,
            ],
        ]);

        $this->db->table('options')->insert([
            [
                'key' => 'refresh-cache',
                'value' => '20210601',
                'type' => 'string',
                'group' => 'varios',
                'autoload' => true,
            ],
            [
                'key' => 'current-edition',
                'value' => '2021',
                'type' => 'integer',
                'group' => 'varios',
                'autoload' => true,
            ],
            [
                'key' => 'current-state',
                'value' => 'pre-upload-proposals',
                'type' => 'string',
                'group' => 'varios',
                'autoload' => true,
            ],
            [
                'key' => 'states',
                'value' => "[{\"key\":\"pre-upload-proposals\",\"name\":\"Pre-Propuestas\"},{\"key\":\"upload-proposals\",\"name\":\"Subida Propuestas\"},{\"key\":\"pre-vote\",\"name\":\"Pre-Votación\"},{\"key\":\"vote\",\"name\":\"Votación\"},{\"key\":\"pre-results\",\"name\":\"Pre-Resultados\"},{\"key\":\"results\",\"name\":\"Resultados\"}]",
                'type' => 'object',
                'group' => 'varios',
                'autoload' => true,
            ],
            [
                'key' => 'proposals-launch',
                'value' => '2021-06-07 00:00:00',
                'type' => 'date',
                'group' => 'varios',
                'autoload' => true,
            ],
            [
                'key' => 'proposals-deadline',
                'value' => '2021-07-11 23:59:59',
                'type' => 'date',
                'group' => 'varios',
                'autoload' => true,
            ],
            [
                'key' => 'proposals-available',
                'value' => 'true',
                'type' => 'boolean',
                'group' => 'varios',
                'autoload' => true,
            ],  
            [
                'key' => 'vote-launch',
                'value' => '2021-08-02 00:00:00',
                'type' => 'date',
                'group' => 'varios',
                'autoload' => true,
            ],
            [
                'key' => 'vote-deadline',
                'value' => '2021-08-22 23:59:59',
                'type' => 'date',
                'group' => 'varios',
                'autoload' => true,
            ],
            [
                'key' => 'vote-available',
                'value' => 'true',
                'type' => 'boolean',
                'group' => 'varios',
                'autoload' => true,
            ],
            [
                'key' => 'budget-comunitario-limit',
                'value' => '1440000',
                'type' => 'integer',
                'group' => 'varios',
                'autoload' => true,
            ],
            [
                'key' => 'budget-institucional-limit',
                'value' => '960000',
                'type' => 'integer',
                'group' => 'varios',
                'autoload' => true,
            ],
            [
                'key' => 'calendar',
                'value' => '[]',
                'type' => 'object',
                'group' => 'varios',
                'autoload' => true,
            ],
            [
                'key' => 'rain-message',
                'value' => 'En caso de lluvia, nos podés encontrar en el parador turístico de San Lorenzo, 3 de Febrero entre San Carlos y Sg. Cabral',
                'type' => 'string',
                'group' => 'varios',
                'autoload' => true,
            ],
            [
                'key' => 'admin-message',
                'value' => 'Este mensaje se muestra por el admin en caso de ser necesario',
                'type' => 'string',
                'group' => 'varios',
                'autoload' => true,
            ],
            [
                'key' => 'show-admin-notification',
                'value' => 'false',
                'type' => 'boolean',
                'group' => 'varios',
                'autoload' => true,
            ],
            [
                'key' => 'last-scrutiny',
                'value' => null,
                'type' => 'date',
                'group' => 'varios',
                'autoload' => true,
            ],
        ]);
    }
}
