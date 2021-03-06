<?php
include('../inc/configuracion.inc');
header("Content-type: text/css");
?>
/*
* Base structure
*/

/* Move down content because we have a fixed navbar that is 50px tall */
body {
padding-top: 50px;
background-color: <?php echo COLORFON; ?>; /*Fondo*/
}


/*
* Global add-ons
*/

.sub-header {
padding-bottom: 10px;
border-bottom: 1px solid #eee;
}


/*
* Sidebar
*/

/* Hide for mobile, show later */
.sidebar {
display: none;
}
@media (max-width: 767px) {
.sidebar {
top: 60px;
bottom: 150px;
background-color: <?php echo COLORLAT; ?>
}
}
@media (min-width: 768px) {
.sidebar {
position: fixed;
top: 51px;
bottom: 0;
left: 0;
z-index: 1000;
display: block;
padding: 5px;
overflow-x: hidden;
overflow-y: auto; /* Scrollable contents if viewport is shorter than content. */
background-color: <?php echo COLORLAT; ?>;/*Lateral*/
border-right: 1px solid #eee;

}
}

/* Sidebar navigation */
.nav-sidebar {
margin-right: -6px; /* 20px padding + 1px border */
margin-bottom: 1px;
margin-left: -1px;
}
.nav-sidebar > li > a {
padding-right: 5px;
padding-left: 5px;
}
.nav-sidebar > .active > a {
color: #fff;
background-color: #428bca;
}


/*
* Main content
*/

.main {
padding: 10px;
}
@media (max-width: 767px) {
.main{
    margin-top: 50px;
}
}
@media (min-width: 768px) {
.main {
padding-right: 20px;
padding-left: 20px;
}
}
.main .page-header {
margin-top: 0;
}


/*
* Placeholder dashboard ideas
*/

.placeholders {
margin-bottom: 5px;
text-align: center;
}
.placeholders h4 {
margin-bottom: 0;
}
.placeholder {
margin-bottom: 5px;
}
.placeholder img {
display: inline-block;
border-radius: 25%;
}
