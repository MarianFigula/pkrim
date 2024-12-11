<?php
/**
 * Class User
 *
 * This class handles CRUD operations for user data in the `user` table, including
 * creating, retrieving, updating, and deleting user records. It also provides methods
 * for password verification and utility functions for centralized error handling.
 */

 // WARNING: getUserById, getUserByEmail retreive password and questions: necessary?

class User {
    private $conn;
    private $table_name = "user";
    private $id;
    private $username;
    private $email;
    private $password;
    private $security_question;
    private $security_answer;
    private $role;

    public function __construct($db) { $this->conn = $db; }

    /**
     * Sets the user ID
     * @param int $id User ID (must be a positive integer)
     * @throws InvalidArgumentException if the ID is not a positive integer
     */
    public function setId($id) {
        if (!filter_var($id, FILTER_VALIDATE_INT, ["options" => ["min_range" => 1]])) {
            throw new InvalidArgumentException("Invalid ID: must be a positive integer.");
        }
        $this->id = $id;
    }    

    /**
     * Sets the email address
     * @param string $email Valid email address (up to 256 characters)
     * @throws InvalidArgumentException if the email format is invalid
     */
    public function setEmail($email) {
        $email = trim($email);
        if (empty($email) || strlen($email) > 256 || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException("Invalid Email: must be a valid email format, up to 256 characters.");
        }
        $this->email = htmlspecialchars($email, ENT_QUOTES, 'UTF-8');
    }
    
    /**
     * Sets the username
     * @param string $username Alphanumeric username (up to 36 characters)
     * @throws InvalidArgumentException if the username format is invalid
     */
    public function setUsername($username) {
        $username = trim($username);
        if (empty($username) || strlen($username) > 36 || !preg_match('/^\w+$/', $username)) {
            throw new InvalidArgumentException("Invalid Username: must be alphanumeric, up to 36 characters.");
        }
        $this->username = htmlspecialchars($username, ENT_QUOTES, 'UTF-8');
    }
    
    /**
     * Sets the password and hashes it
     * @param string $password Plaintext password (minimum 8 characters)
     * @throws InvalidArgumentException if the password does not meet criteria
     */
    // NOTE: Consider adding required password schema
    public function setPassword($password) {
        if (empty($password) || strlen($password) < 8) {
            throw new InvalidArgumentException("Invalid Password: must be at least 8 characters.");
        }
        // Additional complexity checks?
        $this->password = password_hash($password, PASSWORD_BCRYPT); // Hash password directly
    }
  

    public function setSecurityQuestion($security_question) {
        $security_question = trim($security_question);
        if (empty($security_question) || strlen($security_question) > 512) {
            throw new InvalidArgumentException("Invalid Security Question: must be up to 512 characters.");
        }
        $this->security_question = htmlspecialchars($security_question, ENT_QUOTES, 'UTF-8');
    }
    
    private function hashSecurityAnswer($answer) {
        return password_hash($answer, PASSWORD_BCRYPT);
    }
    
    public function setSecurityAnswer($security_answer) {
        $security_answer = trim($security_answer);
        if (empty($security_answer) || strlen($security_answer) > 512) {
            throw new InvalidArgumentException("Invalid Security Answer: must be up to 512 characters.");
        }
        // Store the hashed version instead of HTML encoded
        $this->security_answer = $this->hashSecurityAnswer($security_answer);
    }
    
    public function verifySecurityAnswer($answer, $hashedAnswer) {
        return password_verify($answer, $hashedAnswer);
    }

    public function setRole($role) {
        $allowedRoles = ['A', 'U']; // Define allowed roles
        if (!in_array($role, $allowedRoles)) {
            throw new InvalidArgumentException("Invalid Role: must be 'A' or 'U'.");
        }
        $this->role = $role;
    }

    /**
     * Verifies a plaintext password against a hashed password
     * @param string $password Plaintext password
     * @param string $hashedPassword Hashed password from the database
     * @return bool True if the password matches, false otherwise
     */
    public function verifyPassword($password, $hashedPassword) {
        return password_verify($password, $hashedPassword);
    }

    public function getRole(){return $this->role;}
    public function getTableName() {return $this->table_name;}
    public function getId() {return $this->id;}

    public function getUsername(){return $this->username;}
    public function getEmail() {return $this->email;}

    // TODO: should this be present?
    public function getPassword() {return $this->password;}
    public function getSecurityQuestion() {return $this->security_question;}
    public function getSecurityAnswer() {return $this->security_answer;}

    /**
     * Dynamically binds parameters to a prepared statement
     * 
     * @param PDOStatement $stmt The prepared statement
     * @param array $params Associative array of parameters to bind (e.g., ['email' => 'test@example.com'])
     */
    private function bindParams(PDOStatement &$stmt, array $params) {
        foreach ($params as $key => $value) {
            $stmt->bindValue(':' . $key, $value);
        }
    }

    /**
     * Creates a new user record in the database
     * @return bool True on success, false on failure
     */
    public function createUser() {
        try {
            // Begin transaction for consistency
            $this->conn->beginTransaction();
            
            // Check for existing email
            if ($this->userExistsByEmail()) {
                throw new InvalidArgumentException("Email already in use.");
            }
        
            // Check for existing username
            if ($this->userExistsByUsername()) {
                throw new InvalidArgumentException("Username already taken.");
            }
    
            $query = "INSERT INTO " . $this->table_name . " 
                    (email, username, password, security_question, security_answer, role)
                    VALUES (:email, :username, :password, :security_question, :security_answer, :role)";
    
            $stmt = $this->conn->prepare($query);
            
            $params = [
                'email' => $this->email,
                'username' => $this->username,
                'password' => $this->password,
                'security_question' => $this->security_question,
                'security_answer' => $this->security_answer,
                'role' => $this->role,
            ];
            $this->bindParams($stmt, $params);
            
            $result = $stmt->execute();
            
            if ($result) {
                $this->id = $this->conn->lastInsertId();
                $this->conn->commit();
                return $this->id;  // Return the ID of the created user
            } else {
                $this->conn->rollBack();
                return false;
            }
        } catch (Exception $e) {
            $this->conn->rollBack();
            throw $e;
        }
    }

    /**
     * Retrieves all users with selected fields
     * @return PDOStatement Result set of users
     */
    public function getAllUsers()
    {
        $query = "SELECT id, username, email, security_question,
        security_answer FROM " . $this->table_name;

        $stmt = $this->conn->prepare($query);

        $stmt->execute();
        return $stmt;
    }

    /**
     * Retrieves a user by ID
     * @return PDOStatement User data for the specified ID
     */
    public function getUserById() {
        $query = "SELECT id, username, email, security_question FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
    
        $params = ['id' => $this->id];
        $this->bindParams($stmt, $params);
    
        $stmt->execute();
        return $stmt;
    }    

    /**
     * Retrieves a user by email
     * @return PDOStatement User data for the specified email
     * IDEA: password has to be retrieved for login.php - better way possible?
     */
    public function getUserByEmail() {
        $query = "SELECT id, username, password, email, security_question, security_answer, role FROM " . $this->table_name . " WHERE email = :email";
        $stmt = $this->conn->prepare($query);
    
        $params = ['email' => $this->email];
        $this->bindParams($stmt, $params);
    
        $stmt->execute();
        return $stmt;
    }
    
    /**
     * Checks if a user exists with the specified email
     * @return bool True if user exists, false otherwise
     */
    public function userExistsByEmail() {
        $query = "SELECT COUNT(*) FROM " . $this->table_name . " WHERE email = :email";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":email", $this->email); 
        $stmt->execute();
        return $stmt->fetchColumn() > 0;
    }

    /**
     * Checks if a user exists with the specified username
     * @return bool True if user exists, false otherwise
     */
    public function userExistsByUsername() {
        $query = "SELECT COUNT(*) FROM " . $this->table_name . " WHERE username = :username";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":username", $this->username);
        $stmt->execute();
        return $stmt->fetchColumn() > 0;
    }
    
    // Update user, mozno pridate do query toto:
    /*
     security_question = :security_question,
     security_answer = :security_answer
     */
    /**
     * Updates a user's username and/or email
     * Only updates fields that are set, and requires a valid user ID
     * @return bool True on success, false on failure
     */
    public function updateUserById() {

        $query = "UPDATE " . $this->table_name . "
                  SET email = :email,
                  username = :username
                  WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':username', $this->username);
        $stmt->bindParam(':id', $this->id);
        return $stmt->execute();
    }

    /**
     * Updates the user's password
     * Requires the ID and hashed password to be set
     * @return bool True on success, false on failure
     */
    public function updateUserPassword() {
        $query = "UPDATE " . $this->table_name . "
                  SET password = :password
                  WHERE id = :id";
    
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':password', $this->password);
        $stmt->bindParam(':id', $this->id);
        
        return $stmt->execute();  // Return boolean success/failure
    }

    /**
     * Deletes a user by ID
     * @return bool True on success, false on failure
     */
    public function deleteUserById() {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
    
        $params = ['id' => $this->id];
        $this->bindParams($stmt, $params);
    
        return $stmt->execute();
    }
    
    /**
     * Deletes multiple users by their IDs
     * @param array $ids Array of user IDs
     * @return bool True on success, false on failure
     */
    public function deleteUsersByIds($ids) {
        try {
            $ids = array_values(array_filter($ids, function ($id) {
                return filter_var($id, FILTER_VALIDATE_INT, ["options" => ["min_range" => 1]]) !== false;
            }));
    
            if (empty($ids)) {
                return false;
            }
    
            $this->conn->beginTransaction();
    
            $placeholders = rtrim(str_repeat('?,', count($ids)), ',');
            $query = "DELETE FROM " . $this->table_name . " WHERE id IN ($placeholders)";
            $stmt = $this->conn->prepare($query);
    
            foreach ($ids as $index => $id) {
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
}