<?php

class Database
{
    static public function connect()
    {
        try {
            $link = new PDO('mysql:host=localhost;dbname=users_db', 'developer', 'developer');
            $link->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $link;
        } catch (PDOException $e) {
            die('¡Error de conexión!: ' . $e->getMessage());
        }
    }
}
