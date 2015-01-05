<?php

class Zip_file extends Archive{

	function __construct($name){
		parent::__construct($name);
		$this->options['type'] = "zip";
	}

	function test(){
		$flag = parent::test();
		if($flag!==true){
			return $flag;
		}
		if(function_exists('gzcompress')){
			return true;
		} else{
			return "Zip compress is not support.";
		}
	}

	function create_zip(){
		$files = 0;
		$offset = 0;
		$central = "";
		if(!empty ($this->options['sfx'])){
			if($fp = @fopen($this->options['sfx'], "rb")){
				$temp = fread($fp, filesize($this->options['sfx']));
				fclose($fp);
				$this->add_data($temp);
				$offset += strlen($temp);
				unset ($temp);
			} else{
				$this->error[] = "Could not open sfx module from {$this->options['sfx']}.";
			}
		}

		$pwd = getcwd();
		chdir($this->options['basedir']);

		foreach($this->files as $current){
			if($current['name'] == $this->options['name']){
				continue;
			}

			$timedate = explode(" ", date("Y n j G i s", $current['stat'][9]));
			$timedate = ($timedate[0] - 1980 << 25) | ($timedate[1] << 21) | ($timedate[2] << 16) | ($timedate[3] << 11) | ($timedate[4] << 5) | ($timedate[5]);

			$block = pack("VvvvV", 0x04034b50, 0x000A, 0x0000, (isset($current['method']) || $this->options['method'] == 0) ? 0x0000 : 0x0008, $timedate);

			if($current['stat'][7] == 0 && $current['type'] == 5){
				$block .= pack("VVVvv", 0x00000000, 0x00000000, 0x00000000, strlen($current['name2']) + 1, 0x0000);
				$block .= $current['name2'] . "/";
				$this->add_data($block);
				$central .= pack("VvvvvVVVVvvvvvVV", 0x02014b50, 0x0014, $this->options['method'] == 0 ? 0x0000 : 0x000A, 0x0000, (isset($current['method']) || $this->options['method'] == 0) ? 0x0000 : 0x0008, $timedate, 0x00000000, 0x00000000, 0x00000000, strlen($current['name2']) + 1, 0x0000, 0x0000, 0x0000, 0x0000, $current['type'] == 5 ? 0x00000010 : 0x00000000, $offset);
				$central .= $current['name2'] . "/";
				$files++;
				$offset += (31 + strlen($current['name2']));
			} else if($current['stat'][7] == 0){
				$block .= pack("VVVvv", 0x00000000, 0x00000000, 0x00000000, strlen($current['name2']), 0x0000);
				$block .= $current['name2'];
				$this->add_data($block);
				$central .= pack("VvvvvVVVVvvvvvVV", 0x02014b50, 0x0014, $this->options['method'] == 0 ? 0x0000 : 0x000A, 0x0000, (isset($current['method']) || $this->options['method'] == 0) ? 0x0000 : 0x0008, $timedate, 0x00000000, 0x00000000, 0x00000000, strlen($current['name2']), 0x0000, 0x0000, 0x0000, 0x0000, $current['type'] == 5 ? 0x00000010 : 0x00000000, $offset);
				$central .= $current['name2'];
				$files++;
				$offset += (30 + strlen($current['name2']));
			} else if($fp = @fopen($current['name'], "rb")){
				$temp = fread($fp, $current['stat'][7]);
				fclose($fp);
				$crc32 = crc32($temp);
				if(!isset($current['method']) && $this->options['method'] == 1){
					$temp = gzcompress($temp, $this->options['level']);
					$size = strlen($temp) - 6;
					$temp = substr($temp, 2, $size);
				} else{
					$size = strlen($temp);
				}
				$block .= pack("VVVvv", $crc32, $size, $current['stat'][7], strlen($current['name2']), 0x0000);
				$block .= $current['name2'];
				$this->add_data($block);
				$this->add_data($temp);
				unset ($temp);
				$central .= pack("VvvvvVVVVvvvvvVV", 0x02014b50, 0x0014, $this->options['method'] == 0 ? 0x0000 : 0x000A, 0x0000, (isset($current['method']) || $this->options['method'] == 0) ? 0x0000 : 0x0008, $timedate, $crc32, $size, $current['stat'][7], strlen($current['name2']), 0x0000, 0x0000, 0x0000, 0x0000, 0x00000000, $offset);
				$central .= $current['name2'];
				$files++;
				$offset += (30 + strlen($current['name2']) + $size);
			} else{
				$this->error[] = "Could not open file {$current['name']} for reading. It was not added.";
			}
		}

		$this->add_data($central);

		$this->add_data(pack("VvvvvVVv", 0x06054b50, 0x0000, 0x0000, $files, $files, strlen($central), $offset, !empty ($this->options['comment']) ? strlen($this->options['comment']) : 0x0000));

		if(!empty ($this->options['comment'])){
			$this->add_data($this->options['comment']);
		}

		chdir($pwd);

		return 1;
	}
}