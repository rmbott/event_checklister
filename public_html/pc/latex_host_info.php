<?php
    require_once("../../includes/initialize.php");
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
    \\node[opacity=1,inner sep=0pt] at (current page.center) {\includegraphics[width=\paperwidth,height=\paperheight]{latex/p1}};
\begin{tikzpicture}[overlay]
    \coordinate (name) at (3.4em,-2.1em);
    \coordinate (addr) at (5em,-4.3em);
    \coordinate (city) at (2.6em,-6.5em);
    \coordinate (state) at (17.8em,-6.5em);
    \coordinate (zip) at (23.3em,-6.5em);
    \coordinate (day_phone) at (5.8em,-8.7em);
    \coordinate (evening_phone) at (19.2em,-8.7em);
    \coordinate (cell_phone) at (3.3em,-11em);
    \coordinate (email) at (4em,-13.1em);
    \coordinate (date) at (35em,-2.1em);
    \coordinate (time) at (49.5em,-2.1em);
    ";

    function replace_trailing_clearpage($string) {
        return str_lreplace($string, "\clearpage", "");
    }

    $pages = "";
    $footer = "\\end{document}";
    $hash = md5(uniqid(mt_rand(), true));
    $file = "latex/{$hash}.tex";

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
            $name = substr($cur['contact']->first_name,0,18) . " " . substr($cur['contact']->last_name,0,18);
            $addr = substr($cur['contact']->addr1,0,20) . (!empty($cur['contact']->addr2) ? " ; " . substr($cur['contact']->addr2,0,8) : "") . (!empty($cur['contact']->addr3) ? " ; " . substr($cur['contact']->addr3,0,3) : "");
            $time = new DateTime($cur['event']->time);

            // Position fields in overlay
            $nodes = "\\node[anchor=north west] at (name) {\large \\textbf{" . latex_escape($name) . "}};\n";
            $nodes .= "\\node[anchor=north west] at (addr) {\large \\textbf{" . latex_escape($addr) . "}};\n";
            $nodes .= "\\node[anchor=north west] at (city) {\large \\textbf{" . substr(latex_escape($cur['contact']->city), 0, 18) . "}};\n";
            $nodes .= "\\node[anchor=north west] at (state) {\large \\textbf{" . substr(latex_escape($cur['contact']->state),0,5) . "}};\n";
            $nodes .= "\\node[anchor=north west] at (zip) {\large \\textbf{" . substr(latex_escape($cur['contact']->zip),0,7) . "}};\n";
            $nodes .= "\\node[anchor=north west] at (day_phone) {\large \\textbf{" . substr(latex_escape($cur['contact']->day_phone),0,13) . "}};\n";
            $nodes .= "\\node[anchor=north west] at (evening_phone) {\large \\textbf{" . substr(latex_escape($cur['contact']->evening_phone),0,13) . "}};\n";
            $nodes .= "\\node[anchor=north west] at (cell_phone) {\large \\textbf{" . substr(latex_escape($cur['contact']->cell_phone),0,13) . "}};\n";
            $nodes .= "\\node[anchor=north west] at (email) {\large \\textbf{" . substr(latex_escape($cur['contact']->email), 0, 36) . "}};\n";
            $nodes .= "\\node[anchor=north west] at (date) {\large \\textbf{" . latex_escape($cur['event']->date) . "}};\n";
            $nodes .= "\\node[anchor=north west] at (time) {\large \\textbf{" . latex_escape($time->format('g:ia')) . "}};\n";
            $pages .= $page_static_head . $nodes . " \\end{tikzpicture}\n\n\clearpage\n\n";
        }

        // Cobble the LaTex file together
        $pages = replace_trailing_clearpage($pages);
        $tex = $header . $pages . $footer;

        // Build the PDF
        file_put_contents($file, $tex);
        $out = exec('pdflatex -synctex=1 -interaction=nonstopmode -output-directory=latex ' . $file);
        $out = exec('pdflatex -synctex=1 -interaction=nonstopmode -output-directory=latex ' . $file);

        // The temporary files ought to be deleted here
        
        redirect_to("latex/{$hash}.pdf");
    }
?>
