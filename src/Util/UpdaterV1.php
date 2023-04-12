<?php

namespace App\Util;

class Updater
{
    protected $db;
    protected $schema;
    
    public function __construct($db)
    {
        $this->db = $db;
        $this->schema = $db->schema();
    }

    public function up()
    {
        $this->schema->table('citizens', function($t) {
            $t->dropColumn('voted_at');
            $t->boolean('voted')->default(false);
        });

        $this->schema->dropIfExists('online_ballots');
        $this->schema->dropIfExists('online_votes');
        
        $this->schema->create('online_ballots', function ($t) {
            $t->engine = 'InnoDB';
            $t->increments('id');
            $t->string('code', 10)->nullable();
            $t->timestamp('created_at');
            $t->boolean('sent')->nullable(); // false -> invalid
        });
        $this->schema->create('online_votes', function ($t) {
            $t->engine = 'InnoDB';
            $t->increments('id');
            $t->string('hash');
            $t->integer('project_id')->unsigned();
            $t->foreign('project_id')->references('id')->on('projects');
        });

        $this->schema->create('statistical_ballots', function ($t) {
            $t->engine = 'InnoDB';
            $t->increments('id');
            $t->string('type'); // user, link, tablet, paper
            $t->string('gender')->nullable;
            $t->integer('age')->nullable();
            $t->timestamps();
        });
        $this->schema->create('audit_trails', function ($t) {
            $t->engine = 'InnoDB';
            $t->increments('id');
            $t->string('file_name');
            $t->string('state');
            $t->string('description');
            $t->text('extra')->nullable();
            $t->timestamps();
        });
    }
}
