<?php
###########################################################
# class:		Jtext
# Description:	Simulates the joomla JText class for Wordpress       
# Version:      1.0
# Requires PHP: 7.2
# desription:	message file should be in map contents/data/
#				First define filename: jtext->textfile=.....
# Author:		Theo van der Greft
# Author URI:   https://www.pranamas.nl
###########################################################
namespace MDT;
class jtext 
{
    public function _($textfile,$code) 
	{
		$JLang = dirname( __FILE__ ) . '/../data/'.$self->textfile;
		$fp = fopen($textfile,"r");
		if($fp == false) { return($code); }
		$text = '';
		while(($line=fgets($fp)) !== false)
		{
			if(strpos($line, "="))
			{
				$line = str_replace(array("\n", "\r"), '', $line);
				$message = explode("=",$line);
				if($message[0] == $code) return($message[1]);
			}
		}
        return ($text);
    }
}
?>