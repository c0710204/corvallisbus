<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Welcome extends CI_Controller {

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
	public function cron()
	{
		if ((!file_exists(__DIR__.'/flag.txt'))||(time()-file_get_contents(__DIR__.'/flag.txt')>1))
		{
			@file_put_contents(__DIR__.'/flag.txt',time());
			$idlist=array(
				10267,
				10312,
				//#6
				12859,
				11949,
				12863,
				11983,
				11996,
				12006,
				12010,
				12023,
				12034
				);
			foreach($idlist as $i)$this->businfo($i);
			@unlink(__DIR__.'/flag.txt');

		}
		else 
			echo "running...";
	}
	
	public function businfo($id)
	{
		$this->load->database();
		$target="http://www.corvallistransit.com/rtt/public/RoutePositionET.aspx?PlatformNo={id}"; 
		
		$remoteurl=str_replace('{id}',$id,$target);
		echo "<a href='$remoteurl'>$remoteurl</a><br>\n";
		$remote=file_get_contents($remoteurl);
		if (strpos($remote,"No arrivals in the next 30 minutes.")>0)return;
//		echo "<pre>$remote</pre>";
		$arr=array();
		preg_match('/<tr><td>(.?.?.?.?\d?\d?)<\/td><td><div class="divDestination">(.*?)<\/div><\/td><td>(\d*?)<\/td><\/tr>/i',$remote,$arr);
		$arr1=array();
		//var_dump($arr);
		if (!($arr))return;
		if (!(is_array($arr[0])))
			$arr=array($arr);
		foreach ($arr as $line)
		{
			if (count($line)==0)continue;
//			if ($line[2])continue;
			echo "$line[1]-$line[3]-$line[2]\n";
			$sql = "INSERT INTO `bus`.`bushistory` (`eta`, `station`, `bus`) VALUES (?,?,(select max(id) from route where name = ? ));";
			echo $this->db->query($sql, array($line[3], $id, $line[1]));
		}
		//
	}
}
