<?php

class database{
    function opencon(): PDO{
    return new PDO(
dsn: 'mysql:host=localhost;
    dbname=library_management',
    username: 'root',
    password: '');
    }
    
    function insertUser($email,$password_hash,$is_active){
        $con = $this->opencon();

        try{
            $con->beginTransaction();
            $stmt = $con->prepare('INSERT INTO Users (username,user_password_hash,is_active) VALUES (?,?,?)');
            $stmt->execute([$email,$password_hash,$is_active]);
            $user_id = $con->lastInsertId();
            $con->commit();
            return $user_id;


        }catch(PDOException $e){
            if($con->inTransaction()){
                $con->rollBack();
                }
                throw $e;
        } 
    }
    
    function insertBorrower($firstname,$lastname,$email,$phone,$member_since,$is_active){
        $con = $this->opencon();

        try{
            $con->beginTransaction();
            $stmt = $con->prepare('INSERT INTO Borrowers (borrower_firstname,borrower_lastname,borrower_email,borrower_phone_number,borrower_member_since,is_active) VALUES(?,?,?,?,?,?)');
            $stmt->execute([$firstname,$lastname,$email,$phone,$member_since,$is_active]);
            $borrower_id = $con->lastInsertId();
            $con->commit();
            return $borrower_id;
        }catch(PDOException $e){
            if($con->inTransaction()){
                $con->rollBack();
            }
            throw $e;
        }
    }

    function insertBorrowerUser($user_id, $borrower_id){
        $con = $this->opencon();

        try{
            $con->beginTransaction();
            $stmt = $con->prepare('INSERT INTO BorrowerUser (user_id, borrower_id) VALUES(?,?)');
            $stmt->execute([$user_id,$borrower_id]);
            $con->commit();
            return true;
        }catch(PDOException $e){
            if($con->inTransaction()){
                $con->rollBack();
            }
            throw $e;
        }    
    }

    function viewBorrowerUser(){
        $con = $this->opencon();
        return $con->query('SELECT * from Borrowers')->fetchAll();
    }

    function insertBorrowerAddress($borrower_id, $house_number, $street, $barangay, $city, $province, $postal_code, $is_primary){
        $con = $this->opencon();

        try{
            $con->beginTransaction();
            $stmt = $con->prepare('INSERT INTO Borroweraddress (borrower_id, ba_house_number, ba_street, ba_barangay, ba_city, ba_province, ba_postal_code, is_primary) VALUES(?,?,?,?,?,?,?,?)');
            $stmt->execute([$borrower_id, $house_number, $street, $barangay, $city, $province, $postal_code, $is_primary]);
            $ba_id = $con->lastInsertId();
            $con->commit();
            return $ba_id;
        }catch(PDOException $e){
            if($con->inTransaction()){  
                $con->rollBack();
            }
            throw $e;
        }
    }

    function addBooks($title, $isbn, $publication_year, $edition, $publisher){
        $con = $this->opencon();

        try{
            $con->beginTransaction();
            $stmt = $con->prepare('INSERT INTO Books (book_title, book_isbn, book_publication_year, book_edition, book_publisher) VALUES(?,?,?,?,?)');
            $stmt->execute([$title, $isbn, $publication_year, $edition, $publisher]);
            $book_id = $con->lastInsertId();
            $con->commit();
            return $book_id;
        }catch(PDOException $e){
            if($con->inTransaction()){
                $con->rollBack();
            }
            throw $e;
        }
    }

    function viewBooks(){
        $con = $this->opencon();
        return $con->query('SELECT * from Books')->fetchAll();
    }

    function addCopy($book_id, $status){
        $con = $this->opencon();

        $book_id = (int)$book_id;

        try{
            $con->beginTransaction();

            $range_start = $book_id * 100;
            $range_end = $range_start + 99;

            $stmt = $con->prepare('SELECT COALESCE(MAX(copy_id), 0) FROM Bookcopy WHERE copy_id BETWEEN ? AND ? FOR UPDATE');
            $stmt->execute([$range_start, $range_end]);
            $max_copy_id = (int)$stmt->fetchColumn();

            $copy_id = ($max_copy_id > 0) ? $max_copy_id + 1 : $range_start + 1;

            if($copy_id > $range_end){
                throw new RuntimeException('Cannot add more than 99 books.');
            }

            $stmt = $con->prepare('INSERT INTO Bookcopy (copy_id, book_id, bc_status) VALUES(?,?,?)');
            $stmt->execute([$copy_id, $book_id, $status]);

            $con->commit();
            return $copy_id;
        }catch(Throwable $e){
            if($con->inTransaction()){
                $con->rollBack();
            }
            throw $e;
        }
    }

    function viewAuthors(){
        $con = $this->opencon();
        return $con->query('SELECT * from Authors')->fetchAll();
    }

    function addBookAuthor($book_id, $author_id){
        $con = $this->opencon();

        try{
            $con->beginTransaction();
            $stmt = $con->prepare('INSERT INTO BookAuthors (book_id, author_id) VALUES(?,?)');
            $stmt->execute([$book_id,$author_id]);
            $con->commit();
            return true;
        }catch(PDOException $e){
            if($con->inTransaction()){
                $con->rollBack();
            }
            throw $e;
        }    
    }

    function viewGenres(){
        $con = $this->opencon();
        return $con->query('SELECT * from Genres')->fetchAll();
    }

    function addGenre($genre_id, $book_id){
        $con = $this->opencon();

        try{
            $con->beginTransaction();
            $stmt = $con->prepare('INSERT INTO Bookgenre (genre_id, book_id) VALUES(?,?)');
            $stmt->execute([$genre_id, $book_id]);
            $con->commit();
            return true;
        }catch(PDOException $e){
            if($con->inTransaction()){
                $con->rollBack();
            }
            throw $e;
        }    
    }

    function viewCopies(){
        $con = $this->opencon();
        return $con->query("SELECT
        books.book_id,
        books.book_title,
        books.book_isbn,
        books.book_publication_year,
        books.book_publisher,
        COUNT(bookcopy.copy_id) AS Copies,
        SUM(bookcopy.bc_status = 'Available') AS Available_Copies
        FROM
        books
        LEFT JOIN bookcopy ON bookcopy.book_id = books.book_id
        GROUP BY 1")->fetchAll();
    }

    function viewUsers(){
        $con = $this->opencon();
        return $con->query("SELECT
        borrowers.borrower_id,
        CONCAT(borrowers.borrower_firstname, ' ', borrowers.borrower_lastname) AS fullname,
        borrowers.borrower_email,
        CASE
        WHEN borrowers.is_active = '1' THEN 'YES'
        ELSE 'NO'
        END AS b_ia,
        CASE
        WHEN users.is_active = '1' THEN 'YES'
        ELSE 'NO'
        END AS u_ia
        FROM borrowers
        JOIN borroweruser ON borroweruser.borrower_id = borrowers.borrower_id
        JOIN users ON users.user_id = borroweruser.user_id
    ")->fetchAll();
    }
}
