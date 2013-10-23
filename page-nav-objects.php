<?php 

/*
 *  Page_Nav(ancestor,current)
 *
 *  Represents a list of instances of Nav_Page.
 *
 *  ancestor -- the wordpress page ID of the "ancestor" of the current page.
 *    all pages share the same ancestor
 *  current -- the wordpress page ID of the actual page in which this nav exists.
 *    used for visually specifying which page is selected.

 *  You can add Nav_Pages to a Page_Nav 
 *  and print out an HTML-formatted version of the entire nav structure.
 *
 */
class Page_Nav
{
    Public $ancestor ;       
    public $pages ; 
    public $current ; 
    public $current_parent_page ; 

    /*
     *  The ancestor is a WordPress page ID
     *  the parent is a Nav_Page object
     */
    public function __construct($ancestor, $current)
    {
	//echo "<p>constructing Page_Nav with ancestor $ancestor and current $current</p>\n" ; 
        $this->ancestor = $ancestor ; 
        $this->current = $current ; 
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
	$nav_page = new Nav_Page($page, $this->current) ; 
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
	      $indicator = false ; 
	      if ( $nav_page->ID == $this->current ) { 
	        $indicator = true ; 
	      }
 	      $html .= $nav_page->format($indicator) ; 
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
    public function __construct($page, $current)
    {
        $this->page = $page ; 
        $this->current = $current ; 
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
	    $this->subnav = new Page_Nav($this->parent, $this->current) ; 
	}
	$this->subnav->add_page($nav_page) ; 
    }


    public function format($current) 
    { 
      $attribute = "" ; 
      if ( $current ) { 
	  $attribute = "class='current'" ; 
      }
      $html = "<li $attribute><a href='$this->link'>$this->title</a>" ; 
 
      if ($this->subnav != NULL) { 
          $html .= $this->subnav->format() ; 
      }
 
      return $html . "</li>\n" ; 
    }
}
?>
