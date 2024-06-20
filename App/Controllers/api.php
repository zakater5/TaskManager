<?php

namespace App\Controllers;

class api {
    private $db; // globalna spremenljivka za podatkovno bazo katera je potem dostopna celem klasa (ampak le tem klasu ker je private)

    public function __construct($pdo) { // konstruktor kateri pripravi instanco podatkovne baze za uporabo v klasu
        $this->db = $pdo; // poda objekt podatkovne baze globalni spremenljivki v tem kalsu
    }

    public function get_all_tasks(){ // funkcija, ki uporabniku posle vse taske v podat. bazi
        session_start();
        if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === false) { // preveri ali ni prijavljen
            echo json_encode([]); // return empty json / ce ni prijavljen nima kej tuki iskat
            exit;
        }

        try {
            $stmt = $this->db->prepare("SELECT * FROM tasks"); // sql query za uzem useh informacij v vseh taskih
            $stmt->execute(); // izvedemo sql query
            $tasks = $stmt->fetchAll(\PDO::FETCH_ASSOC); // shranima rezulatat v $tasks
            echo json_encode($tasks); // vrnemo rezultat v obliki json-a

        } catch (\PDOException $e) { // ce pride do napake
            die("Error executing query: " . $e->getMessage());
        }
    }

    public function add_task(){
        session_start();
        if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === false) { // preveri ali ni prijavljen
            echo json_encode([]); // return empty json / ce ni prijavljen nima kej tuki iskat
            exit;
        }

        $task_desc = $_POST['task_desc']; // dobimo opis task-a
        $task_priority = $_POST['task_priority'];
        $username = $_SESSION['username']; // dobimo ime tistega ki jo je ustvaril od seje

        try {
            $stmt = $this->db->prepare("SELECT id FROM users WHERE username = :username"); // sql query s katerega dobimo id uporanika kateri je ustavril task
            $stmt->execute(['username' => $username]); // izvedemo sql
            $user = $stmt->fetch(\PDO::FETCH_ASSOC); // rezultat zapisemo v $user

            if (!$user) { // ce uporanik ne obstaja
                throw new Exception("User not found"); // exception - ni uporabnika
            }
            $user_id = $user['id']; // id uporanika zapisemo v $user_id

        } catch (\PDOException $e) { // ce pride do napake, vrnemo status in javimo napako uporaniku v obliki json-a
            echo json_encode(['status' => 'error', 'message' => "Error executing query: " . $e->getMessage()]);
            exit;
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
            exit;
        }

        try { // ustavimo nov record v tabelo tasks
            $stmt = $this->db->prepare("INSERT INTO tasks (is_completed, task, user_id, priority) VALUES (:is_completed, :task, :user_id, :priority)");
            $stmt->execute([ // dodamo argumente in izvedemo sql query
                'user_id' => $user_id,
                'task' => $task_desc,
                'priority' => $task_priority,
                'is_completed' => 0
            ]);
            $task_id = $this->db->lastInsertId(); // pridobimo id zadnje-ustavrjenega task-a

            // uporabniku poslemo novo ustvarjen task, da ga lahko jquery na novo prikaze
            echo json_encode(['status' => 'success', 'task' => ['id' => $task_id, 'task' => $task_desc, 'priority' => $task_priority, 'is_completed' => 0]]);
            exit;

        } catch (\PDOException $e) { // prislo do napake :(
            echo json_encode(['status' => 'error', 'message' => "Error executing query: " . $e->getMessage()]);
            exit;
        }
    }

    public function delete_task(){ // funkcija za brisanje task-ov
        $task_id = $_POST['task_id']; // pridobimo task id
    
        try {
            $stmt = $this->db->prepare("DELETE FROM tasks WHERE id = :task_id"); // query za brisanje taska kateri ima ta id
            $stmt->execute(['task_id' => $task_id]); // izvajanje in podajanje argumenta task_id
    
            echo json_encode(['status' => 'success']); // uspesno zbrisano
    
        } catch (\PDOException $e) { // erroror
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }
    
    public function edit_task(){ // funkcija za edit-anje task-a
        $task_id = $_POST['task_id']; // id taska katerega zelimo posodobiti
        $task_desc = $_POST['task_desc']; // novi opis taska
    
        try {
            $stmt = $this->db->prepare("UPDATE tasks SET task = :task_desc WHERE id = :task_id"); // update query
            $stmt->execute(['task_id' => $task_id, 'task_desc' => $task_desc]); // argumenti in izvajanje
    
            echo json_encode(['status' => 'success']); // successful
    
        } catch (\PDOException $e) { // failed
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function mark_task_complete(){ // funkcija za oznacevanje taska kot upravljen
        $task_id = $_POST['task_id'];

        try { // posodobitev column-a is_completed iz 0 na 1 kot da je upravljen
            $stmt = $this->db->prepare("UPDATE tasks SET is_completed = 1 WHERE id = :task_id");
            $stmt->execute(['task_id' => $task_id]);
    
            echo json_encode(['status' => 'success']);

        } catch (\PDOException $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }
}
