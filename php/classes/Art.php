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
     */
    public function setTitle($title) {
        $this->title = $title;
    }

    /**
     * Sets the artwork description
     * @param string $description Description of the artwork (up to 1024 characters)
     * @throws InvalidArgumentException if the description is invalid
     */
    public function setDescription($description) {
        $this->description = $description;
    }
    
    /**
     * Sets the artwork price
     * @param int|null $price Price of the artwork (must be a positive integer or null)
     * @throws InvalidArgumentException if the price is invalid
     */
    public function setPrice($price) {
        $this->price = $price;
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

    /**
     * Retrieves artwork with associated reviews and user data
     * @return PDOStatement Result set containing artwork, reviews, and user data
     */
    public function getArtWithReviewsAndUser() {
        $query = "
            SELECT 
                art_creator.username AS art_creator_username,    
                a.user_id AS art_creator_id,                     
                a.id AS art_id,
                a.img_url,                                       
                a.title,                                         
                a.description,                                   
                a.price,                                         
                a.upload_date,                                   
                review_user.username AS review_user_username,    
                r.user_id AS review_user_id,                     
                r.review_text,                                   
                r.rating,                                        
                r.review_creation_date                           
            FROM 
                " . $this->table_name . " a
            JOIN 
                user art_creator ON a.user_id = art_creator.id   
            LEFT JOIN 
                review r ON a.id = r.art_id                      
            LEFT JOIN 
                user review_user ON r.user_id = review_user.id   
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
        $artIds = implode(',', array_map('intval', explode(',', $artIds)));

        $query = "
        SELECT 
            a.id AS art_id,
            a.user_id,
            a.img_url,
            a.title,
            a.description,
            a.price,
            a.upload_date,
            u.username AS author_name
        FROM 
            " . $this->table_name . " a
        JOIN 
            user u ON a.user_id = u.id
        WHERE 
            a.id IN ($artIds)";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt;
    }

    /**
     * Retrieves an artwork by its ID
     * @return PDOStatement Result set containing the artwork data
     */
    public function getArtById() {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = ". $this->id;
        $stmt = $this->conn->prepare($query);

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
    
        if (!empty($this->title)) {
            $fieldsToUpdate[] = "title = '" . $this->title . "'";
        }
        if (!empty($this->description)) {
            $fieldsToUpdate[] = "description = '" . $this->description . "'";
        }
        if ($this->price !== null) {
            $fieldsToUpdate[] = "price = " . $this->price;
        }
    
        if (empty($fieldsToUpdate)) {
            throw new Exception("No fields to update.");
        }

        $query .= implode(", ", $fieldsToUpdate) . ", upload_date = CURRENT_TIMESTAMP() WHERE id = " . $this->id;
        $stmt = $this->conn->query($query);

        return $stmt !== false;
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