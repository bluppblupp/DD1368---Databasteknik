/*
VÄRT ATT TESTA MOT DATABSEN HELA TIDEN OCH ÄNDRA DÄR DÅ MAN KAN SE LIVE FEEDBACK LIKSOM



1.Present a table that shows the sum of books each student has borrowed from each genre. 
a.Example:Student Name | Genre    | Amount borrowedLucas   | Fantasy | 3Lucas   | Sci-fi    | 0 */

SELECT fullName AS "Student Name", count(*) AS "Amount borrowed" 
FROM borrowlist, students, resources, books

WHERE borrowlist.userid = students.userid
AND borrowlist.resourceid = resources.resourceid
AND books.isbn = resources.isbn

GROUP BY students.fullname
ORDER BY students.fullName, "Amount borrowed";

/*

2. Present a table that shows for each student their preferred genre of choice based on
their previous borrowed books.

You are not expected to present a genre for students who have not previously
borrowed a book, they can be excluded from this table. Students with no clear
preference can be presented with any genre from their previously borrowed books.
 
a. Example:
Student Name | Genre
Donald | Philosophy */

select fullname, max(genre) as "Genre"
from books
         inner join
     (select fullname, isbn
      from students
               natural join
           (select userid, isbn
            from borrowlist
                     inner join resources b on borrowlist.resourceid = b.resourceid
            where returndate IS NOT NULL)
               AS "id+isbn") AS "name+isbn" on books.isbn = "name+isbn".isbn
GROUP BY fullname ORDER BY fullname desc;

/*

3. Present a table that for each book shows the amount of students that have at some
point borrowed the book. Sort the table by the highest to lowest amount of borrowed
books.
a. Example:
Book | Times borrowed
Oliver Twist | 5 */

SELECT title, count(resourceid) as "Times Borrowed" FROM books,
       (SELECT isbn, resources.resourceID from resources,

(SELECT resourceid, students.userid from borrowlist, students
where borrowlist.userid = students.userid) as "resourceid+userid"

where "resourceid+userid".resourceid = resources.resourceid) as "resourceid+isbn" WHERE "resourceid+isbn".isbn = books.isbn GROUP BY title, edition
ORDER BY "Times Borrowed" desc;


/*

4. Present a table that shows a monthly report for the number of books
borrowed/returned for each week (for example week 1-4)
a. Example:
Week | Borrowed | Returned | Missing
4 | 10 | 5 | 5 */

select extract('week' from borrowdate) as week, count(borrowid) as Borrowed, count(returndate) as returned
from borrowlist
where borrowdate between current_date - interval '1 month' and current_date
group by week
order by week asc;2


/*


5. Present a table that shows the 5 most fined programs at KTH according to the
borrowed books by their students.
a. Example:
Program | Fine | Rank
Interactive Media Technology | 29 | 1 */

SELECT students.Programme, count(amount) AS fine, rank() OVER (ORDER BY count(amount)DESC)as Rank
FROM fines, borrowlist, students
WHERE fines.borrowid = borrowlist.borrowid
AND borrowlist.userid = students.userid
GROUP BY students.programme;

/*

6. Present a table that shows the top 3 borrowed books for each publisher, also showing
their corresponding rank.
a. Example:
Book | Publisher | Times borrowed | Rank
Pippi Långstrump | Rabén & Sjögren | 20 | 1
Emil i Lönneberga | Rabén & Sjögren | 15 | 2
.
.
.
A study in scarlet | Ward Lock & Co | 5 | 1
*/

SELECT * FROM 

(SELECT resources.title AS "Book", publisher.name AS "Publisher", count(resources.resourceid) 

AS "Times Borrowed", rank() OVER (PARTITION BY publisher.name ORDER BY count(resources.resourceid)DESC) as Rank

FROM resources, publisher, bookpublisher, borrowlist

WHERE borrowlist.resourceid = resources.resourceid
AND resources.isbn = bookpublisher.isbn
AND bookpublisher.publisherid = publisher.publisherid


GROUP BY resources.title, publisher.name) as complete


WHERE Rank <= 3;




/*

7. Present a table that shows the top 10% of students with the highest sum of fines, also
showing their corresponding rank.
Make sure that your query is adaptable to tables with varying amounts of data.
a. Example:
Student Name| Total fine | Rank
Lisa | 1000 | 1
Bolívar | 760 | 2
*/


SELECT *

FROM
     (SELECT students.fullname as "Student Name", sum(amount) AS "Total fine", rank() OVER (ORDER BY sum(amount)DESC)as Rank
FROM fines, borrowlist, students

WHERE fines.borrowid = borrowlist.borrowid
AND borrowlist.userid = students.userid


GROUP BY students.fullname
ORDER BY SUM(amount) DESC) as complete

LIMIT
    (SELECT ceil(count(*)*0.1) FROM

        (SELECT students.fullname as "Student Name", sum(amount) AS "Total fine", rank() OVER (ORDER BY sum(amount)DESC)as Rank
FROM fines, borrowlist, students

WHERE fines.borrowid = borrowlist.borrowid
AND borrowlist.userid = students.userid


GROUP BY students.fullname
ORDER BY SUM(amount) DESC) AS complete2)


SELECT students.fullname AS "Student Name", fines.amount AS "Total Fine", RANK() OVER (ORDER BY fines.amount DESC)

FROM students, borrowlist, fines, users

WHERE students.userid = users.userid

AND fines.borrowid = borrowlist.borrowid
AND borrowlist.userid = students.userid
ORDER BY rank
LIMIT(SELECT (COUNT(*)::decimal) *0.1 FROM students);


/*
8. For each book series, present a table that shows the name of each book and its
sequel(s) and prequel(s) which is shown with arrows. The table should present the
series, the total length of the series and the total number of pages.
a. Example:
The Lord of the Rings: The Fellowship of the Ring => The Two Towers =>
The Return of the King | 3 | 9,250
*/



WITH RECURSIVE series AS 
(

--Anchor
SELECT title, isbn, series, prequel, pages
FROM books, series
WHERE books.isbn = series.isbn
) 

UNION ALL

SELECT books.title, books.isbn, series.serieid, 

--Recursive member
SELECT series AS "Series",
string_agg(title, '=>') AS "Books",
COUNT(title) AS "Books in Series",
SUM(pages) AS "Pages"
FROM series
GROUP BY series;



WITH RECURSIVE serie AS (
SELECT title, isbn, series, orderinserie, pages
FROM books, series
WHERE series.orderinserie IS NULL
AND series.serieid IS NOT NULL
UNION
SELECT books.title, books.isbn, series.serieid, series.orderinserie, books.pages
FROM books, series
JOIN serie ON series.orderinserie = series.serieid
)
SELECT series AS "Series",
string_agg(title, ' → ') AS "Books",
COUNT(title) as "Books in series",
SUM(pages) AS "Pages"
FROM serie
GROUP BY series;































