<?php $cookie_time = time()+60*60*24*365*10;

if($_GET['orderby'] == 'date'){
    setcookie("OrderBy",$_COOKIE['orderby'] = "date", $cookie_time, "/", $vars->networkSite);
    $_COOKIE['OrderBy'] = 'date';
}
if($_GET['orderby'] == 'duedate'){
    setcookie("OrderBy",$_COOKIE['orderby'] = "duedate", $cookie_time, "/", $vars->networkSite);
    $_COOKIE['OrderBy'] = 'duedate';
}
if($_GET['orderby'] == 'title'){
    setcookie("OrderBy",$_COOKIE['orderby'] = "title", $cookie_time, "/", $vars->networkSite);
    $_COOKIE['OrderBy'] = 'title';
}
if($_GET['orderby'] == 'modified'){
    setcookie("OrderBy",$_COOKIE['orderby'] = "modified", $cookie_time, "/", $vars->networkSite);
    $_COOKIE['OrderBy'] = 'modified';
}
if($_GET['orderby'] == 'comment_count'){
    setcookie("OrderBy",$_COOKIE['orderby'] = "comment_count", $cookie_time, "/", $vars->networkSite);
    $_COOKIE['OrderBy'] = 'comment_count';
}
if($_GET['orderby'] == 'author'){
    setcookie("OrderBy",$_COOKIE['orderby'] = "author", $cookie_time, "/", $vars->networkSite);
    $_COOKIE['OrderBy'] = 'author';
}
if($_GET['orderby'] == 'rand'){
    setcookie("OrderBy",$_COOKIE['orderby'] = "rand", $cookie_time, "/", $vars->networkSite);
    $_COOKIE['OrderBy'] = 'rand';
}
if($_GET['order'] == 'asc'){
    setcookie("Order",$_COOKIE['order'] = "asc", $cookie_time, "/", $vars->networkSite);
    $_COOKIE['Order'] = 'asc';
}
if($_GET['order'] == 'desc'){
    setcookie("Order",$_COOKIE['order'] = "desc", $cookie_time, "/", $vars->networkSite);
    $_COOKIE['Order'] = 'desc';
}
?>