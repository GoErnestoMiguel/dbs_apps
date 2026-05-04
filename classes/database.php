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

    function viewDashboardOverview(){
        $con = $this->opencon();
        return $con->query("SELECT
            (SELECT COUNT(*) FROM books) AS total_books,
            (SELECT COUNT(*) FROM bookcopy) AS total_copies,
            (SELECT COUNT(*) FROM loan WHERE loan_status = 'OPEN') AS open_loans,
            (SELECT COUNT(*)
            FROM loanitem
            WHERE li_returned_at IS NULL
                AND li_duedate IS NOT NULL
                AND li_duedate < CURDATE()) AS overdue_items
        ")->fetch();
    }

    function viewRecentLoans($limit = 5){
        $con = $this->opencon();
        $limit = (int)$limit;

        $stmt = $con->prepare("SELECT
            loan.loan_id,
            CONCAT(borrowers.borrower_firstname, ' ', borrowers.borrower_lastname) AS fullname,
            loan.loan_status,
            loan.loan_date,
            users.username AS processed_by
        FROM loan
        JOIN borrowers ON borrowers.borrower_id = loan.borrower_id
        JOIN users ON users.user_id = loan.processed_by_user_id
        ORDER BY loan.loan_date DESC, loan.loan_id DESC
        LIMIT {$limit}");
        $stmt->execute();

        return $stmt->fetchAll();
    }

    function updateBook($book_id, $title, $isbn, $year, $publisher)
{
    $con = $this->opencon();
    try {
        $con->beginTransaction();
            $stmt = $con->prepare(
                'UPDATE Books 
                SET 
                    book_title = ?, 
                    book_isbn = ?, 
                    book_publication_year = ?, 
                    book_publisher = ? 
                WHERE book_id = ?');
            $stmt->execute([$title, $isbn, $year, $publisher, $book_id]);
        $con->commit();
        return true;
    } catch (PDOException $e) {
        if ($con->inTransaction()) {
            $con->rollBack();
        }
        throw $e;
    }
}

    function addAuthor($firstname, $lastname, $birth, $nationality)
    {
        $con = $this->opencon();

        try {
            $con->beginTransaction();

            $stmt = $con->prepare('INSERT INTO authors (author_firstname, author_lastname, author_birthyear, author_nationality) VALUES (?, ?, ?, ?)');
            $stmt->execute([$firstname, $lastname, $birth, $nationality]);

            $con->commit();
            return true;
        } catch (PDOException $e) {
            if ($con->inTransaction()) {
                $con->rollBack();
            }
            throw $e;
        }
    }

    function addGenres($genre_name)
    {
        $con = $this->opencon();

        try {
            $con->beginTransaction();

            $stmt = $con->prepare('INSERT INTO genres (genre_name) VALUES (?)');
            $stmt->execute([$genre_name]);

            $con->commit();
            return true;
        } catch (PDOException $e) {
            if ($con->inTransaction()) {
                $con->rollBack();
            }
            throw $e;
        }
    }

    function getAuthors()
    {
        $con = $this->opencon();
        $stmt = $con->prepare('SELECT * FROM authors');
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    function getGenres()
    {
        $con = $this->opencon();
        $stmt = $con->prepare('SELECT * FROM genres');
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    function deletebooks($book_id){
        $con = $this->opencon();
        try {
            $con->beginTransaction();

            $stmtCopies = $con->prepare('DELETE FROM BookCopy WHERE book_id = ?');
            $stmtCopies->execute([$book_id]);

            $stmtGenre = $con->prepare('DELETE FROM BookGenre WHERE book_id = ?');
            $stmtGenre->execute([$book_id]);

            $stmtBA = $con->prepare('DELETE FROM BookAuthors WHERE book_id = ?');
            $stmtBA->execute([$book_id]);

            $stmtBook = $con->prepare('DELETE FROM Books WHERE book_id = ?');
            $stmtBook->execute([$book_id]);

            $con->commit();
            return true;
        }catch (PDOException $e) {
            if ($con->inTransaction()) {
                $con->rollBack();
            }
            throw $e;
        }
    }
}