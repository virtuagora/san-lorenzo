<?php

namespace App\Util;

class Updater
{
    // Update for Version 02 - 2023-03-28
    protected $db;
    protected $schema;
    
    public function __construct($db)
    {
        $this->db = $db;
        $this->schema = $db->schema();
    }

    public function up()
    {
        $this->schema->table('projects', function($t) {
            // add edition column - INT 11 and required
            $t->integer('edition')->nullable();
            // add column monitoringStatus VARCHAR 255
            $t->string('monitoringStatus', 255)->nullable();
            // add column monitoringComment TEXT
            $t->text('monitoringComment')->nullable();
            // add column monitoringJournal TEXT
            $t->text('monitoringJournal')->nullable();

        });

        
    }
}
