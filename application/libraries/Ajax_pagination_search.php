<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP 5.1.6 or newer
 *
 * @package		CodeIgniter
 * @author		EllisLab Dev Team
 * @copyright		Copyright (c) 2008 - 2014, EllisLab, Inc.
 * @copyright		Copyright (c) 2014 - 2015, British Columbia Institute of Technology (http://bcit.ca/)
 * @license		http://codeigniter.com/user_guide/license.html
 * @link		http://codeigniter.com
 * @since		Version 1.0
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * Pagination Class
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Pagination
 * @author		EllisLab Dev Team
 * @link		http://codeigniter.com/user_guide/libraries/pagination.html
 */
class Ajax_pagination_search {

	var $base_url			= ''; // The page we are linking to
	var $prefix				= ''; // A custom prefix added to the path.
	var $suffix				= ''; // A custom suffix added to the path.

	var $total_rows			=  0; // Total number of items (database results)
	var $per_page			= 10; // Max number of items you want shown per page
	var $num_links			=  2; // Number of "digit" links to show before/after the currently viewed page
	var $cur_page			=  2; // The current page being viewed
	var $use_page_numbers	= FALSE; // Use page number for segment instead of offset
	var $first_link			= '<<';
	var $next_link			= '&gt;';
	var $prev_link			= '&lt;';
	var $last_link			= '>>';
	var $uri_segment		= 3;
	var $full_tag_open		= '';
	var $full_tag_close		= '';
	var $first_tag_open		= '';
	var $first_tag_close	= '&nbsp;';
	var $last_tag_open		= '&nbsp;';
	var $last_tag_close		= '';
	var $first_url			= ''; // Alternative URL for the First Page.
	var $cur_tag_open		= '&nbsp;<strong>';
	var $cur_tag_close		= '</strong>';
	var $next_tag_open		= '&nbsp;';
	var $next_tag_close		= '&nbsp;';
	var $prev_tag_open		= '&nbsp;';
	var $prev_tag_close		= '';
	var $num_tag_open		= '&nbsp;';
	var $num_tag_close		= '';
	var $page_query_string	= TRUE;
	var $query_string_segment = '';
	var $display_pages		= TRUE;
	var $anchor_class		= '';

	/**
	 * Constructor
	 *
	 * @access	public
	 * @param	array	initialization parameters
	 */
	public function __construct($params = array())
	{
		if (count($params) > 0)
		{
			$this->initialize($params);
		}

		if ($this->anchor_class != '')
		{
			$this->anchor_class = 'class="'.$this->anchor_class.'" ';
		}

		log_message('debug', "Pagination Class Initialized");
	}

	// --------------------------------------------------------------------

	/**
	 * Initialize Preferences
	 *
	 * @access	public
	 * @param	array	initialization parameters
	 * @return	void
	 */
	function initialize($params = array())
	{
		if (count($params) > 0)
		{
			foreach ($params as $key => $val)
			{
				if (isset($this->$key))
				{
					$this->$key = $val;
				}
			}
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Generate the pagination links
	 *
	 * @access	public
	 * @return	string
	 */
	
	
	function create_links()
	{
	  // EDIT: ADDED THIS BECAUSE COULDN'T SEEM TO SET THIS ANYWHERE ELSE
	  if ($this->anchor_class != '')
	  {
		 $this->anchor_class = 'class="'.$this->anchor_class.'" ';
	  }

	  // If our item count or per-page total is zero there is no need to continue.
	  if ($this->total_rows == 0 OR $this->per_page == 0)
	  {
		 return '';
	  }

	  // Calculate the total number of pages
	  $num_pages = ceil($this->total_rows / $this->per_page);

	  // Is there only one page? Hm... nothing more to do here then.
	  if ($num_pages == 1)
	  {
		 return '';
	  }

	  // Determine the current page number.
	  $CI =& get_instance();

	  if ($CI->config->item('enable_query_strings') === TRUE OR $this->page_query_string === TRUE)
	  {
		 if ($CI->input->get($this->query_string_segment) != 0)
		 {
			$this->cur_page = $CI->input->get($this->query_string_segment);

			// Prep the current page - no funny business!
			$this->cur_page = (int) $this->cur_page;
		 }
	  }
	  else
	  {
		 if ($CI->uri->segment($this->uri_segment) != 0)
		 {
			$this->cur_page = $CI->uri->segment($this->uri_segment);

			// Prep the current page - no funny business!
			$this->cur_page = (int) $this->cur_page;
		 }
	  }

	  $this->num_links = (int)$this->num_links;

	  if ($this->num_links < 1)
	  {
		 show_error('Your number of links must be a positive number.');
	  }

	  if ( ! is_numeric($this->cur_page))
	  {
		 $this->cur_page = 1;
	  }

	  // Is the page number beyond the result range?
	  // If so we show the last page
	  if ($this->cur_page > $this->total_rows)
	  {
		 $this->cur_page = ($num_pages - 1);
	  }

	  // EDIT: DON'T NEED THIS THE WAY I'VE CHANGED IT
	  // $uri_page_number = $this->cur_page;
	  // $this->cur_page = floor(($this->cur_page/$this->per_page) + 1);

	  // EDIT: START OF MODIFIED START AND END TO WORK HOW I WANT
	  $totalLinks = ($this->num_links*2)+1;
	  if($totalLinks > ($this->total_rows/$this->per_page))
	  {
		 $totalLinks = ceil($this->total_rows/$this->per_page);
	  }
	  //first page
	  if($this->cur_page == 1)
	  {
		 $start = 1;
		 $end = $start + $totalLinks - 1;
	  }
	  //middle pages
	  elseif($this->cur_page + $this->num_links <= $num_pages && $this->cur_page - $this->num_links > 0)
	  {
		 $start = $this->cur_page - $this->num_links;
		 $end = $this->cur_page + $this->num_links;
	  }
	  //last couple of pages
	  elseif(($this->cur_page + $totalLinks) > $num_pages)
	  {
		 $start = $num_pages - $totalLinks + 1;
		 $end = $num_pages;
		 //check to see if this is in the first half of links so it doesn't jump the paging
		 if($this->cur_page <= $this->num_links)
		 {
			$start = 1;
			$end = $start + $totalLinks - 1;
		 }
	  }
	  //first couple of pages
	  elseif(($this->cur_page - $totalLinks) < 1)
	  {
		 $start = 1;
		 $end = $start + $totalLinks - 1;
	  }
	  // EDIT: END OF MODIFIED START AND END TO WORK HOW I WANT

	  // EDIT: CODEIGNITERS BASE PAGING SETUP SEE ABOVE FOR MY CHANGES
	  // $start = (($this->cur_page - $this->num_links) > 0) ? $this->cur_page - ($this->num_links - 1) : 1;
	  // $end   = (($this->cur_page + $this->num_links) < $num_pages) ? $this->cur_page + $this->num_links : $num_pages;

	  // Is pagination being used over GET or POST?  If get, add a per_page query
	  // string. If post, add a trailing slash to the base URL if needed
	  if ($CI->config->item('enable_query_strings') === TRUE OR $this->page_query_string === TRUE)
	  {
		 $this->base_url = rtrim($this->base_url).'&amp;'.$this->query_string_segment.'=';
	  }
	  else
	  {
		 $this->base_url = rtrim($this->base_url, '/') .'/';
	  }

	  // And here we go...
	  $output = '';

	  // Render the "First" link
	  // EDIT: CHANGED TO ALWAYS SHOW FIRST LINK AT LEAST
	  if  ($this->first_link !== FALSE AND $this->cur_page != 1)
	  {
		 $first_url = ($this->first_url == '') ? $this->base_url."1" : $this->first_url;
		$output .= $this->first_tag_open.'<a '.$this->anchor_class.' href="javascript:void(0);"  onclick="custompagination(1)">'.$this->first_link.'</a>'.$this->first_tag_close;
	  }
	  else
	  {
		 $output .= $this->cur_tag_open.$this->first_link.$this->cur_tag_close;
	  }

	  // Render the "previous" link
	  // EDIT: CHANGED TO ALWAYS SHOW PREVIOUS LINK AT LEAST
	  if  ($this->prev_link !== FALSE AND $this->cur_page != 1)
	  {
		 $i = $this->cur_page-1;

		 if ($i == 0 && $this->first_url != '')
		 {
			$output .= $this->prev_tag_open.'<a  '.$this->anchor_class.' href="javascript:void(0);"  onclick="custompagination(1)">'.$this->prev_link.'</a>'.$this->prev_tag_close;
		 }
		 else
		 {
			$i = ($i == 0) ? '' : $this->prefix.$i.$this->suffix;
			$output .= $this->prev_tag_open.'<a  '.$this->anchor_class.' href="javascript:void(0);"  onclick="custompagination(1)">'.$this->prev_link.'</a>'.$this->prev_tag_close;
		 }

	  }
	  else
	  {
		 $output .= $this->cur_tag_open.$this->prev_link.$this->cur_tag_close;
	  }

	  // EDIT: CHANGED THIS TO ALWAYS SHOW ALL LINKS WANTED EVEN IF ON FIRST PAGE
	  // Render the pages
	  if ($this->display_pages !== FALSE)
	  {
		 // Write the digit links
		 for ($loop = $start; $loop <= $end; $loop++)
		 {
			// EDIT: DON'T NEED THIS THE WAY I'VE CHANGED IT
			// $i = ($loop * $this->per_page) - $this->per_page;

			if ($loop >= 0)
			{
			   if ($this->cur_page == $loop)
			   {
				  $output .= $this->cur_tag_open.$loop.$this->cur_tag_close; // Current page
			   }
			   else
			   {
				  $n = ($loop == 0) ? '0' : $loop;

				  if ($n == '' && $this->first_url != '')
				  {
					 $output .= $this->num_tag_open.'<a  '.$this->anchor_class.' href="javascript:void(0);"  onclick="custompagination(1)">'.$loop.'</a>'.$this->num_tag_close;
				  }
				  else
				  {
					 $n = ($n == '') ? '' : $this->prefix.$n.$this->suffix;
if(empty($n)){ $n = 1;}
					 $output .= $this->num_tag_open.'<a  '.$this->anchor_class.'  href="javascript:void(0);"  onclick="custompagination('.$n.')">'.$loop.'</a>'.$this->num_tag_close;
				  }
			   }
			}
		 }
	  }

	  // Render the "next" link
	  // EDIT: CHANGED TO ALWAYS SHOW NEXT LINK AT LEAST
	  if ($this->next_link !== FALSE AND $this->cur_page < $num_pages)
	  {
		 $dsa = $this->cur_page+1;
		 $output .= $this->next_tag_open.'<a  '.$this->anchor_class.' href="javascript:void(0);"  onclick="custompagination('.$dsa.')">'.$this->next_link.'</a>'.$this->next_tag_close;
	  }
	  else
	  {
		 $output .= $this->cur_tag_open.$this->next_link.$this->cur_tag_close;
	  }

	  // Render the "Last" link
	  // EDIT: CHANGED TO ALWAYS SHOW LAST LINK AT LEAST
	  if ($this->last_link !== FALSE AND $this->cur_page != $num_pages)
	  {
		 $i = (($num_pages));
		$output .= $this->last_tag_open.'<a  '.$this->anchor_class.'  href="javascript:void(0);"  onclick="custompagination('.$i.')">'.$this->last_link.'</a>'.$this->last_tag_close;
	  }
	  else
	  {
		 $output .= $this->cur_tag_open.$this->last_link.$this->cur_tag_close;
	  }

	  // Kill double slashes.  Note: Sometimes we can end up with a double slash
	  // in the penultimate link so we'll kill all double slashes.
	  $output = preg_replace("#([^:])//+#", "\\1/", $output);

	  // Add the wrapper HTML if exists
	  $output = $this->full_tag_open.$output.$this->full_tag_close;

	  return $output;
	}
	
	
	//for custom fitment search tool
	function create_links_fitment()
	{
	  // EDIT: ADDED THIS BECAUSE COULDN'T SEEM TO SET THIS ANYWHERE ELSE
	  if ($this->anchor_class != '')
	  {
		 $this->anchor_class = 'class="'.$this->anchor_class.'" ';
	  }

	  // If our item count or per-page total is zero there is no need to continue.
	  if ($this->total_rows == 0 OR $this->per_page == 0)
	  {
		 return '';
	  }

	  // Calculate the total number of pages
	  $num_pages = ceil($this->total_rows / $this->per_page);

	  // Is there only one page? Hm... nothing more to do here then.
	  if ($num_pages == 1)
	  {
		 return '';
	  }

	  // Determine the current page number.
	  $CI =& get_instance();

	  if ($CI->config->item('enable_query_strings') === TRUE OR $this->page_query_string === TRUE)
	  {
		 if ($CI->input->get($this->query_string_segment) != 0)
		 {
			$this->cur_page = $CI->input->get($this->query_string_segment);

			// Prep the current page - no funny business!
			$this->cur_page = (int) $this->cur_page;
		 }
	  }
	  else
	  {
		 if ($CI->uri->segment($this->uri_segment) != 0)
		 {
			$this->cur_page = $CI->uri->segment($this->uri_segment);

			// Prep the current page - no funny business!
			$this->cur_page = (int) $this->cur_page;
		 }
	  }

	  $this->num_links = (int)$this->num_links;

	  if ($this->num_links < 1)
	  {
		 show_error('Your number of links must be a positive number.');
	  }

	  if ( ! is_numeric($this->cur_page))
	  {
		 $this->cur_page = 1;
	  }

	  // Is the page number beyond the result range?
	  // If so we show the last page
	  if ($this->cur_page > $this->total_rows)
	  {
		 $this->cur_page = ($num_pages - 1);
	  }

	  // EDIT: DON'T NEED THIS THE WAY I'VE CHANGED IT
	  // $uri_page_number = $this->cur_page;
	  // $this->cur_page = floor(($this->cur_page/$this->per_page) + 1);

	  // EDIT: START OF MODIFIED START AND END TO WORK HOW I WANT
	  $totalLinks = ($this->num_links*2)+1;
	  if($totalLinks > ($this->total_rows/$this->per_page))
	  {
		 $totalLinks = ceil($this->total_rows/$this->per_page);
	  }
	  //first page
	  if($this->cur_page == 1)
	  {
		 $start = 1;
		 $end = $start + $totalLinks - 1;
	  }
	  //middle pages
	  elseif($this->cur_page + $this->num_links <= $num_pages && $this->cur_page - $this->num_links > 0)
	  {
		 $start = $this->cur_page - $this->num_links;
		 $end = $this->cur_page + $this->num_links;
	  }
	  //last couple of pages
	  elseif(($this->cur_page + $totalLinks) > $num_pages)
	  {
		 $start = $num_pages - $totalLinks + 1;
		 $end = $num_pages;
		 //check to see if this is in the first half of links so it doesn't jump the paging
		 if($this->cur_page <= $this->num_links)
		 {
			$start = 1;
			$end = $start + $totalLinks - 1;
		 }
	  }
	  //first couple of pages
	  elseif(($this->cur_page - $totalLinks) < 1)
	  {
		 $start = 1;
		 $end = $start + $totalLinks - 1;
	  }
	  // EDIT: END OF MODIFIED START AND END TO WORK HOW I WANT

	  // EDIT: CODEIGNITERS BASE PAGING SETUP SEE ABOVE FOR MY CHANGES
	  // $start = (($this->cur_page - $this->num_links) > 0) ? $this->cur_page - ($this->num_links - 1) : 1;
	  // $end   = (($this->cur_page + $this->num_links) < $num_pages) ? $this->cur_page + $this->num_links : $num_pages;

	  // Is pagination being used over GET or POST?  If get, add a per_page query
	  // string. If post, add a trailing slash to the base URL if needed
	  if ($CI->config->item('enable_query_strings') === TRUE OR $this->page_query_string === TRUE)
	  {
		 $this->base_url = rtrim($this->base_url).'&amp;'.$this->query_string_segment.'=';
	  }
	  else
	  {
		 $this->base_url = rtrim($this->base_url, '/') .'/';
	  }

	  // And here we go...
	  $output = '';

	  // Render the "First" link
	  // EDIT: CHANGED TO ALWAYS SHOW FIRST LINK AT LEAST
	  if  ($this->first_link !== FALSE AND $this->cur_page != 1)
	  {
		 $first_url = ($this->first_url == '') ? $this->base_url."1" : $this->first_url;
		$output .= $this->first_tag_open.'<a '.$this->anchor_class.' href="javascript:void(0);"  onclick="custompaginationfitment(1)">'.$this->first_link.'</a>'.$this->first_tag_close;
	  }
	  else
	  {
		 $output .= $this->cur_tag_open.$this->first_link.$this->cur_tag_close;
	  }

	  // Render the "previous" link
	  // EDIT: CHANGED TO ALWAYS SHOW PREVIOUS LINK AT LEAST
	  if  ($this->prev_link !== FALSE AND $this->cur_page != 1)
	  {
		 $i = $this->cur_page-1;

		 if ($i == 0 && $this->first_url != '')
		 {
			$output .= $this->prev_tag_open.'<a  '.$this->anchor_class.' href="javascript:void(0);"  onclick="custompaginationfitment(1)">'.$this->prev_link.'</a>'.$this->prev_tag_close;
		 }
		 else
		 {
			$i = ($i == 0) ? '' : $this->prefix.$i.$this->suffix;
			$output .= $this->prev_tag_open.'<a  '.$this->anchor_class.' href="javascript:void(0);"  onclick="custompaginationfitment('.$i.')">'.$this->prev_link.'</a>'.$this->prev_tag_close;
		 }

	  }
	  else
	  {
		 $output .= $this->cur_tag_open.$this->prev_link.$this->cur_tag_close;
	  }

	  // EDIT: CHANGED THIS TO ALWAYS SHOW ALL LINKS WANTED EVEN IF ON FIRST PAGE
	  // Render the pages
	  if ($this->display_pages !== FALSE)
	  {
		 // Write the digit links
		 for ($loop = $start; $loop <= $end; $loop++)
		 {
			// EDIT: DON'T NEED THIS THE WAY I'VE CHANGED IT
			// $i = ($loop * $this->per_page) - $this->per_page;

			if ($loop >= 0)
			{
			   if ($this->cur_page == $loop)
			   {
				  $output .= $this->cur_tag_open.$loop.$this->cur_tag_close; // Current page
			   }
			   else
			   {
				  $n = ($loop == 0) ? '0' : $loop;

				  if ($n == '' && $this->first_url != '')
				  {
					 $output .= $this->num_tag_open.'<a  '.$this->anchor_class.' href="javascript:void(0);"  onclick="custompaginationfitment(1)">'.$loop.'</a>'.$this->num_tag_close;
				  }
				  else
				  {
					 $n = ($n == '') ? '' : $this->prefix.$n.$this->suffix;
if(empty($n)){ $n = 1;}
					 $output .= $this->num_tag_open.'<a  '.$this->anchor_class.'  href="javascript:void(0);"  onclick="custompaginationfitment('.$n.')">'.$loop.'</a>'.$this->num_tag_close;
				  }
			   }
			}
		 }
	  }

	  // Render the "next" link
	  // EDIT: CHANGED TO ALWAYS SHOW NEXT LINK AT LEAST
	  if ($this->next_link !== FALSE AND $this->cur_page < $num_pages)
	  {
		 $dsa = $this->cur_page+1;
		 $output .= $this->next_tag_open.'<a  '.$this->anchor_class.' href="javascript:void(0);"  onclick="custompaginationfitment('.$dsa.')">'.$this->next_link.'</a>'.$this->next_tag_close;
	  }
	  else
	  {
		 $output .= $this->cur_tag_open.$this->next_link.$this->cur_tag_close;
	  }

	  // Render the "Last" link
	  // EDIT: CHANGED TO ALWAYS SHOW LAST LINK AT LEAST
	  if ($this->last_link !== FALSE AND $this->cur_page != $num_pages)
	  {
		 $i = (($num_pages));
		$output .= $this->last_tag_open.'<a  '.$this->anchor_class.'  href="javascript:void(0);"  onclick="custompaginationfitment('.$i.')">'.$this->last_link.'</a>'.$this->last_tag_close;
	  }
	  else
	  {
		 $output .= $this->cur_tag_open.$this->last_link.$this->cur_tag_close;
	  }

	  // Kill double slashes.  Note: Sometimes we can end up with a double slash
	  // in the penultimate link so we'll kill all double slashes.
	  $output = preg_replace("#([^:])//+#", "\\1/", $output);

	  // Add the wrapper HTML if exists
	  $output = $this->full_tag_open.$output.$this->full_tag_close;

	  return $output;
	}
	
	// for product pagination in front end
	function create_links_product()
	{
	  // EDIT: ADDED THIS BECAUSE COULDN'T SEEM TO SET THIS ANYWHERE ELSE
	  if ($this->anchor_class != '')
	  {
		 $this->anchor_class = 'class="'.$this->anchor_class.'" ';
	  }

	  // If our item count or per-page total is zero there is no need to continue.
	  if ($this->total_rows == 0 OR $this->per_page == 0)
	  {
		 return '';
	  }

	  // Calculate the total number of pages
	  $num_pages = ceil($this->total_rows / $this->per_page);

	  // Is there only one page? Hm... nothing more to do here then.
	  if ($num_pages == 1)
	  {
		 return '';
	  }

	  // Determine the current page number.
	  $CI =& get_instance();

	  if ($CI->config->item('enable_query_strings') === TRUE OR $this->page_query_string === TRUE)
	  {
		 if ($CI->input->get($this->query_string_segment) != 0)
		 {
			$this->cur_page = $CI->input->get($this->query_string_segment);

			// Prep the current page - no funny business!
			$this->cur_page = (int) $this->cur_page;
		 }
	  }
	  else
	  {
		 if ($CI->uri->segment($this->uri_segment) != 0)
		 {
			$this->cur_page = $CI->uri->segment($this->uri_segment);

			// Prep the current page - no funny business!
			$this->cur_page = (int) $this->cur_page;
		 }
	  }

	  $this->num_links = (int)$this->num_links;

	  if ($this->num_links < 1)
	  {
		 show_error('Your number of links must be a positive number.');
	  }

	  if ( ! is_numeric($this->cur_page))
	  {
		 $this->cur_page = 1;
	  }

	  // Is the page number beyond the result range?
	  // If so we show the last page
	  if ($this->cur_page > $this->total_rows)
	  {
		 $this->cur_page = ($num_pages - 1);
	  }

	  // EDIT: DON'T NEED THIS THE WAY I'VE CHANGED IT
	  // $uri_page_number = $this->cur_page;
	  // $this->cur_page = floor(($this->cur_page/$this->per_page) + 1);

	  // EDIT: START OF MODIFIED START AND END TO WORK HOW I WANT
	  $totalLinks = ($this->num_links*2)+1;
	  if($totalLinks > ($this->total_rows/$this->per_page))
	  {
		 $totalLinks = ceil($this->total_rows/$this->per_page);
	  }
	  //first page
	  if($this->cur_page == 1)
	  {
		 $start = 1;
		 $end = $start + $totalLinks - 1;
	  }
	  //middle pages
	  elseif($this->cur_page + $this->num_links <= $num_pages && $this->cur_page - $this->num_links > 0)
	  {
		 $start = $this->cur_page - $this->num_links;
		 $end = $this->cur_page + $this->num_links;
	  }
	  //last couple of pages
	  elseif(($this->cur_page + $totalLinks) > $num_pages)
	  {
		 $start = $num_pages - $totalLinks + 1;
		 $end = $num_pages;
		 //check to see if this is in the first half of links so it doesn't jump the paging
		 if($this->cur_page <= $this->num_links)
		 {
			$start = 1;
			$end = $start + $totalLinks - 1;
		 }
	  }
	  //first couple of pages
	  elseif(($this->cur_page - $totalLinks) < 1)
	  {
		 $start = 1;
		 $end = $start + $totalLinks - 1;
	  }
	  // EDIT: END OF MODIFIED START AND END TO WORK HOW I WANT

	  // EDIT: CODEIGNITERS BASE PAGING SETUP SEE ABOVE FOR MY CHANGES
	  // $start = (($this->cur_page - $this->num_links) > 0) ? $this->cur_page - ($this->num_links - 1) : 1;
	  // $end   = (($this->cur_page + $this->num_links) < $num_pages) ? $this->cur_page + $this->num_links : $num_pages;

	  // Is pagination being used over GET or POST?  If get, add a per_page query
	  // string. If post, add a trailing slash to the base URL if needed
	  if ($CI->config->item('enable_query_strings') === TRUE OR $this->page_query_string === TRUE)
	  {
		 $this->base_url = rtrim($this->base_url).'&amp;'.$this->query_string_segment.'=';
	  }
	  else
	  {
		 $this->base_url = rtrim($this->base_url, '/') .'/';
	  }

	  // And here we go...
	  $output = '';

	  // Render the "First" link
	  // EDIT: CHANGED TO ALWAYS SHOW FIRST LINK AT LEAST
	  if  ($this->first_link !== FALSE AND $this->cur_page != 1)
	  {
		 $first_url = ($this->first_url == '') ? $this->base_url."1" : $this->first_url;
		$output .= $this->first_tag_open.'<a '.$this->anchor_class.' href="javascript:void(0);"  onclick="custompagination_search(1)">'.$this->first_link.'</a>'.$this->first_tag_close;
	  }
	  else
	  {
		 $output .= $this->cur_tag_open.$this->first_link.$this->cur_tag_close;
	  }

	  // Render the "previous" link
	  // EDIT: CHANGED TO ALWAYS SHOW PREVIOUS LINK AT LEAST
	  if  ($this->prev_link !== FALSE AND $this->cur_page != 1)
	  {
		 $i = $this->cur_page-1;

		 if ($i == 0 && $this->first_url != '')
		 {
			$output .= $this->prev_tag_open.'<a  '.$this->anchor_class.' href="javascript:void(0);"  onclick="custompagination_search(1)">'.$this->prev_link.'</a>'.$this->prev_tag_close;
		 }
		 else
		 {
			$i = ($i == 0) ? '' : $this->prefix.$i.$this->suffix;
			$output .= $this->prev_tag_open.'<a  '.$this->anchor_class.' href="javascript:void(0);"  onclick="custompagination_search('.$i.')">'.$this->prev_link.'</a>'.$this->prev_tag_close;
		 }

	  }
	  else
	  {
		 $output .= $this->cur_tag_open.$this->prev_link.$this->cur_tag_close;
	  }

	  // EDIT: CHANGED THIS TO ALWAYS SHOW ALL LINKS WANTED EVEN IF ON FIRST PAGE
	  // Render the pages
	  if ($this->display_pages !== FALSE)
	  {
		 // Write the digit links
		 for ($loop = $start; $loop <= $end; $loop++)
		 {
			// EDIT: DON'T NEED THIS THE WAY I'VE CHANGED IT
			// $i = ($loop * $this->per_page) - $this->per_page;

			if ($loop >= 0)
			{
			   if ($this->cur_page == $loop)
			   {
				  $output .= $this->cur_tag_open.$loop.$this->cur_tag_close; // Current page
			   }
			   else
			   {
				  $n = ($loop == 0) ? '0' : $loop;

				  if ($n == '' && $this->first_url != '')
				  {
					 $output .= $this->num_tag_open.'<a  '.$this->anchor_class.' href="javascript:void(0);"  onclick="custompagination_search(1)">'.$loop.'</a>'.$this->num_tag_close;
				  }
				  else
				  {
					 $n = ($n == '') ? '' : $this->prefix.$n.$this->suffix;
if(empty($n)){ $n = 1;}
					 $output .= $this->num_tag_open.'<a  '.$this->anchor_class.'  href="javascript:void(0);"  onclick="custompagination_search('.$n.')">'.$loop.'</a>'.$this->num_tag_close;
				  }
			   }
			}
		 }
	  }

	  // Render the "next" link
	  // EDIT: CHANGED TO ALWAYS SHOW NEXT LINK AT LEAST
	  if ($this->next_link !== FALSE AND $this->cur_page < $num_pages)
	  {
		 $dsa = $this->cur_page+1;
		 $output .= $this->next_tag_open.'<a  '.$this->anchor_class.' href="javascript:void(0);"  onclick="custompagination_search('.$dsa.')">'.$this->next_link.'</a>'.$this->next_tag_close;
	  }
	  else
	  {
		 $output .= $this->cur_tag_open.$this->next_link.$this->cur_tag_close;
	  }

	  // Render the "Last" link
	  // EDIT: CHANGED TO ALWAYS SHOW LAST LINK AT LEAST
	  if ($this->last_link !== FALSE AND $this->cur_page != $num_pages)
	  {
		 $i = (($num_pages));
		$output .= $this->last_tag_open.'<a  '.$this->anchor_class.'  href="javascript:void(0);"  onclick="custompagination_search('.$i.')">'.$this->last_link.'</a>'.$this->last_tag_close;
	  }
	  else
	  {
		 $output .= $this->cur_tag_open.$this->last_link.$this->cur_tag_close;
	  }

	  // Kill double slashes.  Note: Sometimes we can end up with a double slash
	  // in the penultimate link so we'll kill all double slashes.
	  $output = preg_replace("#([^:])//+#", "\\1/", $output);

	  // Add the wrapper HTML if exists
	  $output = $this->full_tag_open.$output.$this->full_tag_close;

	  return $output;
	}
}
// END Pagination Class

/* End of file Pagination.php */
/* Location: ./system/libraries/Pagination.php */