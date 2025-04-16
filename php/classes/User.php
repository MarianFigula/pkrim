<?php
/**
 * Class User
 *
 * This class handles CRUD operations for user data in the `user` table, including
 * creating, retrieving, updating, and deleting user records. It also provides methods
 * for password verification and utility functions for centralized error handling.
 */

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
        $this->id = $id;
    }    

    /**
     * Sets the email address
     * @param string $email Valid email address (up to 256 characters)
     * @throws InvalidArgumentException if the email format is invalid
     */
    public function setEmail($email) {
        $this->email = $email;
    }
    /**
     * Sets the username
     * @param string $username Alphanumeric username (up to 36 characters)
     * @throws InvalidArgumentException if the username format is invalid
     */
    public function setUsername($username) {
        $this->username = $username;
    }
    
    /**
     * Sets the password and hashes it
     * @param string $password Plaintext password (minimum 8 characters)
     * @throws InvalidArgumentException if the password does not meet criteria
     */
    public function setPassword($password) {
        if (empty($password)) {
            throw new InvalidArgumentException("Invalid Password: must be at least 8 characters.");
        }

        $this->password = $password;
    }
  

    public function setSecurityQuestion($security_question) {
        $this->security_question = $security_question;
    }

    
    public function setSecurityAnswer($security_answer) {
        $security_answer = trim($security_answer);
        if (empty($security_answer) || strlen($security_answer) > 512) {
            throw new InvalidArgumentException("Invalid Security Answer: must be up to 512 characters.");
        }
        $this->security_answer = $security_answer;
    }
    
    public function verifySecurityAnswer($answerFromRequest, $databaseAnswer) {
        return $answerFromRequest === $databaseAnswer;
    }

    public function setRole($role) {
        $allowedRoles = ['S', 'U'];
        if (!in_array($role, $allowedRoles)) {
            throw new InvalidArgumentException("Invalid Role: must be 'S' or 'U'.");
        }
        $this->role = $role;
    }


    public function getRole(){return $this->role;}
    public function getTableName() {return $this->table_name;}
    public function getId() {return $this->id;}

    public function getUsername(){return $this->username;}
    public function getEmail() {return $this->email;}

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
            $this->conn->beginTransaction();

            if ($this->userExistsByEmail()) {
                throw new InvalidArgumentException("Email already in use.");
            }

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
                return $this->id;
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
        $query = "SELECT id, username, password, email, security_question, security_answer, role 
              FROM " . $this->table_name . " 
              WHERE email = '" . $this->email . "'";

        $stmt = $this->conn->query($query);

        return $stmt;
    }

    public function verifyUserLogin()
    {
        try {
            $query = "SELECT id, username, password, email, security_question, security_answer, role 
            FROM " . $this->table_name . " WHERE email = '" . $this->email . "' AND password = '" . $this->password . "';";


            $stmt = $this->conn->query($query);
        }catch (Exception $e) {
            throw $e;
        }

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
        
        return $stmt->execute();
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