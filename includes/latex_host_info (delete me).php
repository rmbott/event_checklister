<?php

    require_once("initialize.php");

    /*
     * Generates event information PDf - one page for each event selected in the
     * dashboard.php page. Uses LaTex to overlay event information on a  
     * template which is based entirely on Pampered Chef's "Host Information 
     * Checklist".
     * 
     * The resulting .tex file is converted to a PDF using pdflatex.
     */
    
    // LaTeX file header
    $header = "\documentclass{article}
\usepackage{tikz}
\usepackage[top=2cm, bottom=2cm, outer=0cm, inner=0cm]{geometry}
\\renewcommand{\\familydefault}{\sfdefault}
\pagenumbering{gobble}
\begin{document}
";
    // LaTeX header for each page
    $page_static_head = "\\tikz[remember picture, overlay] 
    \\node[opacity=1,inner sep=0pt] at (current page.center) {\includegraphics[width=\paperwidth,height=\paperheight]{../public_html/pc/latex/p1}};
\begin{tikzpicture}[overlay]
    \coordinate (name) at (3.1em,-1.4em);
    \coordinate (addr) at (5em,-3.6em);
    \coordinate (city) at (2.6em,-5.9em);
    \coordinate (state) at (17.8em,-5.9em);
    \coordinate (zip) at (22.3em,-5.9em);
    \coordinate (day_phone) at (5.4em,-8.5em);
    \coordinate (evening_phone) at (18.8em,-8.5em);
    \coordinate (cell_phone) at (4em,-10.8em);
    \coordinate (email) at (4em,-12.5em);
    \coordinate (date) at (35em,-1.3em);
    \coordinate (time) at (48.7em,-1.3em);
    ";

    function replace_trailing_clearpage($string) {
        return str_lreplace($string, "\clearpage", "");
    }

    $pages = "";
    $footer = "\\end{document}";
    $hash = md5(uniqid(mt_rand(), true));
    $file = "../public_html/pc/latex/{$hash}.tex";

    if (isset($_POST)) {

        // Get Events from ids POSTed from corresponding DashboardElements
        $events = [];
        foreach ($_POST AS $id => $value) {
            if ($value == "on") {
                $events[] = Event::find_by_id($id);
            }
        }
        
        // Get primary contact info associated with the events ids
        foreach ($events AS $event) {
            $events_contacts[] = ["event" => $event, "contact" => $event->get_primary_contact()];
        }

        // Insert overlay content - longer content gets trimmed so it doesn't 
        // bleed over on to the template in the background.
        foreach ($events_contacts as $cur) {
            // Prepare some fields for overlay
            $name = $cur['contact']->first_name . " " . $cur['contact']->last_name;
            $addr = $cur['contact']->addr1 . (!empty($cur['contact']->addr2) ? " ; " . $cur['contact']->addr2 : "") . (!empty($cur['contact']->addr3) ? " ; " . $cur['contact']->addr3 : "");
            $time = new DateTime($cur['event']->time);

            // Position fields in overlay
            $nodes = "\\node[anchor=north west] at (name) {\huge \\textbf{" . substr(latex_escape($name), 0, 21) . "}};\n";
            $nodes .= "\\node[anchor=north west] at (addr) {\huge \\textbf{" . substr(latex_escape($addr), 0, 20) . "}};\n";
            $nodes .= "\\node[anchor=north west] at (city) {\huge \\textbf{" . substr(latex_escape($cur['contact']->city), 0, 10) . "}};\n";
            $nodes .= "\\node[anchor=north west] at (state) {\huge \\textbf{" . latex_escape($cur['contact']->state) . "}};\n";
            $nodes .= "\\node[anchor=north west] at (zip) {\huge \\textbf{" . latex_escape($cur['contact']->zip) . "}};\n";
            $nodes .= "\\node[anchor=north west] at (day_phone) {\large \\textbf{" . latex_escape($cur['contact']->day_phone) . "}};\n";
            $nodes .= "\\node[anchor=north west] at (evening_phone) {\large \\textbf{" . latex_escape($cur['contact']->evening_phone) . "}};\n";
            $nodes .= "\\node[anchor=north west] at (cell_phone) {\large \\textbf{" . latex_escape($cur['contact']->cell_phone) . "}};\n";
            $nodes .= "\\node[anchor=north west] at (email) {\huge \\textbf{" . substr(latex_escape($cur['contact']->email), 0, 21) . "}};\n";
            $nodes .= "\\node[anchor=north west] at (date) {\huge \\textbf{" . latex_escape($cur['event']->date) . "}};\n";
            $nodes .= "\\node[anchor=north west] at (time) {\huge \\textbf{" . latex_escape($time->format('g:ia')) . "}};\n";
            $pages .= $page_static_head . $nodes . " \\end{tikzpicture}\n\n\clearpage\n\n";
        }

        // Cobble the LaTex file together
        $pages = replace_trailing_clearpage($pages);
        $tex = $header . $pages . $footer;

        // Build the PDF
        file_put_contents($file, $tex);
        $out = exec('pdflatex -synctex=1 -interaction=nonstopmode -output-directory=../public_html/pc/latex ' . $file);
        $out = exec('pdflatex -synctex=1 -interaction=nonstopmode -output-directory=../public_html/pc/latex ' . $file);

        // The temporary files ought to be deleted here
        
        redirect_to("../public/latex/{$hash}.pdf");
    }
?>
