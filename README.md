Event Checklister 
    An event tracking and task management app. Includes additional features for 
    MLM consultants.

Ryan Bott
ryan@rmbott.com


Current Features
    -Multiple users
    -Event C.R.U.D.
    -Import contacts from .csv file
    -Create PDF checklists for one or more events

Getting Started

    Database:
        
        Use the included .sql files to initialize the MySQL database with some 
        test data. See the comments therein for specifics, or you can run the 
        following two commands from the project directory:
            
            mysql -u root -p < db_create.sql > log.txt
            mysql -u ec_admin -psecretpassword ecdb < db_tables.sql > log.txt

        If you are not using the default setup you will probably want to change 
        the following file accordingly:

            includes/db_config.php



    Path configuration:

        Edit the following files (which contain absolute paths) to fit your 
        particular system:
            
            includes/initialize.php



    Permissions:

        Your webserver will need permissions to write to the following folders:
                
            public_html/pc/uploads
            public_html/pc/latex
        
        ... so, for example, if you use apache you can execute the following to 
        determine the what user it is running as:

            ps -aux | grep apache

        For me it was "www-data". To give it write permissions execute: 

            sudo chown www-data public_html/pc/uploads
            sudo chown www-data public_html/pc/latex

    

    LaTeX and Tikz:

        LaTeX, pdflatex, and the Tikz package for LaTeX are required. If you are 
        this running on a hosted site that doesn't have Tikz, you'll have to 
        copy all of the individual files from the package into the 
        public_html/pc/latex directory.