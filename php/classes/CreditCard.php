<?php
/**
 * Class CreditCard 
 *
 * This class handles basic CRUD operations for credit card data in the `credit_card` table.
 * Note: In a production environment, consider using a payment processor/gateway 
 * instead of storing credit card details directly.
 */

class CreditCard {
    private $conn;
    private $table_name = "credit_card";
    private $id;
    private $user_id;
    private $card_number;
    private $expiration_date;
    private $cvc;
    private $card_creation_date;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getId() { return $this->id; }
    public function getUserId() { return $this->user_id; }
    public function getCardNumber() { return $this->card_number; }
    public function getExpirationDate() { return $this->expiration_date; }
    public function getTableName() { return $this->table_name; }
    // No getter for CVC for security reasons

    /**
     * Sets the credit card ID
     * @param int $id Credit card ID (must be a positive integer)
     * @throws InvalidArgumentException if the ID is not a positive integer
     */
    public function setId($id) {
        if (!filter_var($id, FILTER_VALIDATE_INT, ["options" => ["min_range" => 1]])) {
            throw new InvalidArgumentException("Invalid ID: must be a positive integer.");
        }
        $this->id = $id;
    }

    /**
     * Sets the user ID
     * @param int $user_id User ID (must be a positive integer)
     * @throws InvalidArgumentException if the user ID is not a positive integer
     */
    public function setUserId($user_id) {
        if (!filter_var($user_id, FILTER_VALIDATE_INT, ["options" => ["min_range" => 1]])) {
            throw new InvalidArgumentException("Invalid User ID: must be a positive integer.");
        }
        $this->user_id = $user_id;
    }

    /**
     * Sets the card number (basic implementation)
     * @param string $card_number Credit card number
     * @throws InvalidArgumentException if the card number is invalid
     */
    public function setCardNumber($card_number) {
        $card_number = preg_replace('/\D/', '', $card_number);
        if (empty($card_number) || strlen($card_number) < 13 || strlen($card_number) > 19) {
            throw new InvalidArgumentException("Invalid card number: must be between 13 and 19 digits.");
        }
        // Optionally, encrypt the card number here for secure storage
        $this->card_number = $card_number;
    }

    /**
     * Sets the expiration date
     * @param string $expiration_date Date in YYYY-MM-DD format
     * @throws InvalidArgumentException if the date is invalid
     */
    public function setExpirationDate($expiration_date) {
        // Validate the date format (YYYY-MM-DD)
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $expiration_date)) {
            throw new InvalidArgumentException("Invalid expiration date format: use YYYY-MM-DD.");
        }
    
        // Convert to timestamp and check if it's a valid date
        $exp_timestamp = strtotime($expiration_date);
        if (!$exp_timestamp) {
            throw new InvalidArgumentException("Invalid expiration date.");
        }
    
        // Ensure the expiration date is in the future
        $current_date = strtotime(date('Y-m-d')); // Get the current date in YYYY-MM-DD format
        if ($exp_timestamp <= $current_date) {
            throw new InvalidArgumentException("Expiration date must be in the future.");
        }
    
        $this->expiration_date = $expiration_date;
    }

    /**
     * Sets and validates the CVC
     * @param string $cvc 3 or 4 digit security code
     * @throws InvalidArgumentException if the CVC is invalid
     */
    public function setCVC($cvc) {
        $cvc = preg_replace('/\D/', '', $cvc); // Remove non-digits
        
        if (!preg_match('/^[0-9]{3,4}$/', $cvc)) {
            throw new InvalidArgumentException("Invalid CVC: must be 3 or 4 digits.");
        }

        // Store hashed version of CVC
        $this->cvc = password_hash($cvc, PASSWORD_BCRYPT);
    }

    /**
     * Verifies a CVC against its stored hash
     * @param string $cvc The CVC to verify
     * @param string $hashedCVC The stored hashed CVC
     * @return bool True if CVC matches, false otherwise
     */
    public function verifyCVC($cvc, $hashedCVC) {
        return password_verify($cvc, $hashedCVC);
    }

    /**
     * Dynamically binds parameters to a prepared statement
     *
     * @param PDOStatement $stmt The prepared statement
     * @param array $params Associative array of parameters to bind (e.g., ['id' => 1])
     */
    private function bindParams(PDOStatement &$stmt, array $params) {
        foreach ($params as $key => $value) {
            $stmt->bindValue(':' . $key, $value);
        }
    }

    /**
     * Creates a new credit card record
     * @return bool True on success, false on failure
     */
    public function createCard() {
        try {
            if ($this->isCardNumberDuplicate($this->card_number)) {
                throw new InvalidArgumentException("Credit card number already exists.");
            }
    
            $this->conn->beginTransaction();
    
            $query = "INSERT INTO " . $this->table_name . " 
                      (user_id, card_number, expiration_date, cvc)
                      VALUES (:user_id, :card_number, :expiration_date, :cvc)";
    
            $stmt = $this->conn->prepare($query);
    
            $params = [
                'user_id' => $this->user_id,
                'card_number' => $this->card_number,
                'expiration_date' => $this->expiration_date,
                'cvc' => $this->cvc
            ];
            $this->bindParams($stmt, $params);
    
            if ($stmt->execute()) {
                $this->conn->commit();
                return true;
            } else {
                $this->conn->rollBack();
                return false;
            }
        } catch (InvalidArgumentException $e) {
            throw $e;
        } catch (Exception $e) {
            $this->conn->rollBack();
            error_log("Error creating credit card record: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Check if user exists by ID
     * @param int $user_id
     * @return bool True if the user exists, false otherwise
     */
    public function doesUserExist($user_id) {
        $query = "SELECT COUNT(*) as count FROM user WHERE id = :user_id";
        $stmt = $this->conn->prepare($query);
    
        $params = ['user_id' => $user_id];
        $this->bindParams($stmt, $params);
    
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['count'] > 0;
    }

    /**
     * Check if a card number already exists
     * @param string $card_number
     * @return bool True if card number exists, false otherwise
     */
    public function isCardNumberDuplicate($card_number) {
        $query = "SELECT COUNT(*) as count FROM " . $this->table_name . " WHERE card_number = :card_number"; $stmt = $this->conn->prepare($query);
        $params = ['card_number' => $card_number];
        $this->bindParams($stmt, $params);

        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['count'] > 0;
    }

    /**
     * Retrieves a credit card by ID
     * @return PDOStatement Credit card data for the specified ID
     */
    public function getCardById() {
        $query = "SELECT id, user_id, card_number, expiration_date, card_creation_date 
                  FROM " . $this->table_name . " 
                  WHERE id = :id";
    
        $stmt = $this->conn->prepare($query);
    
        $params = ['id' => $this->id];
        $this->bindParams($stmt, $params);
    
        $stmt->execute();
        return $stmt;
    }
    
    /**
     * Retrieves all credit cards for a user
     * @return PDOStatement All credit cards for the specified user
     */
    public function getCardsByUserId() {
        $query = "SELECT id, card_number, expiration_date, card_creation_date 
                  FROM " . $this->table_name . " 
                  WHERE user_id = :user_id 
                  ORDER BY card_creation_date DESC";
    
        $stmt = $this->conn->prepare($query);
    
        $params = ['user_id' => $this->user_id];
        $this->bindParams($stmt, $params);
    
        $stmt->execute();
        return $stmt;
    }
    

    /**
     * Updates a credit card's basic information
     * Note: CVC can't be updated for security reasons
     * @return bool True on success, false on failure
     */
    public function updateCard() {
        try {
            $query = "UPDATE " . $this->table_name . "
                      SET expiration_date = :expiration_date
                      WHERE id = :id";
    
            $stmt = $this->conn->prepare($query);
    
            $params = [
                'id' => $this->id,
                'expiration_date' => $this->expiration_date
            ];
            $this->bindParams($stmt, $params);
    
            return $stmt->execute();
        } catch (Exception $e) {
            error_log("Error updating credit card: " . $e->getMessage());
            throw $e;
        }
    }    

    /**
     * Deletes a credit card by ID
     * @return bool True on success, false on failure
     */
    public function deleteCard() {
        try {
            $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
    
            $stmt = $this->conn->prepare($query);
    
            $params = ['id' => $this->id];
            $this->bindParams($stmt, $params);
    
            return $stmt->execute();
        } catch (Exception $e) {
            error_log("Error deleting credit card: " . $e->getMessage());
            throw $e;
        }
    }    

    /**
     * Deletes multiple credit cards by their IDs
     * @param array $ids Array of credit card IDs to delete
     * @return bool True if at least one card was deleted, false otherwise
     */
    public function deleteCardsByIds($ids) {
        try {
            $validIds = array_values(array_filter($ids, function ($id) {
                return filter_var($id, FILTER_VALIDATE_INT, ["options" => ["min_range" => 1]]) !== false;
            }));
    
            if (empty($validIds)) {
                return false;
            }
    
            $this->conn->beginTransaction();
    
            $placeholders = rtrim(str_repeat('?,', count($validIds)), ',');
    
            $query = "DELETE FROM " . $this->table_name . " WHERE id IN ($placeholders)";
            $stmt = $this->conn->prepare($query);
    
            foreach ($validIds as $index => $id) {
                $stmt->bindValue($index + 1, $id, PDO::PARAM_INT);
            }
    
            if (!$stmt->execute() || $stmt->rowCount() === 0) {
                $this->conn->rollBack();
                return false;
            }
    
            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            if ($this->conn->inTransaction()) {
                $this->conn->rollBack();
            }
            throw $e;
        }
    }    

    /**
     * Retrieves all credit cards (Admin only)
     * @return PDOStatement All credit cards
     */
    public function getAllCards() {
        $query = "SELECT id, user_id, card_number, expiration_date, card_creation_date 
                FROM " . $this->table_name . " 
                ORDER BY card_creation_date DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }
}