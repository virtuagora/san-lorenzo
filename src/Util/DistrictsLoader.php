<?php namespace App\Util;

class DistrictsLoader
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function up()
    {
        $this->db->table('districts')->insert([
            ['id' => 1, 'name' => 'Norte'],
            ['id' => 2, 'name' => 'Centro'],
            ['id' => 3, 'name' => 'Sur']
        ]);
        $this->db->table('neighbourhoods')->insert([
            ['id' => 1, 'district_id' => 1, 'name' => 'Norte'],
            ['id' => 2, 'district_id' => 1, 'name' => 'Diaz Velez'],
            ['id' => 3, 'district_id' => 1, 'name' => 'Islas Malvinas'],
            ['id' => 4, 'district_id' => 1, 'name' => 'El Pino'],
            ['id' => 5, 'district_id' => 1, 'name' => 'Capitán Bermudez'],
            ['id' => 6, 'district_id' => 1, 'name' => '3 de Febrero'],
            ['id' => 7, 'district_id' => 1, 'name' => 'Fonavi Oeste'],
            ['id' => 8, 'district_id' => 1, 'name' => 'Las Quintas'],
            ['id' => 9, 'district_id' => 3, 'name' => 'Mitre'],
            ['id' => 10, 'district_id' => 2, 'name' => 'Oroño'],
            ['id' => 11, 'district_id' => 2, 'name' => 'Alem'],
            ['id' => 12, 'district_id' => 2, 'name' => 'S.U.P.E.'],
            ['id' => 13, 'district_id' => 2, 'name' => 'Remedios de Escalada'],
            ['id' => 14, 'district_id' => 2, 'name' => 'Del combate'],
            ['id' => 15, 'district_id' => 2, 'name' => 'Santiago Cabral'],
            ['id' => 16, 'district_id' => 2, 'name' => '17 de Agosto'],
            ['id' => 17, 'district_id' => 2, 'name' => '1° de Julio'],
            ['id' => 18, 'district_id' => 2, 'name' => 'San Martin'],
            ['id' => 19, 'district_id' => 3, 'name' => 'M. Moreno'],
            ['id' => 20, 'district_id' => 3, 'name' => 'José Hernandez'],
            ['id' => 21, 'district_id' => 3, 'name' => 'Villa Felisa'],
            ['id' => 22, 'district_id' => 3, 'name' => 'Rivadavia'],
            ['id' => 23, 'district_id' => 3, 'name' => 'Bouchard'],
            ['id' => 24, 'district_id' => 3, 'name' => 'Morando'],
            ['id' => 25, 'district_id' => 3, 'name' => '2 de Abríl'],
            ['id' => 26, 'district_id' => 3, 'name' => 'San Eduardo']
        ]);
    }
}
