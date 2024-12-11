<?php
/**
 * Class Review
 *
 * This class handles CRUD operations for review data in the `review` table, including
 * creating, retrieving, updating, and deleting review records. It provides methods
 * for managing art reviews with ratings and implementing data validation.
 */

class Review {
    private $conn;
    private $table_name = "review";
    private $id;
    private $user_id;
    private $art_id;
    private $review_text;
    private $rating;
    private $review_creation_date;
    
    public function __construct($db) { $this->conn = $db;}

    public function getTableName() {return $this->table_name;}
    public function getId() {return $this->id;}
    public function getUserId() {return $this->user_id;}
    public function getArtId() {return $this->art_id;}
    public function getRating(){return $this->rating;}
    public function getReviewText() {return $this->review_text;}
    public function getReviewCreationDate(){
        return $this->review_creation_date;
    }

    /**
     * Sets the review ID
     * @param int $id Review ID (must be a positive integer)
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
     * Sets the art ID
     * @param int $art_id Art ID (must be a positive integer)
     * @throws InvalidArgumentException if the art ID is not a positive integer
     */
    public function setArtId($art_id) {
        if (!filter_var($art_id, FILTER_VALIDATE_INT, ["options" => ["min_range" => 1]])) {
            throw new InvalidArgumentException("Invalid Art ID: must be a positive integer.");
        }
        $this->art_id = $art_id;
    }

    /**
     * Sets the review text
     * @param string $review_text Review text (up to 1024 characters)
     * @throws InvalidArgumentException if the review text is empty or too long
     */
    public function setReviewText($review_text) {
        $review_text = trim($review_text);
        if (empty($review_text) || strlen($review_text) > 1024) {
            throw new InvalidArgumentException("Invalid Review Text: must be between 1 and 1024 characters.");
        }
        $this->review_text = htmlspecialchars($review_text, ENT_QUOTES, 'UTF-8');
    }

    /**
     * Sets the rating value
     * @param int $rating Rating value (1-5)
     * @throws InvalidArgumentException if the rating is not between 1 and 5
     */
    public function setRating($rating) {
        if (!filter_var($rating, FILTER_VALIDATE_INT, ["options" => ["min_range" => 1, "max_range" => 5]])) {
            throw new InvalidArgumentException("Invalid Rating: must be between 1 and 5.");
        }
        $this->rating = $rating;
    }

    /**
     * Sets the review creation date
     * @param string $date Date in Y-m-d H:i:s format
     * @throws InvalidArgumentException if the date format is invalid
     */
    public function setReviewCreationDate($date) {
        if ($date && !strtotime($date)) {
            throw new InvalidArgumentException("Invalid Date Format: must be Y-m-d H:i:s.");
        }
        $this->review_creation_date = $date;
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
     * Creates a new review record in the database
     * @return bool True on success, false on failure
     */
    public function createReview() {
        try {
            $this->review_creation_date = date('Y-m-d H:i:s');
    
            $query = "INSERT INTO " . $this->table_name . " 
                      (user_id, art_id, review_text, rating, review_creation_date)
                      VALUES (:user_id, :art_id, :review_text, :rating, :review_creation_date)";
    
            $stmt = $this->conn->prepare($query);
    
            $params = [
                'user_id' => $this->user_id,
                'art_id' => $this->art_id,
                'review_text' => $this->review_text,
                'rating' => $this->rating,
                'review_creation_date' => $this->review_creation_date,
            ];
            $this->bindParams($stmt, $params);
    
            return $stmt->execute();
        } catch (Exception $e) {
            error_log("Error creating review: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Retrieves all reviews
     * @return PDOStatement Result set of reviews
     */
    public function getReviews() {
        $query = "SELECT r.*, u.username 
                 FROM " . $this->table_name . " r
                 JOIN user u ON r.user_id = u.id
                 ORDER BY r.review_creation_date DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    /**
     * Retrieves a review by ID
     * @return PDOStatement Review data for the specified ID
     */
    public function getReviewById() {
        $query = "SELECT r.*, u.username 
                  FROM " . $this->table_name . " r
                  JOIN user u ON r.user_id = u.id
                  WHERE r.id = :id";
    
        $stmt = $this->conn->prepare($query);
    
        $params = ['id' => $this->id];
        $this->bindParams($stmt, $params);
    
        $stmt->execute();
        return $stmt;
    }
    

    /**
     * Retrieves reviews by user ID
     * @return PDOStatement Reviews for the specified user
     */
    public function getReviewsByUserId() {
        $query = "SELECT r.*, u.username, a.title as art_title
                  FROM " . $this->table_name . " r
                  JOIN user u ON r.user_id = u.id
                  JOIN art a ON r.art_id = a.id
                  WHERE r.user_id = :user_id
                  ORDER BY r.review_creation_date DESC";
    
        $stmt = $this->conn->prepare($query);
    
        $params = ['user_id' => $this->user_id];
        $this->bindParams($stmt, $params);
    
        $stmt->execute();
        return $stmt;
    }
    

    /**
     * Retrieves reviews by art ID
     * @return PDOStatement Reviews for the specified artwork
     */
    public function getReviewsByArtId() {
        $query = "SELECT r.*, u.username 
                  FROM " . $this->table_name . " r
                  JOIN user u ON r.user_id = u.id
                  WHERE r.art_id = :art_id
                  ORDER BY r.review_creation_date DESC";
    
        $stmt = $this->conn->prepare($query);
    
        $params = ['art_id' => $this->art_id];
        $this->bindParams($stmt, $params);
    
        $stmt->execute();
        return $stmt;
    }
    

    /**
     * Retrieves all reviews
     * @return PDOStatement Result set of reviews
     */
    public function getAllReviews() {
        $query = "SELECT r.*, u.username, a.title as art_title 
                FROM " . $this->table_name . " r
                JOIN user u ON r.user_id = u.id
                JOIN art a ON r.art_id = a.id
                ORDER BY r.review_creation_date DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    /**
     * Updates a review by ID
     * @return bool True on success, false on failure
     */
    public function updateReviewById() {
        $query = "UPDATE " . $this->table_name . " SET ";
        $fieldsToUpdate = [];
        $params = ['id' => $this->id];
    
        if (!empty($this->review_text)) {
            $fieldsToUpdate[] = "review_text = :review_text";
            $params['review_text'] = $this->review_text;
        }
        if ($this->rating !== null) {
            $fieldsToUpdate[] = "rating = :rating";
            $params['rating'] = $this->rating;
        }
    
        if (empty($fieldsToUpdate)) {
            throw new Exception("No fields to update.");
        }
    
        $query .= implode(", ", $fieldsToUpdate) . ", review_creation_date = CURRENT_TIMESTAMP WHERE id = :id";
    
        $stmt = $this->conn->prepare($query);
        $this->bindParams($stmt, $params);
    
        return $stmt->execute();
    }    

    /**
     * Deletes a review by ID
     * @return bool True on success, false on failure
     */
    public function deleteReviewById() {
        try {
            $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";

            $stmt = $this->conn->prepare($query);

            $params = ['id' => $this->id];
            $this->bindParams($stmt, $params);

            return $stmt->execute();
        } catch (Exception $e) {
            error_log("Error deleting review: " . $e->getMessage());
            throw $e;
        }
    }


    /**
     * Deletes multiple reviews by their IDs
     * @param array $ids Array of review IDs to delete
     * @return bool True on success, false on failure
     * @throws InvalidArgumentException if any ID is not a positive integer
     */
    public function deleteReviewsByIds($ids) {
        try {
            $validIds = array_values(array_filter($ids, function ($id) {
                return filter_var($id, FILTER_VALIDATE_INT, ["options" => ["min_range" => 1]]) !== false;
            }));
    
            if (empty($validIds)) {
                return false;
            }
    
            $placeholders = rtrim(str_repeat('?,', count($validIds)), ',');
    
            $query = "DELETE FROM " . $this->table_name . " WHERE id IN ($placeholders)";
            $stmt = $this->conn->prepare($query);
    
            foreach ($validIds as $index => $id) {
                $stmt->bindValue($index + 1, $id, PDO::PARAM_INT);
            }
    
            return $stmt->execute();
        } catch (Exception $e) {
            error_log("Error deleting reviews: " . $e->getMessage());
            throw $e;
        }
    }    
}
