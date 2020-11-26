<?php
##################################################################################
# class: 		mtndbtable
# description:	
# 				detects the plugin name and arguments form content
#				plugintag should be the following syntax
#				[mtndbtable arg1="...." arg2="...." ...... ]
##################################################################################
namespace MDT;

class mntdbtable
{
	public $textfile;	#file with text of messages
	public $dbview;		#the dbviewer
	public $dbv;
	public $prefix;		#prefix of the tablename in the dbviewer
	public $table;		#tablename
	public $columns;	#columnnames to be displayed seperated by , (e.g. id,name,street,....)
	public $allcolumns;	#all columns of the table
	public $headers;	#headers of the columns in the table
	public $aligns;		#aligns of the columns (left,right or center)
	public $maxlines;	#maximum number of lines per page
	public $selectmaxlines;	#options for maxlines, defined in options.php
	public $permissions;	#permissions for maintaining table cr,md,dl,vw,
	public $onpage = 1;	#pagenummer will be changed bij POST value during 
	public $onsort;		#pagenummer will be changed bij POST value during 
	public $prefilter;	#defined as argument like: field:content Display only the records matching this filter
	public $filters;	#user defined filters
	public $title;		#title of the table, will be displayed above the table/css/table
	public $action;		#url to restart plugin
	
	
	function init($args)
	{
		global $wp;
		$dbio = new dbio();
		$this->textfile=dirname( __FILE__ ) . '/../data/mntdbtable_nl.txt';
		$html = '';
		$this->action = home_url(add_query_arg(array(), $wp->request));
		$this->selectmaxlines=explode(',', get_option('mdt_selectmaxlines'));
		$jQuery = 'https://code.jquery.com/jquery-3.5.1.js';
		$jQuery_UI = 'https://code.jquery.com/ui/1.12.1/jquery-ui.js';
		$jquery_min="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js";
		$bootstrap_min="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js";
		$cdntables = 'https://cdn.datatables.net/1.10.22/js/jquery.dataTables.min.js';
		$mdttables = home_url() . '/wp-content/plugins/maintain-database-table/javascript/mdt_tables.js';
		$forms = home_url() . '/wp-content/plugins/maintain-database-table/javascript/forms.js';
		$exportcsv = home_url() . '/wp-content/plugins/maintain-database-table/javascript/exportcsv.js';

		$ui_css = 'https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css';
		$cdntables_css = 'https://cdn.datatables.net/1.10.22/css/jquery.dataTables.min.css';
		$table_css= home_url() . '/wp-content/plugins/maintain-database-table/css/table.css';
		$forms_css= home_url() . '/wp-content/plugins/maintain-database-table/css/forms.css';
		
    $cdntablesrp_css = 'https://cdn.datatables.net/responsive/2.2.6/css/responsive.dataTables.min.css';

		
		$html .= '<script src="' . $jQuery . '"></script>';
		$html .= '<script src="' . $jQuery_UI . '"></script>';
		$html .= '<script src="' . $jquery_min . '"></script>';
		$html .= '<script src="' . $bootstrap_min . '"></script>';
		$html .= '<script src="' . $cdntables . '"></script>';
		$html .= '<script src="' . $mdttables . '"></script>';
		$html .= '<script src="' . $forms . '"></script>';
		$html .= '<script src="' . $exportcsv . '"></script>';
		
		$html .= '<link rel="stylesheet" href="' . $cdntables_css . '">';
		$html .= '<link rel="stylesheet" href="' . $cdntablesrp_css . '">';
		$html .= '<link rel="stylesheet" href="' . $ui_css . '">';
		#echo $table_css;
		$html .= '<link rel="stylesheet" href="' . $table_css . '">';
		$html .= '<link rel="stylesheet" href="' . $forms_css . '">';
		
		#
		# treat arguments
		#
		$this->prefix = isset($args["prefix"]) ? $args["prefix"] : "";
		$this->maxlines = isset($args["maxlines"]) ? $args["maxlines"] : "";	#if maxline not given maximum = MAXLINES
		$this->headers = isset($args["headers"]) ? explode(',', $args["headers"]) : "";
		$this->aligns = isset($args["aligns"]) ? explode(',', $args["aligns"]) : "";
		$this->permissions = isset($args["permissions"]) ? explode(',', $args["permissions"]) : "no";
		#$this->columns = isset($args["columns"]) ? explode(',', $args['columns']) : $dbio->columns($this->table); # which columns to display? default is all
		$this->columns = isset($args["columns"]) ? json_decode($args["columns"]) : ""; # columns to be filtered
		$this->onsort = isset($args["onsort"]) ? $args['onsort'] : "";	# column to sort on
		$this->prefilter = isset($args["prefilter"]) ? json_decode($args["prefilter"]) : "";
		$this->filtercolumns = isset($args["filtercolumns"]) ? json_decode($args["filtercolumns"]) : ""; # columns to be filtered
		$this->title = isset($args["title"]) ? $args["title"] : "Content of the table";
		
		#
		# The table can be defined by argument "table"'
		# But it also possible to define the table in a special viewer, in which the table is defined or a joined table
		# It is also possible to define a special form for modify or create records
		# load the dbviewer
		# 
		if(isset($args["dbview"]))
		{
			$this->dbview = $args["dbview"];
			#require_once ( dirname( __FILE__ ) . '/../classes/' . $args['dbview'] . '.php');
			$class = 'MDT\\' . 'dbviewer_' . $args["dbview"];
			#$this->dbv = new relations();
			$this->dbv = new $class();
			$this->table = $this->dbv->TableName($this->prefix);
		}
		else
		{
			$this->table = $this->prefix . '_' . $args["table"];
		}
		#
		# table given??
		#
		if(!$this->table)
		{
			$html .= '<div class="isa_error">'. jtext::_($this->textfile,"ERROR_NOTABLE") . '</div>';
			return($html);
		}
		if(!$this->prefix)
		{
			$html .= '<div class="isa_error">'. jtext::_($this->textfile,"ERROR_NOPREFIX") . '</div>';
			return($html);
		}

		$this->allcolumns = $dbio->DescribeColumns(array("table"=>$this->table));	#get information about all columns
		# content of an element is:
		#stdClass Object ( [Field] => id [Type] => int(10) unsigned [Null] => NO [Key] => PRI [Default] => [Extra] => auto_increment )
		#stdClass Object ( [Field] => created [Type] => datetime [Null] => NO [Key] => [Default] => 0000-00-00 00:00:00 [Extra] => )
		#stdClass Object ( [Field] => modified [Type] => datetime [Null] => NO [Key] => [Default] => 0000-00-00 00:00:00 [Extra] => )
		#....
		#
		#
		#
		# treat POST values of variables modified in previous runs
		#
		#print_r($_POST);
		if(isset($_POST['maxlines'])) { $this->maxlines = $_POST['maxlines']; } #maxlines changed by user
		if(isset($_POST['onpage'])) { $this->onpage = $_POST['onpage']; } #maxlines changed by user
		if(isset($_POST['nextpage'])) { $this->onpage += 1; } #maxlines changed by user
		if(isset($_POST['previouspage'])) { $this->onpage -= 1; } #maxlines changed by user
		if(isset($_POST['sort'])) { $this->onsort = $_POST['sort']; }
		if(isset($_POST['newfilters'])) {$this->filters=json_decode(urldecode($_POST['newfilters'])); } #restore user defined filters
		#
		# Associative array always output as object by json_decode
		#
		if(isset($_POST['create']))
		{
			$html .= $this->create();		#modify the record
		}
		elseif(isset($_POST['modify']))
		{
			$html .= $this->modify();		#modify the record
		}
		elseif(isset($_POST['filter']))
		{
			$html .= $this->SetFilters();
		}
		elseif(isset($_POST['modifyrecord'])) 
		{ 
			$html .= $this->ModifyRecord();
		}
		elseif(isset($_POST['createrecord'])) 
		{ 
			$html .= $this->CreateRecord();
		}
		elseif(isset($_POST['deleterecord'])) 
		{ 
			$html .= $this->DeleteRecord();
		}
		else
		{
			$html .= $this->DisplayTable();
			#$html .= $this->ExportRecords();
		}
		return($html);
	}
	public function DisplayTable()
	{
		global $wp;
		$self = new self();
		$dbio = new dbio();
		$form = new pranaform();
		$html = '';
		#
		# count number of records and calculate number of pages
		#
		#$pb = $dbio->ReadRecords(array("table"=>$this->table));
		$pb = $dbio->ReadRecords(array("table"=>$this->table,"prefilter"=>$this->prefilter,"filters"=>$this->filters));
		$NumberOfRecords=count($pb);
		if(!$this->maxlines) { $this->maxlines=$NumberOfRecords; } # if maxlines not defines: show all records
		$pages=ceil($NumberOfRecords/$this->maxlines);
		$pb = $dbio->ReadRecords(array("table"=>$this->table,"page"=>$this->onpage,"maxlines"=>$this->maxlines,"sort"=>$this->onsort,"prefilter"=>$this->prefilter,"filters"=>$this->filters));
		#
		# start the form and table
		#
		$html .= '<div class="prana-display">';
		#
		# Show number of rows and pages and a help function if defined
		#
		#
		$html .='<form action=' . $this->action . ' method="post" name="records">';
		$html .= '<div class="row">';
		$html .= '<div class="col-md-6")>';
		$html .= '<h2>' . $this->title . '</h2><br><br><br>';
		$html .= jtext::_($this->textfile,"PAGING_TOTALRECORDS") . ':' . $NumberOfRecords . '&nbsp' . jtext::_($this->textfile,"PAGING_TOTALPAGES"). ':' . $pages;
		$html .= '</div>';
		$html .= '<div class="col-md-5 prana-box">';
		$html .= '<h3>' . jtext::_($this->textfile,"FORM_SEARCH") . '</h3>';
		#$html .= '<p style="font-size:8px;">' . jtext::_($this->textfile,"INFO_SEARCH") . '</p>';
		#
		# print filterform
		#
		#print_r($this->filters);
		if(isset($this->filtercolumns))
		{
			foreach ($this->filtercolumns as $c => $label)
			{
				$value="";
				#
				# has filters a content?
				#
				if(isset($this->filters->$c))
				{
					$value=$this->filters->$c;
				}
				#echo "<br>before filter" . $value;
				$form->required=FALSE;
				$form->collabel="col-md-5";
				$form->colinput="col-md-7";
				
				$html .= $form->Text(array("label"=>$label, "id"=>$c, "value"=>$value, "popover"=>jtext::_($this->textfile,"INFO_SEARCH")));
			}
		}
		$html .= '<button class="prana-btnsmall" name="filter">' . jtext::_($this->textfile,"BUTTON_SEARCH") . '</button>';
		$html .= '<br><br>';
		$html .= '</div>';
		$html .= '</div>';
		$html .= '</form>';
		#
		# start the table
		#
		$html .='<form action=' . $this->action . ' method="post" name="records">';
		$html .= '<table class="compact-table">';
		$html .= '<thead>';
		$html .= '<tr>';
		$i=0;
		/*
		foreach ($this->columns as $c)
		{
			$h=$c;
			if(isset($this->headers[$i])) { $h=$this->headers[$i]; }	#user defined headers
			$sortfield='<button class="pbtn-header" name="sort" value="' . $c . '">' . $h  . '</button>';
			$html .= '<th>' . $sortfield . '</th>';
			#$html .= '<th>' . $h . '</th>';
			$i++;
		}
		*/
		foreach ($this->columns as $name => $translation)
		{
			$sortfield='<button class="pbtn-header" name="sort" value="' . $name . '">' . $translation  . '</button>';
			$html .= '<th>' . $sortfield . '</th>';
		}
		if (in_array("vw", $this->permissions)) {$html .= '<th></th>';}	#Empty header for view button
		if (in_array("dl", $this->permissions)) {$html .= '<th></th>';}	#Empty header for view button
		if (in_array("md", $this->permissions)) {$html .= '<th></th>';}	#Empty header for view button
		if (in_array("cp", $this->permissions)) {$html .= '<th></th>';}	#Empty header for view button
		$html .= '</tr>';
		$html .= '</thead>';
		#
		# print rows
		#
		$html .= '<tbody>';
		foreach ( $pb as $p )
		{
			$html .= '<tr>';
			$i=0;
			foreach($this->columns as $name => $translation)
			{
				$html .= '<td align="' . $aligns[$i] . '">' . $p->$name . '</td>';
				$i++;
			}
			#
			# view / modify / delete / copy buttons
			#
			if (in_array("vw", $this->permissions)) 
			{
				#$html .= '<td><button type="submit" class="btn btn-link btn-xs showrecord" name="showrecord" value="' . $p->id . '"><i class="fa fa-eye"></i></button>';
				$html .= '<td class="showrecord"><a class="btn btn-link btn-xs"><i class="fa fa-eye"></a></td>';
			}
			
			if (in_array("dl", $this->permissions)) 
			{ 
				$message=sprintf(jtext::_($this->textfile,"LABEL_SUREDELETE"),$p->id);
				#$html .= '<td><button type="submit" class="btn btn-link btn-xs" name="deleterecord" onclick="return confirm(\'' . $message. '\'value="' . $p->id . '"><i class="fa fa-trash"></i></button></td>'; 
				$html .= '<td><button type="submit" name="deleterecord" class="btn btn-link btn-xs" onclick="return confirm(\'' . $message. '\');" value="' . $p->id . '"><i class="fa fa-trash"></i></button></td>';
			}
			if (in_array("md", $this->permissions)) 
			{ 
				$html .= '<td><button type="submit" name="modifyrecord" class="btn btn-link btn-xs" value="' . $p->id . '"><i class="fa fa-pencil"></i></button></td>';
			}
			if (in_array("cp", $this->permissions)) 
			{ 
				$html .= '<td><button type="submit" name="createrecord" class="btn btn-link btn-xs" value="' . $p->id . '"><i class="fa fa-copy"></i></button></td>'; 
			}

			$html .= '</tr>';
			$detail = $dbio->DisplayAllFields(array("table"=>$this->table,"id"=>$p->id));
			$html .= '<tr>';
			$html .= '<td colspan="' . $i .'" class="showdetail">' . $detail . '</td>';
			$html .= '</tr>';
			
			
		}
		$html .= '</tbody>';
		$html .= '</table>';
		#
		# buttons for next and previous page
		#
		$html .= sprintf(jtext::_($this->textfile,"LABEL_PAGES"),$this->onpage,$pages);
		$html .= '&nbsp;&nbsp;&nbsp;';
		if($this->onpage > 1) { $html .= '<button type="submit" class="btn btn-link btn-sx" name="previouspage" value="' . $this->onpage . '"><i class="fa fa-caret-square-o-left" style="font-size:24px"></i></button>'; }
		if($this->onpage < $pages) { $html .= '<button type="submit" class="btn btn-link btn-sx" name="nextpage" value="' . $this->onpage . '"><i class="fa fa-caret-square-o-right" style="font-size:24px"></i></button>'; }
		#
		# create new record
		#
		$html .= '<div style="float:right" class="row">';
		if( in_array("cr", $this->permissions))
		{
			$html .= '<button class="prana-btnhigh" name="createrecord" value="";>'. jtext::_($this->textfile,"FORM_CREATE") . '</button>';
			$html .= $this->ExportRecords();		# export records in csv file
		}
		$html .= '</div>';
		$html .= $this->PostValues();	# save modified variables
		$html .= '<br><br><br>';
		$html .= '</form>';
		$html .= '</div>';
		$html .= '</div>';
		return($html);
	}
	#
	# modify record 
	#
	public function ModifyRecord()
	{
		$form = new pranaform();
		$dbio = new dbio();
		$id=$_POST['modifyrecord'];
		$row=$dbio->ReadRecord(array("table"=>$this->table,"id"=>$id));
		$html = '';
		$html .= '<h2>'. jtext::_($this->textfile,"FORM_MODIFY").'</h2>';
		$html .= '<div class="prana-display">';
		$html .= '<br>';
		$html .='<form action=' . $this->action . ' method="post" enctype="multipart/form-data" name="modifyform">';
		
		if($this->dbview)
		{ 
			#$html .= $this->RawRecordForm(array("record"=>$row));
			$html .= $this->dbv->DisplayForm(array("record"=>$row,"selfservice"=>FALSE));
		}
		else
		{
			$html .= $this->RawRecordForm(array("record"=>$row));
		}
		$html .= '<br><button class="prana-btnhigh checkformbutton" name="modify" value="' . $row->id. '">'. jtext::_($this->textfile,"BUTTON_MODIFY") . '</button>';
		$html .= '<button class="prana-btnhigh">'. jtext::_($this->textfile,"BUTTON_CANCEL") . '</button>';
		$html .= $this->PostValues();	# save modified variables
		$html .= '</form>';
		$html .= '</div>';
		return($html);
	}
	#
	# modify a record in the database
	#
	public function Modify()
	{
		global $wp;
		$dbio = new dbio();
		$id=$_POST['modify'];
		#$html .= '<br>modify:'.$id;
		if($this->dbview)
		{
			$modfields = $this->dbv->RenewRecord(array("columns"=>$this->allcolumns));
		}
		else
		{
			$modfields=array();
			foreach ($this->allcolumns as $c)
			{
				if(isset($_POST[$c->Field]))
				{
					$modfields[$c->Field] = $_POST[$c->Field];
				}
			}
			$modfields = (object) $modfields;
		}
		#print_r($modfields);
		$result = $dbio->ModifyRecord(array("table"=>$this->table,"id"=>$id,"fields"=>$modfields));
		#echo "<br>result=";
		#print_r($result);
		$html .= $this->DisplayTable();
		return($html);
	}
	#
	# create record 
	# all fields are treated as text fields.
	# there is no content check on fields. 
	# only for maintenance purposes
	#
	public function CreateRecord()
	{
		$form = new pranaform();
		$dbio = new dbio();
		$html = '';
		$html .= '<h2>'. jtext::_($this->textfile,"FORM_CREATE").'</h2>';
		$html .= '<div class="prana-display">';
		$html .= '<br>';
		$html .='<form action=' . $this->action . ' method="post" name="createform">';
		#
		# read record just for testing
		if($_POST["createrecord"] > 0) #new record is a copy (copyrecord pressed)
		{
			$row=$dbio->ReadRecord(array("table"=>$this->table,"id"=>$_POST["createrecord"]));
			$row->id="";
			$html .= $this->RawRecordForm(array("record"=>$row));
		}
		else
		{
			$html .= $this->RawRecordForm(0);
		}
		#
		$html .= '<br><button class="prana-btnhigh" name="create" >'. jtext::_($this->textfile,"BUTTON_MODIFY") . '</button>';
		$html .= '<button class="prana-btnhigh">'. jtext::_($this->textfile,"BUTTON_CANCEL") . '</button>';
		$html .= $this->PostValues();	# save modified variables
		$html .= '</form>';
		$html .= '</div>';

		return($html);
	}
	public function Create()
	{
		global $wp;
		$dbio = new dbio();
		$modfields=array();
		foreach ($this->allcolumns as $c)
		{
			if(isset($_POST[$c->Field]))
			{
				$modfields[$c->Field] = $_POST[$c->Field];
			}
		}
		$modfields = (object) $modfields;
		#print_r($modfields);
		$result = $dbio->CreateRecord(array("table"=>$this->table,"fields"=>$modfields));
		echo "<br>result=";
		print_r($result);
		$html .= $this->DisplayTable();
		return($html);
	}
	#
	# RawRecordForm
	# all fields are treated as text fields.
	# except the field is a key field or the fields: created and modified (these fields are set when writing the record into the database
	# there is no content check on fields. 
	# This function is only used for maintenance purposes
	#
	public function RawRecordForm($args)
	{
		$form = new pranaform();
		$form->width="400px";
		$form->required=FALSE;
		foreach ($this->allcolumns as $c)
		{
			$name=$c->Field;
			$value="";
			if(isset($args["record"])) { $value = $args["record"]->$name; }
			$length = (int) filter_var($c->Type, FILTER_SANITIZE_NUMBER_INT);
			$form->readonly="";
			if($c->Key) { continue; }		#index fields are readonly
			if($c->Field == "created" || $c->Field == "modified") { $form->readonly="readonly"; }	
			if($length > 255)
			{
				$html .= $form->TextArea(array("label"=>$c->Field, "id"=>$c->Field, "value"=>$value));
			}
			else
			{
				$html .= $form->Text(array("label"=>$c->Field, "id"=>$c->Field, "value"=>$value, "width"=>"200px;"));
			}
		}
		return($html);
	}
	public function DeleteRecord()
	{	
		$dbio = new dbio();
		$html = '';
		$id=$_POST['deleterecord'];
		$html .= '<h2>record: ' . $id . ' ' . jtext::_($this->textfile,"MESSAGE_REMOVED") . '</h2>';
		$dbio->DeleteRecord(array("table"=>$this->table,"id"=>$id));
		$html .= $this->DisplayTable();
		return($html);
	}
		
	#
	# set filters given by user
	#
	public function SetFilters()
	{
		global $wp;
		$html = '';
		$this->filters = array();
		if(isset($this->filtercolumns))
		{
			foreach ($this->filtercolumns as $c => $label)
			{
				if(isset($_POST[$c]) && $_POST[$c])
				{
					#echo '<br>c='.$c.'label='.$label.'f='.$c.'value='.$_POST[$c];
					$this->filters[$c]=$_POST[$c];
				}
			}
		}
		$this->filters=(object) $this->filters;
		#echo '<br>filters new';
		#print_r($this->filters);
		$html = $this->DisplayTable();
		return($html);
	}
	#
	# Export records to be used in Excell and now using javascript
	#
	public function ExportRecords()
	{
		global $wpdb;
		$dbio = new dbio();
		$export = '';
		$export .= '<div>';
		$export .= '<table style="display:none">';
		#$export .= '<table class="csvexport" style="display:none">';
		$export .= '<tr>';
		foreach ($this->allcolumns as $c)
		{
			$export .= '<th>' . $c->Field . '</th>';
		}
		$export .= '</tr>';
		$pb = $dbio->ReadRecords(array("table"=>$this->table,"prefilter"=>$this->prefilter,"filters"=>$this->filters));
		foreach ( $pb as $p )
		{
			$export .= '<tr>';
			foreach ($this->allcolumns as $c)
			{
				$name=$c->Field;
				$export .= '<td>' . $p->$name . '</td>';
			}
			$export .= '</tr>';
		}
		$export .= '</table>';
		$filename = $this->table . '.csv';	#add csv extension
		#$export .= '<p id="exportfilename" style="display:none">'.$filename.'</p>';
		#$export .= '<button id="exporttable" class="prana-btnhigh">export</button>';   #javascript export.js does the rest
		$export .= '<span style="display:none">'.$filename.'</span>';
		$export .= '<button class="prana-btnhigh exporttable">export</button>';   #javascript export.js does the rest
		$export .= '</div>';
		return($export);
	}
	#
	# save modified variables
	#
	public function PostValues()
	{
		$html='';
		$html .='<input id="onpage" name="onpage" type="hidden" value=' . $this->onpage .  ' />';
		$html .='<input id="onsort" name="onsort" type="hidden" value=' . $this->onsort .  ' />';
		if($this->filters) 
		{
			$html .='<input id="newfilters" name="newfilters" type="hidden" value=' . urlencode(json_encode($this->filters)) .  ' />';
		}
		/*
		$form .='<input id="table" name="table" type="hidden" value=' . $_POST['table'] .  ' />';
		if(isset($_POST['tableview'])) { $form .='<input id="tableview" name="tableview" type="hidden" value=' . $_POST['tableview'] .  ' />'; }
		if(isset($_POST['jointable'])) { $form .='<input id="jointable" name="jointable" type="hidden" value=' . $_POST['jointable'] .  ' />'; }
		$form .='<input id="columns" name="columns" type="hidden" value=' . $_POST['columns'] .  ' />';
		if(isset($_POST['headers'])) { $form .='<input id="headers" name="headers" type="hidden" value=' . $_POST['headers'] .  ' />'; }
		if(isset($_POST['aligns'])) {$form .='<input id="aligns" name="aligns" type="hidden" value=' . $_POST['aligns'] .  ' />';}
		if(isset($_POST['filters'])) {$form .='<input id="filters" name="filters" type="hidden" value=' . $_POST['filters'] .  ' />';}
		if(isset($_POST['sortfield'])) {$form .='<input id="sortfield" name="sortfield" type="hidden" value="' . $_POST['sortfield'] .  '" />';}
		if(isset($_POST['prefilter'])) { $form .='<input id="prefilter" name="permissions" type="hidden" value=' . $_POST['prefilter'] .  ' />'; }
		if(isset($_POST['permissions'])) { $form .='<input id="permissions" name="permissions" type="hidden" value=' . $_POST['permissions'] .  ' />'; }
		if(isset($_POST['prana_page'])) { $form .='<input id="prana_page" name="prana_page" type="hidden" value=' . $_POST['prana_page'] .  ' />'; }
		if(isset($_POST['maxlines'])) {$form .='<input id="maxlines" name="maxlines" type="hidden" value=' . $_POST['maxlines'] .  ' />'; }
		if(isset($_POST['searchstring'])) { $form .='<input id="searchstring" name="searchstring" type="hidden" value=' . $_POST['searchstring'] .  ' />';}
		if(isset($_POST['cmfunction'])) $form .='<input id="cmfunction" name="cmfunction" type="hidden" value=' . $_POST['cmfunction'] .  ' />';
		#
		# POST values for uploading files
		#
		if(isset($_POST['maxupload'])) $form .='<input id="maxupload" name="maxupload" type="hidden" value=' . $_POST['maxupload'] .  ' />';
		if(isset($_POST['filetypes'])) $form .='<input id="filetypes" name="filetypes" type="hidden" value=' . $_POST['filetypes'] .  ' />';
		if(isset($_POST['fileoverwrite'])) $form .='<input id="fileoverwrite" name="fileoverwrite" type="hidden" value=' . $_POST['fileoverwrite'] .  ' />';
		*/
		return($html);
	}
}
?>	