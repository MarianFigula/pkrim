<?php

class CartArt
{
    private $conn;
    private $table_name = "cart_art";
    private int $id;
    private int $cartId;
    private int $artId;

    public function __construct($db) { $this->conn = $db;}

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getCartId(): int
    {
        return $this->cartId;
    }

    public function setCartId(int $cartId): void
    {
        $this->cartId = $cartId;
    }

    public function getArtId(): int
    {
        return $this->artId;
    }

    public function setArtId(int $artId): void
    {
        $this->artId = $artId;
    }

    /**
     * Retrieves cart items based on the cart ID.
     *
     * This method fetches all records from the database table corresponding to the cart ID.
     * The query uses a prepared statement for security.
     *
     * @return PDOStatement The statement containing the fetched cart items.
     * @throws PDOException If the query fails to execute.
     */

    public function getCartArtsByCartId()
    {
        $query = "SELECT * FROM " . $this->table_name . " WHERE cart_id = :cart_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":cart_id", $this->cartId);
        $stmt->execute();
        return $stmt;
    }

    /**
     * Creates a new record in the cart_art table.
     *
     * This method inserts a new association between a cart and an art piece
     * into the database using the provided cart ID and art ID. The query uses
     * prepared statements to ensure security.
     *
     * @return bool True if the record is successfully inserted, false otherwise.
     * @throws Exception If an error occurs during the execution of the query.
     */


    public function createCartArt()
    {
        try {
            $query = "INSERT INTO cart_art (cart_id, art_id) 
                      VALUES (:cart_id, :art_id)";

            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(":cart_id", $this->cartId);
            $stmt->bindParam("art_id", $this->artId);

            return $stmt->execute();

        }catch (Exception $e){
            throw $e;
        }
    }

    /**
     * Deletes a record from the cart_art table based on the art ID.
     *
     * This method removes the association between a cart and an art piece
     * from the database using the specified art ID. The query uses a prepared
     * statement to enhance security.
     *
     * @return bool True if the record is successfully deleted, false otherwise.
     * @throws PDOException If an error occurs during the execution of the query.
     */


    public function deleteCartArtByArtId()
    {
        $query = "DELETE FROM " . $this->table_name . " WHERE art_id = :art_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':art_id', $this->artId);

        return $stmt->execute();

    }

    public function deleteCartArtByCartId()
    {
        $query = "DELETE FROM " . $this->table_name . " WHERE cart_id = :cart_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':cart_id', $this->cartId);

        return $stmt->execute();
    }

    public function deleteCartArtByCartIdAndArtId()
    {
        $query = "DELETE FROM " . $this->table_name . " WHERE cart_id = :cart_id AND art_id = :art_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':cart_id', $this->cartId);
        $stmt->bindParam(':art_id', $this->artId);

        return $stmt->execute();
    }

    /**
     * Remove all rows when the user will buy the arts
     */
    public function clearCartArt(array $artIds)
    {
        try {
            // Build a parameterized query with placeholders for the art IDs
            $placeholders = implode(',', array_fill(0, count($artIds), '?'));

            // Define the SQL query
            $query = "DELETE FROM " . $this->table_name . " WHERE cart_id = ? AND art_id IN ($placeholders)";

            // Prepare the statement
            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(1, $this->cartId);

            // Bind the `art_id` values dynamically
            foreach ($artIds as $index => $artId) {
                $stmt->bindValue($index + 2, $artId); // Start from the second parameter
            }

            // Execute the statement
            return $stmt->execute();
        } catch (Exception $e) {
            throw $e; // Rethrow the exception for error handling
        }
    }
}

