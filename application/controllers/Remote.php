<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Remote extends CI_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see http://codeigniter.com/user_guide/general/urls.html
	 */
	public function index()
	{
		$this->load->view('welcome_message');
	}
	public function stationinfo($id)
	{
		$this->load->database();
		$target="http://www.corvallistransit.com/rtt/public/RoutePositionET.aspx?PlatformNo={id}"; 
		
		$remoteurl=str_replace('{id}',$id,$target);
		echo "<a href='$remoteurl'>$remoteurl</a><br>";
		$remote=file_get_contents($remoteurl);
//		echo "<pre>$remote</pre>";
		$arr=array();
		preg_match('/<tr><td>(.?.?.?.?\d?\d?)<\/td><td><div class="divDestination">(.*)<\/div><\/td><td>(\d*)<\/td><\/tr>/i',$remote,$arr);
		$arr1=array();
		if (!($arr))return;
		if (!(is_array($arr[0])))
			$arr=array($arr);
		foreach ($arr as $line)
		{
			if (count($line)==0)continue;
			echo "$line[1] $line[3] $line[2]\n";
			$sql = "INSERT INTO `bus`.`bushistory` (`eta`, `station`, `bus`) VALUES (
			(select max(id) from route where name = ? )
			, ?, ?);";
			$this->db->query($sql, array($line[3], $id, $line[1]));
		}
		var_dump($arr);
	}
}