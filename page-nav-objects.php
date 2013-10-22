<?php 

/*
 * Page_Nav
 *
 * Represents a list of instances of Nav_Page.
 *
 * You can add Nav_Pages to a Page_Nav and 
 * print out an HTML formatted version of the entire nav structure.
 */
class Page_Nav
{
    Public $ancestor ;       
    public $pages ; 
    public $current_parent_page ; 

    /*
     *  The ancestor is a WordPress page ID
     *  the parent is a Nav_Page object
     */
    public function __construct($ancestor)
    {
        $this->ancestor = $ancestor ; 
        $this->pages = NULL ;
        $this->current_parent_page = NULL ; 
    }

    /*
     * add_page(page)
     *
     * add the specified Nav_Page object to the list of pages.
     *
     * Returns the new number of pages in this Page_Nav.
     */
    public function add_page($nav_page)
    {
        if ( $this->pages == NULL ) { $this->pages = array() ; }
        return array_push($this->pages, $nav_page) ; 
    }

    /*
     * sort_page(page)
     * create a Nav_Page object from the specified WP page
     * and place it in the appropriate list--either the current Page_Nav, 
     * or a subnav of the current parent page
     */
    public function sort_page($page)
    {
	$nav_page = new Nav_Page($page) ; 
	//echo "<p>page is " . $nav_page->title . "</p>\n" ; 
	
        //  This is an instance of a good first-level page
	if ( $this->ancestor == $nav_page->parent ) 
	{
	   //echo "<p><b>Keeping this first-level page</b></p>\n" ; 
  	   $this->current_parent_page = $nav_page ; 
	   return $this->add_page($nav_page) ;
        } 

	//  This is an instance of a good second-level page
 	else if ( $this->current_parent_page && ($this->current_parent_page->ID == $nav_page->parent) )
        { 
	    //echo "<p><em>Keeping this second-level page</em></p>\n" ; 
	    return $this->current_parent_page->add_subnav_page($nav_page) ; 
	}

	else 
	{ 
	    //echo "<p>ignoring this page</p>\n" ; 
	}  
    }	    


    public function format() 
    { 
      $html = "<ul>\n" ;       
 
      if ($this->pages != NULL) 
      { 
          foreach ($this->pages as $nav_page) 
	  {
 	      $html .= $nav_page->format() ; 
	  }
      }
 
      return $html . "</ul>\n" ; 
    }
}


/*
 * Nav_Page
 *
 * Represents a page within a Page_Nav.
 * 
 * You can add a subnav to a page, which is an instance of Page_Nav.
 */
class Nav_Page
{
    /*
     * 
     */
    public function __construct($page)
    {
        $this->page = $page ; 
        $this->parent = $page->post_parent ; 
        $this->ID = $page->ID ; 
        $this->title = $page->post_title ; 
	$this->link = get_permalink($this->ID) ; 
	$this->subnav = NULL ; 
   }

    /*
     * add_subnav_page(nav_page)
     * 
     * add the specified nav_page to the 
     *
     */
    public function add_subnav_page($nav_page)
    {
	if ( $this->subnav == NULL ) 
	{ 
	    $this->subnav = new Page_Nav($this->parent) ; 
	}
	$this->subnav->add_page($nav_page) ; 
    }


    public function format() 
    { 
      $html = "<li><a href='$this->link'>$this->title</a>" ; 
 
      if ($this->subnav != NULL) { 
          $html .= $this->subnav->format() ; 
      }
 
      return $html . "</li>\n" ; 
    }
}
?>
