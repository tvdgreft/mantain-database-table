<?php
namespace MDT;
class pranaform
{
	# default values
	#
	public $width="100%";
	public $heigth="25px";
	public $readonly = FALSE;
	public $collabel="col-md-2";
	public $colinput="col-md-6";
	public $col="col-md-3";
	public $required= TRUE;
	public $inline = TRUE;
	public $uploaderror = "";
	public $row = TRUE;
	#
	# functions for forms
	#
	# Text	- text input
	# "label"=>			label of input boxt
	# "id"=>			name and id of object
	# "checkclass"=>	classname if there is a jscript checker
	# "row"=>			TRUE = label and input in one row.
	#
	public function Text($args)
	{
		if($this->required) { $args["label"] .= "*"; }
		$id=$args["id"];
		$value = isset($args["value"]) ? $args["value"] : '';
		$error = isset($args['error']) ? $args['error'] : '';			 # error if input error
		$row = isset($args['row']) ? $args['row'] : $this->row;
		$col = isset($args['col']) ? $args['col'] : $this->col;	
		$width = isset($args['width']) ? $args['width'] : $this->width;	
		$checkclass = isset($args["check"]) ? ' ' . $args["check"] : ''; # javascript test input on this class
		$html='';
		if($row == TRUE) 
		{
			$html .= '<div class="form-group row">'; 
			$html .= '<div class="' . $this->collabel .'">';
		}
		else 
		{ 
			$html .= '<div class="' . $col . '">';
			$html .= '<div class="control-label">';
		}
		$html .= '<label for="' . $args["id"] . '"';
		if(isset($args["popover"])) { $html .= ' class="hasPopover"  title="' . $args["popover"] . '"'; }
		$html .= '>' .  $args["label"] . '</label>';
		$html .= '</div>';
		if($row == TRUE) { $html .= '<div class="' . $this->colinput . '">'; }
		else { $html .= '<div class="controls">';}
		$html .= 	'<input class="form-control' . $checkclass .'" type="text" id="' . $id . '" name="' . $id . '" value="' . $value . '"';
		$html .= ' style="width:' . $this->width . '"';
		if($this->required) { $html .= ' required="required"'; }
		if($this->readonly) { $html .= ' readonly="readonly"'; }
		if(isset($args["placeholder"])) $html .= 'placeholder="' . $args["placeholder"] . '"';
		$html .= '>';
		$html .= '<span class="error_hide">'.$error.'</span>';
		$html .= '</div>';
		$html .= '</div>';
		return($html);
	}
	/*
	public function TextBox($args)
	{
		if($this->required) { $args["label"] .= "*"; }
		$id=$args["id"];
		$value = isset($args["value"]) ? $args["value"] : '';
		$error = isset($args['error']) ? $args['error'] : '';			 # error if input error
		$row = isset($args['row']) ? $args['row'] : $this->row;
		$col = isset($args['col']) ? $args['col'] : $this->col;	
		$checkclass = isset($args["check"]) ? ' ' . $args["check"] : ''; # javascript test input on this class
		#
		$html='';
		if($row == TRUE) 
		{
			$html .= '<div class="form-group row">'; 
			$html .= '<div class="' . $this->collabel .'">';
		}
		else 
		{ 
			$html .= '<div class="' . $col . '">';
			$html .= '<div class="control-label">';
		}
		$html .= '<label for="' . $args["id"] . '"';
		if(isset($args["popover"])) { $html .= ' class="hasPopover"  title="' . $args["popover"] . '"'; }
		$html .= '>' .  $args["label"] . '</label>';
		$html .= '</div>';
		if($row == TRUE) { $html .= 	'<div class="' . $this->colinput . '">'; }
		else { $html .= '<div class="controls">';}
		$html .= '<input class="form-control' . $checkclass .'" type="text" id="' . $id . '" name="' . $id . '" value="' . $value . '"';
		$html .= ' style="width:100%" ';
		
		if($this->required) { $html .= ' required="required"'; }
		if($this->readonly) { $html .= ' readonly="readonly"'; }
		if(isset($args["placeholder"])) $html .= 'placeholder="' . $args["placeholder"] . '"';
		$html .= '>';
		$html .= '<span class="error_hide">'.$error.'</span>';
		$html .= '</div>';
		$html .= '</div>';
		return($html);
	}
	public function TextBox($args)
	{
		$r='';
		if(!isset($args["value"])) { $args["value"] = ""; }
		if(isset($args["required"])) { $args["label"] .= "*"; }
		$r .= '<div class="' . $args["col"] .'">';
		$r .= '<div class="control-label">';
		$r .= '<label for="' . $args["id"] . '"';
		if(isset($args["popover"])) { $r .= ' class="hasPopover"  title="' . $args["popover"] . '"'; }
		$r .= '>' .  $args["label"] . '</label>';
		$r .= '</div>';
		$r .= '<div class="controls">';
		$r .= '<input type="text" id="' . $args["id"] . '" name="' . $args["id"] . '" value="' . $args["value"] . '"';
		$r .= ' "class="form-control"';
		if(isset($args["width"])) { $r .= ' style="width:' . $args["width"] . 'px;" '; }
		else { $r .= ' style="width:100%" '; }
		if(isset($args["required"]))  { $r .= ' required="required"'; }
		if(isset($args["placeholder"])) { $r.= ' placeholder="' . $args["placeholder"] . '"'; }
		if(isset($args["readonly"]) && $args["readonly"]) { $r .= ' readonly="readonly"'; }
		if(isset($args["onchange"])) { $r .= ' onchange="' . $args["onchange"] . '"'; }
		if(isset($args["autocompleteoff"])) { $r .= ' autocomplete="off"'; }
		if(isset($args["disabled"]) && $args["disabled"] == true) { $r .= ' disabled'; }
		$r .= '>';
		$r .= '</div>';
		$r .= '</div>';
		return($r);
	}
	public function TextOld($args)
	{
		if($this->required) { $args["label"] .= "*"; }
		$id=$args["id"];
		$value = isset($args["value"]) ? $args["value"] : '';
		$error = isset($args['error']) ? $args['error'] : '';			 # error if input error
		$checkclass = isset($args["check"]) ? ' ' . $args["check"] : ''; # javascript test input on this class
		$html='';
		$html .= '<div class="form-group row">';
		$html .= 	'<div class="' . $this->collabel .'">';
		$html .= 		'<label for="' . $args["id"] . '"';
		if(isset($args["popover"])) { $html .= ' class="hasPopover"  title="' . $args["popover"] . '"'; }
		$html .= 		'>' .  $args["label"] . '</label>';
		$html .=	'</div>';
		$html .= 	'<div class="' . $this->colinput . '">';
		$html .= 	'<input class="form-control' . $checkclass .'" type="text" id="' . $id . '" name="' . $id . '" value="' . $value . '"';
		$html .= ' style="width:' . $this->width . '"';
		if($this->required) { $html .= ' required="required"'; }
		if($this->readonly) { $html .= ' readonly="readonly"'; }
		$html .= isset($args["placeholder"]) ? '' : ' placeholder="' . $args["placeholder"] . '"';
		$html .= 	'>';
		$html .= 	'<span class="error_hide">'.$error.'</span>';
		$html .= 	'</div>';
		$html .= '</div>';
		return($html);
	*/
	public function TextArea($args)
	{
		$html='';
		if($this->required) { $args["label"] .= "*"; }
		$id=$args["id"];
		$heigth = isset($args["heigth"]) ? $args["heigth"] : $this->heigth;
		$error = isset($args['error']) ? $args['error'] : '';			 # error if input error
		$html .= '<div class="form-group row">';
		$html .= 	'<div class="' . $this->collabel .'">';
		$html .= 		'<label for="' . $args["id"] . '"';
		if(isset($args["popover"])) { $html .= ' class="hasPopover"  title="' . $args["popover"] . '"'; }
		$html .= '>' .  $args["label"] . '</label>';
		$html .= '</div>';
		$html .= 	'<div class="' . $this->colinput . '">';
		$html .= 	'<textarea class="form-control' . $checkclass .'" type="text" id="' . $id . '" name="' . $id . '"';
		$html .= 'style="width:' . $this->width . '; height:' . $heigth . ';"';
		if($this->required) { $html .= ' required="required"'; }
		if($this->readonly) { $html .= ' readonly="readonly"'; }
		$html .= isset($args["placeholder"]) ? '' : ' placeholder="' . $args["placeholder"] . '"';
		$html .= '>';
		$html .= $args["value"];
		$html .= '</textarea>';
		$html .= 	'<span class="error_hide">'.$error.'</span>';
		$html .= 	'</div>';
		$html .= '</div>';
		return($html);
	}
	#
	# RadioRow
	# inline=1 elements on 1 line
	# label=label text
	# id= name and id of object
	# options = array of options to choose e.g. array("man","vrouw","geen");
	# required = 1 if the box is required
	# col = bootstrap position
	# value = default value
	# readonly=cannot change 
	# 
	public function Radio($args)
	{
		if($this->required) { $args["label"] .= "*"; }
		$id=$args["id"];
		$html='';
		$html .= '<div class="form-group row">';
		$html .= 	'<div class="' . $this->collabel .'">';
		$html .= 		'<label for="radios">' . $args["label"] . '</label>';
		$html .= 	'</div>';
		$html .= 	'<div class="' . $this->colinput . '">';
		foreach ($args["options"] as $p)
		{
			$selected="";
			if($p == $args["value"]) { $selected = " checked";}
			$html .= '<div class="form-check">';
			$rid = $args["id"] . '_' . $p;
			$html .= 	'<input class="form-check-input" type="radio" id="' . $rid . '" name="' . $id . '" value="' . $p . '"' . $selected;
			if($this->readonly) { $html .= ' disabled="disabled"'; }
			$html .= '>';
			$html .= '<label class="form-check-label" for="'. $id . '">' . $p . '</label>';
			$html .=		'</div>';
		}
		$html .=		'</div>';
		$html .=		'</div>';
		return($html);
	}
	public function Checkboxes($args)
	{
		if($this->required) { $args["label"] .= "*"; } 
		$id=$args["id"];
		$html='';
		if($row == TRUE) 
		{
			$html .= '<div class="form-group row">'; 
			$html .= 	'<div class="' . $this->collabel .'">';
		}
		else 
		{ 
			$html .= '<div class="' . $this->col . '">';
			$html .= '<div class="control-label">';
		}
		$html .= 		'<label for="checkbox">' . $args["label"] . '</label>';
		$html .= 	'</div>';
		$html .= 	'<div class="' . $this->colinput . '">';
		foreach ($args["options"] as $p)
		{
			$selected="";
			if(in_array($p,$args["value"])) { $selected = " checked";}
			$html .= '<div class="form-check">';
			$rid = $args["id"] . '_' . $p;
			$html .= 	'<input class="form-check-input" type="checkbox" id="' . $rid . '" name="' . $id . '[]" value="' . $p . '"' . $selected;
			if($this->readonly) { $html .= ' disabled="disabled"'; }
			$html .= '>';
			$html .= '<label class="form-check-label" for="'. $args["id"] . '">' . $p . '</label>';
			$html .=		'</div>';
		}
		$html .=		'</div>';
		$html .=		'</div>';
		return($html);
	}
	public function Date($args)
	{
		if($this->required) { $args["label"] .= "*"; } 
		$id=$args["id"];
		$value = $args["value"];
		$dateformat= "yy-mm-dd";
		if(isset($args["euro"])) { $dateformat= "dd-mm-yy"; }
		$checkclass = isset($args["check"]) ? ' ' . $args["check"] : ''; # add check class if given so that javascript can test the content
		
		$html='';
		$html .= '<div class="form-group row">';
		$html .= 	'<div class="' . $this->collabel .'">';
		$html .= 		'<label for="' . $args["id"] . '"';
		if(isset($args["popover"])) { $html .= ' class="hasPopover"  title="' . $args["popover"] . '"'; }
		$html .= 		'>' .  $args["label"] . '</label>';
		$html .=	'</div>';
		$html .= 	'<div class="' . $this->colinput . '">';
		$html .= '<input class="form-control' . $checkclass . ' datepicker" type="text" id="' . $id . '" name="' . $id . '" style="width:' . $this->width . '" value="' . $value .'"';
		#
		# set event on classes checkclass and datepicker in jquery !!
		#
		if($this->required) { $html .= ' required="required"'; }
		if($this->readonly) { $html .= ' readonly="readonly"'; }
		if(isset($args["placeholder"])) { $html.= ' placeholder="' . $args["placeholder"] . '"'; }
		
		$html .= 	'>';
		$html .= 	'<span class="error_hide">'.$error.'</span>';
		$html .= 	'</div>';
		$html .= '</div>';
		return($html);
	}
	#
	# DropDown
	# label=label text
	# id= name and id of object
	# options = array of options to choose e.g. array("man","vrouw","geen");
	# value = default value
	# 
	public function Dropdown($args)
	{
		if($this->required) { $args["label"] .= "*"; }
		$id=$args["id"];
		$value = $args["value"];
		$html='';
		if($row == TRUE) 
		{
			$html .= '<div class="form-group row">'; 
			$html .= 	'<div class="' . $this->collabel .'">';
		}
		else 
		{ 
			$html .= '<div class="' . $this->col . '">';
			$html .= '<div class="control-label">';
		}
		$html .= 		'<label for="checkbox">' . $args["label"] . '</label>';
		$html .= 	'</div>';
		if($row == TRUE) { $html .= '<div class="' . $this->colinput . '">'; }
		else { $html .= '<div class="controls">';}
		
		
		
		$options = "";
		foreach ($args["options"] as $p)
		{
			$selected = $p == $args["value"] ? " selected=selected" : "";
			$options .= '<option value="' . $p . '"' . $selected . '>' . $p . '</option>';
		}
		#if(isset($args["popover"])) { $r .= ' class="hasPopover"  title="' . $args["popover"] . '"'; }
		$html .= '<select name="' . $id . '" style="padding:0px;width:' . $this->width . ';heigth:' . $this->heigth . ';"';
		if($this->required) { $html .= ' required="required"'; }
		if($this->readonly) { $html .= ' readonly="readonly"'; }
		$html .= '>';
		$html .= $options;
		$html .= '</select>';
		$html .= '</div>';
		$html .= '</div>';
		return($html);
	}
	#
	# search a file 
	# label = label of text box
	# id = id and name of textbox
	# width = widht of input box in pixels
	# required = 1 if the box is required
	# col = bootstrap position 
	# accept = Only accept certain files
	public function File($args)
	{
		$r='';
		if($this->required) { $args["label"] .= "*"; } 
		$id=$args["id"];
		$value=isset($args["value"]) ? $args["value"] : '';
		$checkclass = isset($args["check"]) ? ' ' . $args["check"] : ''; # javascript test input on this class
		$html .= '<div class="form-group row">';
		$html .= 	'<div class="' . $this->collabel .'">';
		$html .= 		'<label for="' . $args["id"] . '"';
		if(isset($args["popover"])) { $html .= ' class="hasPopover"  title="' . $args["popover"] . '"'; }
		$html .= 		'>' .  $args["label"] . '</label>';
		$html .=	'</div>';
		$html .= 	'<div class="' . $this->colinput . '">';
		if(isset($args["value"])) { $value=$args["value"]; }
		$html .= '<input type="file" id="' . $id . '" class="form-control ' . $checkclass .'" name="' . $id . '" value="' . $value . '"';
		
		
		$html .= ' style="width:' . $this->width . '"';
		if($this->required) { $html .= ' required="required"'; }
		if($this->readonly) { $html .= ' readonly="readonly"'; }
		if(isset($args["onchange"])) { $html .= ' onchange="' . $args["onchange"] . '"'; }
		if(isset($args["accept"])) { $html .= ' accept="' . $args["accept"] . '"'; }
		$html .= isset($args["placeholder"]) ? '' : ' placeholder="' . $args["placeholder"] . '"';
		$html .= 	'>';
		$html .= 	'</div>';
		$html .= '</div>';
		return($html);
	}
	#
	# Image
	# upload an image and show it directly
	# $args["uploads"] - upload map of images
	# $args["value"] - current image
	# $args["label"] = label of text box
	# $args["id"] = id and name
	# $args["width"] = width of image
	# $args["heigth"] = width of image
	# $args["required"] = 1 if the box is required
	# $args["collabel"] = bootstrap position label 
	# $args["accept"] = Only accept certain files (e.g. ".jpg,.jpeg")
	#
	public function Image($args)
	{
		$r='';
		if($this->required) { $args["label"] .= "*"; } 
		$value = isset($args["value"]) ? $args["value"] : "";
		$width = isset($args["width"]) ? $args["width"] : $this->width;
		$heigth = isset($args["heigth"]) ? $args["heigth"] : $this->heigth;
		$collabel = isset($args['collabel']) ? $args['collabel'] : $this->collabel;	
		$colinput = isset($args['colinput']) ? $args['colinput'] : $this->colinput;	
		$id=$args["id"];
		$value=isset($args["value"]) ? $args["value"] : '';
		$html .= '<div class="form-group row">';
		if(isset($args["label"]))
		{
			$html .= 	'<div class="' . $collabel .'">';
			$html .= 		'<label for="' . $args["id"] . '"';
			$html .= 		'>' .  $args["label"] . '</label>';
			$html .=	'</div>';
		}
		#$html .= '<div class="' . $this->colinput . '">';
		#
		# image element to place image in it
		#
		$uploads = $args['uploads'];
		$photo_url = home_url() . '/' . $uploads  . '/' . $value;
		$photo_map = ABSPATH . '/' . $uploads  . '/' . $value;
		$html .= '<img id="showimage" src="' . $photo_url . '?' . filemtime($photo_file) .'" width="' . $width .'" height="' . $heigth . '" alt="foto">';
		#
		# input the file
		# the class showimage is trigger for javascript ShowImage to show the image in the img above
		#
		$html .= '<div class="' . $colinput . '">';
		$html .= '<input type="file" id="' . $id . '" class="form-control showimage" name="' . $id . '" value="' . $value . '"';
		$html .= ' style="width:400px;"';
		if($this->required) { $html .= ' required="required"'; }
		if(isset($args["accept"])) { $html .= ' accept="' . $args["accept"] . '"'; }
		$html .= '>';
		$html .= '</div>';
		$html .= '</div>';
		return($html);
	}
	#
	# upload the seleceted file
	#
	#targetdir = directory to put the file in
	#name = name attribute of input element
	#filetypes = legal filetypes seperated by , e.g.: doc,docx,pdf
	#maxkb = maximum size of file in Kb
	#overwrite=1 (overwrite existing file allowed)
	#filename = filename (without extension), if not defined the original filename of the uploaded file is given
	#			extension is extension of original file
	#prefix=unique prefix to force unique filename (optional)
	#return value:
	# 1 : Bad filetype
	# 2 : file exists
	# 3 : file too big
	# 4 : File cannot be uploaded
	# 0 : upload succesfull
	public function UploadFile($args)
	{
		if(!isset($args["name"])) { $this->uploaderror = "name attribute not defined"; return(1); }
		$name = $args["name"];
		$prefix = isset($args["prefix"]) ? $args["prefix"] : "";
		$overwrite = isset($args["overwrite"]) ? $args["overwrite"] : FALSE;
		$ext = pathinfo($_FILES[$name]["name"], PATHINFO_EXTENSION);
		$filename = isset($args["filename"]) ? $args["filename"] . '.' . $ext : basename($_FILES[$name]["name"]);
		$file = $args["targetdir"] . '/' . $prefix . $filename;
		if(isset($args["filetypes"]))
		{
			$fileType = strtolower(pathinfo($file,PATHINFO_EXTENSION));
			$types=explode(",",$filetypes);
			$found = false;
			foreach($types as $t) { if($t == $fileType) { $found=true; } }
			if($found == false) { $this->uploaderror = "bad filetype"; return(FALSE); }
		}
		if(isset($args["maxkb"]))
		{
			$fileSize = $_FILES[$name]["size"];
			$maxsize = $args["maxkb"] * 1000;
			if($fileSize > $maxsize) { $this->uploaderror = " file too big"; return(FALSE); }
		}
		if($overwrite == FALSE && file_exists($file)) { $this->uploaderror = "file exists"; return(FALSE); }
		if (!move_uploaded_file($_FILES[$name]["tmp_name"], $file)) { $this->uploaderror = "cannot upload"; return(FALSE); }
		return(TRUE);
	}
	#
	# StoreImage
	# store the image in the map 
	# $args["uploads"] - upload map of images
	# $args["id"] = id and name
	# $args["name"] = name of image
	# $args["width"] = wiidth of image
	public function StoreImage($args)
	{
		$map = ABSPATH . $args["uploads"];
		echo '<br>map=' . $map . 'id=' . $args['id'];
		echo '<br>';
		#print_r($_FILES);
		if(isset($_FILES[$args["id"]]))
		{
			echo '<br>startupload';
			if (move_uploaded_file($_FILES[$args["id"]]['tmp_name'], $map))
			{
				return(TRUE);
			}
			return(FALSE);
		}
		return(FALSE);
	}
	#
	# resize the image 
	#
	public function resize_image($file, $w, $h, $crop=FALSE) 
	{
		list($width, $height) = getimagesize($file);
		$r = $width / $height;
		if ($crop) 
		{
			if ($width > $height) 
			{
				$width = ceil($width-($width*abs($r-$w/$h)));
			} 
			else 
			{
				$height = ceil($height-($height*abs($r-$w/$h)));
			}
			$newwidth = $w;
			$newheight = $h;
		} 
		else 
		{
			if ($w/$h > $r) 
			{
				$newwidth = $h*$r;
				$newheight = $h;
			} 
			else 
			{
				$newheight = $w/$r;
				$newwidth = $w;
			}
		}
		$src = imagecreatefromjpeg($file);
		$dst = imagecreatetruecolor($newwidth, $newheight);
		imagecopyresampled($dst, $src, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
		imagejpeg( $dst, $file );
		return;
	}
}

/*
$handle = new Upload($_FILES[$args["id"]],'nl_NL');
			if($handle->uploaded)
			{
				$handle->file_new_name_body   = $args["name"];
				$handle->image_resize = true;    		#Make sure the image is resized
				$handle->file_overwrite = true;
				$handle->image_x = $args["width"];					#Set the width of the image
				$handle->image_ratio_y = true;			#Ensure the height of the image is calculated based on ratio
				$handle->process($map);	#Process the image resize and save the uploaded file to the directory
				if($handle->processed) 					#Proceed if image processing completed sucessfully
				{
					$handle->clean();					# Reset the properties of the upload object
					return(TRUE);
				}
			}
*/
?>