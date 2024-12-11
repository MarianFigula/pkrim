<?php

class Cart {

    private $conn;
    private $table_name = "cart";
    private $id;
    private $user_id;
    public function __construct($db) { $this->conn = $db;}

    public function getTableName(): string
    {
        return $this->table_name;
    }
    public function setTableName(string $table_name): void
    {
        $this->table_name = $table_name;
    }
    public function getId()
    {
        return $this->id;
    }
    public function setId($id): void
    {
        $this->id = $id;
    }
    public function getUserId()
    {
        return $this->user_id;
    }
    public function setUserId($user_id): void
    {
        $this->user_id = $user_id;
    }

    public function createCart()
    {
            $query = "INSERT INTO " . $this->table_name . " (user_id) VALUES (:user_id)";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':user_id', $this->user_id);
            return $stmt->execute();
    }


    public function getCartById()
    {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $this->id);
        $stmt->execute();
        return $stmt;
    }

    public function getCartByUserId()
    {

        $query = "SELECT * FROM " . $this->table_name . " WHERE user_id = :user_id";


        // Prepare the SQL statement
        $stmt = $this->conn->prepare($query);

        // Bind the user ID parameter
        $stmt->bindParam(":user_id", $this->user_id);

        // Execute the query
        $stmt->execute();

        // Return the result set
        return $stmt;
    }

    // TODO: Delete cart by user id - ak budeme chciet aj
    // spravit ze si user bude moct odstranit ucet
}