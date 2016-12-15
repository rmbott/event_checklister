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

    

    PDF-overlay dependencies: FPDF, FPDI, and cellfit.php

        FPDF        - for generating PDFs in PHP
        FPDI        - for overlaying an existing PDF with new content
        cellfit.php - fit text to FPDF cell
                      (http://www.fpdf.org/en/script/script62.php)

        FPDF is used to generate PDFs from dynamic PHP content. Unfortunately, 
        one cannot overlay an existing PDF using FPDF alone. To do this we use 
        FPDI. There exists a handy extension to FPDF I'll call "cellfit.php" 
        which automatically adjusts the font size and character spacing to fit 
        text to the CELLs that one works with in FPDF. Unfortunately, 
        cellfit.php needs to be modified to work with FPDI. But this 
        modification is simple:

            1. Include FPDI. (Add a "require_once('fpdi.php');" line)
            2. Where cellfit.php's "FPDF_CellFit" class extends FDPF by default, 
               change this to extend FPDI instead.

        Since these three dependencies have permissive licenses, they've been
        included in includes/FPDF.