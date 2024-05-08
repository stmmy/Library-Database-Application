# Library-Database-Application
Code for a full stack Library application utilizing PHP. There are SQL files to create the DB and populate it with Dummy data.
The Code allows for a student view, which allows the checking in/out of books, placing holds on books, paying fees, and reserving rooms.
There is also a librarian view, in which the librarian can create new books, edit a student's fees, and generate reports on various analytics.

The entire DB SQL schema creation is defined within the "mastersql" file as well as dummy data. There are two triggers also defined, one for disallowing users to checkout
books when they have fees and or overdue books, and another which will generate alerts for a librarian based on in-demand items.
