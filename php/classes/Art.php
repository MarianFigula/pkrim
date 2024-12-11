<?php
/**
 * Class Art
 *
 * This class handles CRUD operations for artwork data in the `art` table, including
 * creating, retrieving, updating, and deleting art records. It provides methods for
 * managing artwork metadata, image URLs, and associated user relationships.
*/

class Art {
    private $conn;
    private $table_name = "art";
    private $id;
    private $user_id;
    private $img_url;
    private $title;
    private $description;
    private $price;
    private $upload_date;

    public function __construct($db) { $this->conn = $db;}

    public function getTableName() {return $this->table_name;}
    public function setTableName($table_name) {
        $this->table_name = $table_name;
    }
    public function getId() { return $this->id;}
    public function getUserId() { return $this->user_id;}
    public function getImgUrl() { return $this->img_url;}
    public function getTitle() {return $this->title;}
    public function getDescription() {return $this->description;}
    public function getPrice() {return $this->price;}
    public function getUploadDate() {return $this->upload_date;}
    
    /**
     * Sets the art ID
     * @param int $id Art ID (must be a positive integer)
     * @throws InvalidArgumentException if the ID is not a positive integer
     */
    public function setId($id) {
        if (!filter_var($id, FILTER_VALIDATE_INT, ["options" => ["min_range" => 1]])) {
            throw new InvalidArgumentException("Invalid ID: must be a positive integer.");
        }
        $this->id = $id;
    }
    
    /**
     * Sets the user ID of the art creator
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
     * Sets the image URL
     * @param string $img_url Valid file path/URL for the artwork image (up to 512 characters)
     * @throws InvalidArgumentException if the URL is invalid or empty
     */
    public function setImgUrl($img_url) {
        $img_url = trim($img_url);
        if (empty($img_url) || strlen($img_url) > 512) {
            throw new InvalidArgumentException("Invalid Image URL: must be between 1 and 512 characters.");
        }
        $this->img_url = $this->parseUrl($img_url);
    }
    
    /**
     * Sets the artwork title
     * @param string $title Title of the artwork (up to 512 characters)
     * @throws InvalidArgumentException if the title is invalid
     */
    public function setTitle($title) {
        $title = trim($title);
        if (empty($title) || strlen($title) > 512) {
            throw new InvalidArgumentException("Invalid Title: must be between 1 and 512 characters.");
        }
        $this->title = htmlspecialchars($title, ENT_QUOTES, 'UTF-8');
    }

    /**
     * Sets the artwork description
     * @param string $description Description of the artwork (up to 1024 characters)
     * @throws InvalidArgumentException if the description is invalid
     */
    public function setDescription($description) {
        $description = trim($description);
        if (empty($description) || strlen($description) > 1024) {
            throw new InvalidArgumentException("Invalid Description: must be between 1 and 1024 characters.");
        }
        $this->description = htmlspecialchars($description, ENT_QUOTES, 'UTF-8');
    }
    
    /**
     * Sets the artwork price
     * @param int|null $price Price of the artwork (must be a positive integer or null)
     * @throws InvalidArgumentException if the price is invalid
     */
    public function setPrice($price) {
        if ($price !== null) {
            if (!filter_var($price, FILTER_VALIDATE_INT, ["options" => ["min_range" => 0]])) {
                throw new InvalidArgumentException("Price must be a more than 0.");
            }
            $this->price = $price;
        } else {
            $this->price = null;
        }
    }

    public function setUploadDate($upload_date) {
        $this->upload_date = $upload_date;
    }

    public function parseUrl($fakePath): string {
        $fakePath = str_replace('\\', '/', $fakePath);
        $filename = basename($fakePath);

        return '/arts/' . $filename;
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
     * Creates a new artwork record in the database
     * @return bool True on success, false on failure
     */
    public function createArt() {
        try {
            $this->upload_date = date('Y-m-d H:i:s');
    
            $query = "INSERT INTO " . $this->table_name . " 
                      (user_id, img_url, title, description, price, upload_date)
                      VALUES (:user_id, :img_url, :title, :description, :price, :upload_date)";
    
            $stmt = $this->conn->prepare($query);
    
            $params = [
                'user_id' => $this->user_id,
                'img_url' => $this->img_url,
                'title' => $this->title,
                'description' => $this->description,
                'price' => $this->price,
                'upload_date' => $this->upload_date,
            ];
            $this->bindParams($stmt, $params);
    
            return $stmt->execute();
        } catch (Exception $e) {
            throw $e;
        }
    }    

    // IDEA - too complex for the RESTful standard?
    /**
     * Retrieves artwork with associated reviews and user data
     * @return PDOStatement Result set containing artwork, reviews, and user data
     */
    public function getArtWithReviewsAndUser() {
        $query = "
            SELECT 
                art_creator.username AS art_creator_username,     -- Username of the art creator
                a.user_id AS art_creator_id,                     -- ID of the user who created the art
                a.id AS art_id,
                a.img_url,                                        -- URL of the art image
                a.title,                                          -- Art title
                a.description,                                    -- Art description
                a.price,                                          -- Art price
                a.upload_date,                                    -- Art upload date
                review_user.username AS review_user_username,     -- Username of the user who left the review
                r.user_id AS review_user_id,                      -- ID of the user who left the review
                r.review_text,                                    -- Review text
                r.rating,                                         -- Rating from the review
                r.review_creation_date                            -- Date of the review
            FROM 
                " . $this->table_name . " a
            JOIN 
                user art_creator ON a.user_id = art_creator.id    -- Join the User table for the art creator's details
            LEFT JOIN 
                review r ON a.id = r.art_id                       -- Join the Review table to get reviews
            LEFT JOIN 
                user review_user ON r.user_id = review_user.id    -- Join the User table again for the reviewer's details
            ORDER BY 
                a.upload_date DESC;
        ";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt;
    }

    /**
     * Retrieves all artwork records
     * @return PDOStatement Result set of artworks
     */
    public function getAllArts() {
        $query = "SELECT * FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function getArtByIds($artIds) {
        // Ensure that the $artIds is sanitized to prevent SQL injection
        $artIds = implode(',', array_map('intval', explode(',', $artIds))); // sanitize the input

        // Update query to join with user table and get the author's name (username)
        $query = "
        SELECT 
            a.id AS art_id,
            a.user_id,
            a.img_url,
            a.title,
            a.description,
            a.price,
            a.upload_date,
            u.username AS author_name  -- Retrieve the username of the author
        FROM 
            " . $this->table_name . " a
        JOIN 
            user u ON a.user_id = u.id  -- Join with the user table to get the author's data
        WHERE 
            a.id IN ($artIds)";  // Use sanitized art IDs

        // Prepare and execute the query
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt;
    }

    /**
     * Retrieves an artwork by its ID
     * @return PDOStatement Result set containing the artwork data
     */
    public function getArtById() {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
    
        $params = ['id' => $this->id];
        $this->bindParams($stmt, $params);
    
        $stmt->execute();
        return $stmt;
    }
    
    /**
     * Retrieves artworks by user ID
     * @return PDOStatement Result set containing the artworks created by the user
     */
    public function getArtsByUserId() {
        $query = "SELECT * FROM " . $this->table_name . " WHERE user_id = :user_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":user_id", $this->user_id);
        $stmt->execute();
        return $stmt;
    }

    /**
     * Updates an artwork by its ID
     * @return bool True on success, false on failure
     */
    public function updateArtById() {
        $query = "UPDATE " . $this->table_name . " SET ";
        $fieldsToUpdate = [];
        $params = ['id' => $this->id];
    
        if (!empty($this->title)) {
            $fieldsToUpdate[] = "title = :title";
            $params['title'] = $this->title;
        }
        if (!empty($this->description)) {
            $fieldsToUpdate[] = "description = :description";
            $params['description'] = $this->description;
        }
        if ($this->price !== null) {
            $fieldsToUpdate[] = "price = :price";
            $params['price'] = $this->price;
        }
    
        if (empty($fieldsToUpdate)) {
            throw new Exception("No fields to update.");
        }
    
        $query .= implode(", ", $fieldsToUpdate) . ", upload_date = CURRENT_TIMESTAMP() WHERE id = :id";
    
        $stmt = $this->conn->prepare($query);
        $this->bindParams($stmt, $params);
    
        return $stmt->execute();
    }

    /**
     * Deletes an artwork by its ID
     * @return bool True on success, false on failure
     */
    public function deleteArtById() {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
    
        $params = ['id' => $this->id];
        $this->bindParams($stmt, $params);
    
        return $stmt->execute();
    }
    

    /**
     * Deletes multiple artworks by their IDs
     * @param array $ids Array of artwork IDs
     * @return bool True on success, false on failure
     */
    public function deleteArtsByIds(array $ids) {
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