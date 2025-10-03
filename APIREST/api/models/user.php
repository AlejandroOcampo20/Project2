<?php

class User {
    private $conn;
    private $table_name = "user";

    //propiedades del usuario
    public $id;
    public $document;
    public $name;
    public $age;
    public $phone;
    public $address;
    public $created_at;

    public function __construct($db) {
        $this->conn = $db;
    }
    
    public function read() {
        $query = 'SELECT id, document, name, created_at FROM ' . $this->table_name . ' ORDER BY created_at DESC';
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function read_single() {
        $query = 'SELECT id, document, name, age, phone, address, created_at FROM ' . $this->table_name . ' WHERE id = ? LIMIT 1';

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if($row) {
            $this->id = $row['id'];
            $this->document = $row['document'];
            $this->name = $row['name'];
            $this->age = $row['age'];
            $this->phone = $row['phone'];
            $this->address = $row['address'];
            $this->created_at = $row['created_at'];
            return true;
        } else {
            return false;
        }
    }

    public function create() {
        $query = 'INSERT INTO ' . $this->table_name . ' SET document = :document, name = :name, age = :age, phone = :phone, address = :address';

        $stmt = $this->conn->prepare($query);

        //limpiar datos
        $this->document = htmlspecialchars(strip_tags($this->document));
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->age = htmlspecialchars(strip_tags($this->age));
        $this->phone = htmlspecialchars(strip_tags($this->phone));
        $this->address = htmlspecialchars(strip_tags($this->address));

        //asignar datos
        $stmt->bindParam(':document', $this->document);
        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':age', $this->age);
        $stmt->bindParam(':phone', $this->phone);
        $stmt->bindParam(':address', $this->address);

        if($stmt->execute()) {
            return true;
        }

        error_log("Error al crear usuario: " . implode(" ", $stmt->errorInfo()));
        return false;
    }

    public function update() {
        $query = 'UPDATE ' . $this->table_name . ' SET document = :document, name = :name, age = :age, phone = :phone, address = :address WHERE id = :id';

        $stmt = $this->conn->prepare($query);

        //limpiar datos
        $this->document = htmlspecialchars(strip_tags($this->document));
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->age = htmlspecialchars(strip_tags($this->age));
        $this->phone = htmlspecialchars(strip_tags($this->phone));
        $this->address = htmlspecialchars(strip_tags($this->address));
        $this->id = htmlspecialchars(strip_tags($this->id));

        //asignar datos
        $stmt->bindParam(':document', $this->document);
        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':age', $this->age);
        $stmt->bindParam(':phone', $this->phone);
        $stmt->bindParam(':address', $this->address);
        $stmt->bindParam(':id', $this->id);

        if($stmt->execute()) {
            return true;
        }

        error_log("Error al actualizar usuario: " . implode(" ", $stmt->errorInfo()));
        return false;
    }

    public function delete() {
        $query = 'DELETE FROM ' . $this->table_name . ' WHERE id = :id';

        $stmt = $this->conn->prepare($query);
        $this->id = htmlspecialchars(strip_tags($this->id));
        $stmt->bindParam(':id', $this->id);

        if($stmt->execute()) {
            return true;
        }
        error_log("Error al eliminar usuario: " . implode(" ", $stmt->errorInfo()));
        return false;
    }
}
