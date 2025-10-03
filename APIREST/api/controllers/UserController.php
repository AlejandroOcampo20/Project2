<?php

class UserController {
    private $userModel;

    public function __construct($userModel)
    {
        $this->userModel = $userModel;
    }

    public function processRequest($method, $id)
    {
        switch ($method) {
            case 'GET':
                if ($id) {
                    $this->getUser($id);
                } else {
                    $this->getUsers();
                }
                break;
            case 'POST':
                $this->createUser();
                break;
            case 'PUT':
                if ($id) {
                    $this->updateUser($id);
                } else {
                    http_response_code(400);
                    echo json_encode(array('message' => 'ID de usuario requerido para actualizar'));
                }
                break;
            case 'DELETE':
                if ($id) {
                    $this->deleteUser($id);
                } else {
                    http_response_code(400);
                    echo json_encode(array('message' => 'ID de usuario requerido para eliminar'));
                }
                break;
            default:
                http_response_code(405);
                echo json_encode(array('message' => 'Método no permitido'));
                break;
        }
    }

    private function getUsers()
    {
        $stmt = $this->userModel->read();
        $num = $stmt->rowCount();

        if ($num > 0) {
            $users_arr = array();
            $users_arr['data'] = array();

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                extract($row);
                $user_item = array(
                    'id' => $id,
                    'document' => $document,
                    'name' => $name,
                    'created_at' => $created_at
                );
                array_push($users_arr['data'], $user_item);
            }

            http_response_code(200);
            echo json_encode($users_arr);
        } else {
            http_response_code(404);
            echo json_encode(array('message' => 'No se encontraron usuarios'));
        }
    }

    //obtener un solo usuario
    private function getUser($id)
    {
        $this->userModel->id = $id;
        if ($this->userModel->read_single()) {
            $user_item = array(
                'id' => $this->userModel->id,
                'document' => $this->userModel->document,
                'name' => $this->userModel->name,
                'age' => $this->userModel->age,
                'phone' => $this->userModel->phone,
                'address' => $this->userModel->address,
                'created_at' => $this->userModel->created_at
            );

            http_response_code(200);
            echo json_encode($user_item);
        } else {
            http_response_code(404);
            echo json_encode(array('message' => 'Usuario no encontrado'));
        }
    }

    private function createUser()
    {
        $data = json_decode(file_get_contents("php://input"));

        if (isset($data->document) && isset($data->name) && isset($data->age) && isset($data->phone) && isset($data->address)) {
            $this->userModel->document = $data->document;
            $this->userModel->name = $data->name;
            $this->userModel->age = $data->age;
            $this->userModel->phone = $data->phone;
            $this->userModel->address = $data->address;

            if ($this->userModel->create()) {
                http_response_code(201);
                echo json_encode(array('message' => 'Usuario creado'));
            } else {
                http_response_code(503);
                echo json_encode(array('message' => 'Error al crear usuario'));
            }
        } else {
            http_response_code(400);
            echo json_encode(array('message' => 'Datos incompletos'));
        }
    }

    private function updateUser($id)
    {
        $data = json_decode(file_get_contents("php://input"));

        if (isset($data->document) && isset($data->name) && isset($data->age) && isset($data->phone) && isset($data->address)) {
            $this->userModel->id = $id;
            $this->userModel->document = $data->document;
            $this->userModel->name = $data->name;
            $this->userModel->age = $data->age;
            $this->userModel->phone = $data->phone;
            $this->userModel->address = $data->address;

            if ($this->userModel->update()) {
                http_response_code(200);
                echo json_encode(array('message' => 'Usuario actualizado'));
            } else {
                http_response_code(503);
                echo json_encode(array('message' => 'Error al actualizar usuario'));
            }
        } else {
            http_response_code(400);
            echo json_encode(array('message' => 'Datos incompletos'));
        }
    }

    private function deleteUser($id)
    {
        $this->userModel->id = $id;

        if ($this->userModel->delete()) {
            http_response_code(200);
            echo json_encode(array('message' => 'Usuario eliminado'));
        } else {
            http_response_code(503);
            echo json_encode(array('message' => 'Error al eliminar usuario'));
        }
    }
}