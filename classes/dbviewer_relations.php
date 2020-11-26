<?php
#########################################################################################################
# databaseviewer
# Defines a view on a table , or joined tables in the database
#########################################################################################################
namespace MDT;

class dbviewer_relations
{
	#
	# define the table to be maintained
	# prefix = prefix of tablename
	#
	const MAINTABLE = "relations";
	public $textfile;
	public $photos;

	public function TableName($prefix)
	{
		$table = $prefix . '_relations';
		$this->photos = 'wp-content/plugins/maintain-database-table/uploads';
		$this->textfile = dirname( __FILE__ ) . '/../data/relations_nl.txt';
		return($table);
	}
	#
	# display the form to enter the relationdata
	# $administrator : data is entered by administrator, so all fields are enabled e.g. relationtype can be set
	# args:
	# record=>objectarray of fields
	# textfile=>file with jtext messages
	# selfservice=>true when user subscribes self
	# photos=>map for photos
	#
	public function DisplayForm($args)
	{
		$form = new pranaform();
		$jtext = new jtext2();
		$form->required=false;
		$form->row=FALSE;
		$record=$args["record"];
		#
		# intro
		#
		$textfile=$this->textfile;
		$relationtype=jtext::_($textfile,"FORM_RELATIONS_RELATIONTYPE");
		$status=jtext::_($textfile,"FORM_RELATIONS_STATUS");
		$photo=jtext::_($textfile,"FORM_RELATIONS_PHOTO");
		$gender=jtext::_($textfile,"FORM_RELATIONS_GENDER");
		$firstname=jtext::_($textfile,"FORM_RELATIONS_FIRSTNAME");
		$middlename=jtext::_($textfile,"FORM_RELATIONS_MIDDLENAME");
		$lastname=jtext::_($textfile,"FORM_RELATIONS_LASTNAME");
		$street=jtext::_($textfile,"FORM_RELATIONS_STREET");
		$house=jtext::_($textfile,"FORM_RELATIONS_HOUSE");
		$zipcode=jtext::_($textfile,"FORM_RELATIONS_ZIPCODE");
		$place=jtext::_($textfile,"FORM_RELATIONS_PLACE");
		$email=jtext::_($textfile,"FORM_RELATIONS_EMAIL");
		$emailmatch=jtext::_($textfile,"FORM_RELATIONS_EMAILMATCH");
		$phone=jtext::_($textfile,"FORM_RELATIONS_PHONE");
		$mobile=jtext::_($textfile,"FORM_RELATIONS_MOBILE");
		$appartment=jtext::_($textfile,"FORM_RELATIONS_APPARTMENT");
		$privacy=jtext::_($textfile,"PLG_SYSTEM_ACCOUNT_PRIVACY");
		$html .= '<h1>' . jtext::_($textfile,"FORM_RELATIONS_HEADER") . '</h1>';
		$html .= jtext::_($textfile,"FORM_RELATIONS_INTRO");
		$html .= '<br><br>';

		if(isset($record->id)) { $html .= '<input id="relationid" name="relationid" type="hidden" value="' . $record->id .  '">'; } # record mofification
		###################
		# ask for photo
		###################
		$html .= '<div class="row">';
		$value = $record->photo ? $record->photo : "emptyphoto.jpeg";
		$html .= '<input id="oldphoto" name="oldphoto" type="hidden" value="' . $value .  '">'; #current photo used when no new photo is choosen
		$html .= $form->Image(array("label"=>$photo,"uploads"=>$this->photos,"id"=>"photo","value"=>$value,"collabel"=>"col-md-3","width"=>"150px;","heigth"=>"150px;","accept"=>".jpg,.jpeg"));
		$html .= '</div>';
		#
		# status and relationtype
		#
		if($args["selfservice"] == TRUE)  # status and relationtype can not be changed by user self
		{	
			$html .= '<input id="status" name="status" type="hidden" value="' . $record->status .  '">';
			$relationtype=json_decode($record->relationtype);
			foreach($relationtype as $value)
			{
				$html .= '<input id="relationtype" name="relationtype[]" type="hidden" value="' . $value .  '">'; #relationtype is encoded
			}
		}
		else
		{
			#
			# status and relationtype
			#
			$html .= '<div class="row">';
			$statuses=json_decode(jtext::_($textfile,"FORM_RELATIONS_STATUSES"));
			$html .= $form->Dropdown(array("label"=>$status, "id"=>"status", "value"=>$record->status, "options"=>$statuses));
			#
			# relationgroups
			#
			/*
			$plugin = JPluginHelper::getPlugin('system', 'relationshellasduin');
			if ($plugin) { $pluginParams = new JRegistry($plugin->params); }
			$groups = $pluginParams->get('groups');
			$groups = json_decode($groups);
			$options=array();
			$relationtypes=json_decode($record->relationtype);
			foreach ($groups as $group)
			{
				$d=$options[] = new JObject(); $d->value=$group; $d->name=$group;
				$d->checked="";
				if(isset($relationtypes))
				{
					foreach($relationtypes as $r)
					{
						if($group == $r) { $d->checked="checked"; }
					}
				}
			}
			$relationtype=json_decode($record->relationtype);
			$html .= $form->CheckBox(array("label"=>$relationtype,"id"=>"relationtype", "value"=>$record->relationtype, "options"=>$options, "width"=>"600","readonly"=>$readonly,"col"=>"col-md-6"));
			*/
			$html .= '</div>';
		}
		#
		# geslacht naam
		#
		$html .= '<div class="row">';
		$html .= $form->Text(array("label"=>$firstname,"id"=>"firstname","value"=>$record->firstname));
		$html .= $form->Text(array("label"=>$middlename,"id"=>"middlename","value"=>$record->middlename));
		$html .= $form->Text(array("label"=>$lastname,"id"=>"lastname","value"=>$record->lastname));
		$genders=json_decode(jtext::_($textfile,"FORM_RELATIONS_GENDERS"));
		$html .= $form->DropDown(array("label"=>$gender,"id"=>"gender","options"=>$genders,"value"=>$record->gender)); 
		$html .= '</div>';
		#
		# street house
		#
		$html .= '<div class="row">';
		$html .= $form->Text(array("label"=>$street,"id"=>"street","value"=>$record->street,"col"=>"col-md-3"));
		$html .= $form->Text(array("label"=>$house,"id"=>"house","value"=>$record->house,"col"=>"col-md-3"));
		$html .= '</div>';
		#
		# zipcode place
		#
		$html .= '<div class="row">';
		$html .= $form->Text(array("label"=>$zipcode,"id"=>"zipcode","value"=>$record->zipcode));
		$html .= $form->Text(array("label"=>$place,"id"=>"place","value"=>$record->place));
		$html .= '</div>';
		#
		# email
		#
		$html .= '<div class="row">';
		$html .= $form->Text(array("label"=>$email,"id"=>"email","width"=>"200","value"=>$record->email,"check"=>"checkemail","error"=>"fout emailadres"));
		$html .= '</div>';
		#
		# phone,mobile
		#
		$html .= '<div class="row">';
		$html .= $form->Text(array("label"=>$phone,"id"=>"phone","value"=>$record->phone));
		$html .= $form->Text(array("label"=>$mobile,"id"=>"mobile","value"=>$record->mobile));
		$html .= '</div>';
		#
		# appartement
		#
		$html .= '<div class="row">';
		$html .= $form->Text(array("label"=>$appartment,"id"=>"appartment","value"=>$record->appartment));
		$html .= '</div>';
		/*
		$html .= '<div class="row">';
		if(isset($_POST['privacy']))
		{
			$url = JURI::base() . 'index.php/' . $_POST['privacy'];
			$text = JText::_("PLG_SYSTEM_ACCOUNT_PRIVACY_AGREE");
			#
			# privacy declaration is a link:
			$text .= '&nbsp;&nbsp;<a href="' . $url . '" target="_blank">' . $privacy . '</a><br><br>';
			$html .= $prana->SingleCheckBox("privacy",1,$text,"col-md-6");
			#
			# privacy declaration is a modal
			#
			#$html .= $prana->modal($privacy,$_POST['privacy']);
		}
		$html .= '</div>';
		*/
		return($html);
	}
	#
	# get the new content of the record after a form submit made buh displayform(0
	# $args["columns"} - columns got by dbio
	#
	public function RenewRecord($args)
	{
		$form = new pranaform();
		$modfields=array();
		foreach ($args["columns"] as $c)
		{
			if(isset($_POST[$c->Field]))
			{
				#if($c->Field == "relationtype") { $value = json_encode($_POST['relationtype']); }
				$modfields[$c->Field] = $_POST[$c->Field];
			}
		}
		#
		# Is a photo uploaded?
		#
		$modfields["photo"] = $_POST["oldphoto"];
		if(isset($_FILES["photo"]) && $_FILES["photo"]["name"])
		{
			echo "<br>new foto";
			$ext = pathinfo($_FILES["photo"]["name"], PATHINFO_EXTENSION);
			$filename=$modfields["email"] . '.' . $ext;	#photo is renamed to email.ext
			if($form->UploadFile(array("name"=>"photo","filename"=>$modfields["email"],"targetdir"=>$this->photos,"overwrite"=>TRUE,"maxkb"=>"1000")) == TRUE)
			{
				$form->resize_image($this->photos . '/' . $filename, 200, 200);
				$modfields["photo"] = $filename;
			}
			else
			{
				echo $form->uploaderror;
			}
		}
		$modfields = (object) $modfields;
		return($modfields);
	}
}
