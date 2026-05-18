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
        return $con->query('SELECT * from Borrowers 
        ORDER BY borrower_id')->fetchAll();
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
        return $con->query('SELECT * from Books
        ORDER BY book_id')->fetchAll();
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
        return $con->query('SELECT * from Authors
        ORDER BY author_id')->fetchAll();
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
        return $con->query('SELECT * from Genres
        ORDER BY genre_id')->fetchAll();
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
    ORDER BY users.user_id")->fetchAll();
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

    function updateAuthor($author_id, $firstname, $lastname, $birth, $nationality)
    {
        $con = $this->opencon();

        try {
            $con->beginTransaction();

            $stmt = $con->prepare('UPDATE authors SET author_firstname = ?, author_lastname = ?, author_birthyear = ?, author_nationality = ? WHERE author_id = ?');
            $stmt->execute([$firstname, $lastname, $birth, $nationality, $author_id]);

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

    function deleteAuthor($author_id){
        $con = $this->opencon();
        try {
            $con->beginTransaction();

            $stmtAuthor = $con->prepare('DELETE FROM authors WHERE author_id = ?');
            $stmtAuthor->execute([$author_id]);

            $stmtBA = $con->prepare('DELETE FROM BookAuthors WHERE author_id = ?');
            $stmtBA->execute([$author_id]);

            $con->commit();
            return true;
        }catch (PDOException $e) {
            if ($con->inTransaction()) {
                $con->rollBack();
            }
            throw $e;
        }
    }

    function updateGenre($genre_id, $genre_name){
        $con = $this->opencon();

        try {
            $con->beginTransaction();

            $stmt = $con->prepare('UPDATE genres SET genre_name = ? WHERE genre_id = ?');
            $stmt->execute([$genre_name, $genre_id]);

            $con->commit();
            return true;
        } catch (PDOException $e) {
            if ($con->inTransaction()) {
                $con->rollBack();
            }
            throw $e;
        }
    }

    function deleteGenre($genre_id){
        $con = $this->opencon();
        try {
            $con->beginTransaction();

            $stmtGenre = $con->prepare('DELETE FROM genres WHERE genre_id = ?');
            $stmtGenre->execute([$genre_id]);

            $stmtBG = $con->prepare('DELETE FROM BookGenre WHERE genre_id = ?');
            $stmtBG->execute([$genre_id]);

            $con->commit();
            return true;
        }catch (PDOException $e) {
            if ($con->inTransaction()) {
                $con->rollBack();
            }
            throw $e;
        }
    }

    function getActiveBorrowers(){
        $con = $this->opencon();
            return $con->query("SELECT 
                borrower_id,
                CONCAT(borrower_firstname, ' ', borrower_lastname) AS borrower_name
                FROM borrowers
                WHERE is_active = 1")->fetchAll();
    }

    function getAvailableCopies(){
        $con = $this->opencon();
            return $con->query("SELECT
                bookcopy.copy_id,
                books.book_id,
                books.book_title
                FROM books
            JOIN bookcopy ON books.book_id = bookcopy.book_id
            WHERE bookcopy.bc_status = 'AVAILABLE'
            ORDER BY books.book_title")->fetchAll();
    }

    function createLoanWithItems($borrower_id, $processed_by_user_id, $copy_ids, $li_duedate, $condition_out){
        $con = $this->opencon();
        try{
            $con->beginTransaction();
            $insertLoanStmt = $con->prepare("INSERT INTO Loan (borrower_id, processed_by_user_id, loan_status, loan_date) VALUES (?, ?, 'OPEN', NOW()) ");
            $insertLoanStmt->execute([$borrower_id, $processed_by_user_id]);
            $loan_id = $con->lastInsertId();

            $checkCopyStmt = $con->prepare("SELECT bc_status FROM BookCopy WHERE copy_id = ?");
            
            $activeLoanStmt = $con->prepare("SELECT COUNT(*) as active_count FROM LoanItem JOIN Loan ON LoanItem.loan_id = Loan.loan_id WHERE LoanItem.copy_id = ? AND LoanItem.li_returned_at IS NULL AND Loan.loan_status = 'OPEN' ");

            $insertLoanItemStmt = $con->prepare("INSERT INTO LoanItem(loan_id, copy_id, li_duedate, condition_out) VALUE (?,?,?,?) ");

            $updateCopyStmt = $con->prepare("UPDATE BookCopy SET bc_status = 'ON_LOAN' WHERE copy_id = ?");

            foreach ($copy_ids as $copy_id) {

                $checkCopyStmt->execute([$copy_id]);
                $copyStatus = $checkCopyStmt->fetch();
            
                if (!$copyStatus) {
                    throw new Exception("Copy ID $copy_id does not exist.");
                }
            
                if ($copyStatus['bc_status'] !== 'AVAILABLE') {
                    throw new Exception("Copy ID $copy_id is not available.");
                }
            
                $activeLoanStmt->execute([$copy_id]);
                $activeLoan = $activeLoanStmt->fetch();
            
                if ($activeLoan['active_count'] > 0) {
                    throw new Exception("Copy already on active loan.");
                }
            
                $insertLoanItemStmt->execute([$loan_id, $copy_id, $li_duedate, $condition_out]);
                $updateCopyStmt->execute([$copy_id]);
            }
            
            $con->commit();
            return $loan_id;

        } catch (Exception $e) {
            if ($con->inTransaction()) {
                $con->rollBack();
            }
            throw $e;
        }
    }

    function getOnLoanItem(){
        $con = $this->opencon();
        return $con->query("SELECT loanitem.loan_item_id, books.book_title, loanitem.li_duedate FROM loanitem JOIN bookcopy ON loanitem.copy_id = bookcopy.copy_id JOIN books ON bookcopy.book_id = books.book_id JOIN loan ON loanitem.loan_id = loan.loan_id WHERE loanitem.li_returned_at IS NULL AND loan.loan_status = 'OPEN'   
        ")->fetchAll();
    }

    function processLoanReturns($loanitemID, $returnedAt, $conditionIn){
        $con = $this->opencon();
        try{
            $con->beginTransaction();

            $getLoanItemstmt = $con->prepare("SELECT copy_id, loan_id FROM loanitem WHERE loan_item_id = ?");
            $getLoanItemstmt->execute([$loanitemID]);

            $Loanitem = $getLoanItemstmt->fetch();

            if(!$Loanitem){
                throw new Exception("Loan Item ". $loanitemID ." is not existing");
            }

            $copy_id = $Loanitem['copy_id'];
            $loan_id = $Loanitem['loan_id'];

            $updateLoanItemstmt = $con->prepare("UPDATE loanitem SET li_returned_at = ?,
            condition_in = ? 
            WHERE loan_item_id = ?");
            $updateLoanItemstmt->execute([$returnedAt, $conditionIn, $loanitemID]);

            $updateBookCopystmt = $con->prepare("UPDATE bookcopy SET bc_status = 'AVAILABLE' WHERE copy_id = ?");
            $updateBookCopystmt->execute([$copy_id]);

            $countRemainingstmt = $con->prepare("SELECT COUNT(*) AS unreturned_count FROM loanitem WHERE loan_id = ? AND li_returned_at IS NULL");
            $countRemainingstmt->execute([$loan_id]);

            $result = $countRemainingstmt->fetch();

            if($result['unreturned_count'] == 0){
                $updateloanstmt = $con->prepare("UPDATE loan SET loan_status = 'CLOSED' WHERE loan_id = ?");
                $updateloanstmt->execute([$loan_id]);
            }
            $con->commit();
            return true;
        }catch(PDOException $e){    
            if($con->inTransaction()){
                $con->rollBack();
            }
            throw $e;
        }
    }
}