<?php
#
# dbio 
# read and write database 
#
namespace MDT;

class dbio
{
	#
	# get description of all columns
	#
	public function DescribeColumns($args)
	{
		global $wpdb;
		$wptable = $wpdb->prefix . $args["table"];
		$query = 'DESCRIBE '.$wptable;
		$result=$wpdb->get_results($wpdb->prepare($query));
		return($result);
	}
	public function Columns($table)
	{
		global $wpdb;
		$wptable = $wpdb->prefix . $table;
		$columns = $wpdb->get_col("DESC {$wptable}", 0);
		return($columns);
	}

	public function ReadRecord($args)
	{
		global $wpdb;
		$table = isset($args["table"]) ? $args["table"] : "";
		$wptable = $wpdb->prefix . $table;
		$id = isset($args["id"]) ? $args["id"] : "";
		$query='SELECT * FROM '. $wptable .' WHERE  id ="' . $id .'"';
		$row=$wpdb->get_row( $query );
		return($row);
	}
	# ReadRecords 
	# $args['table'] - databasetable
	# $args['sort'] - column to be sorted
	# $args['prefilter'] - overall filter defined in call (columnname:value)
	# $args['filters'] - 
	# $args['page'} - current pagenumber
	# $args['maxlines'] - maxlines per page
	#
	public function ReadRecords($args)
	{
		$table = isset($args["table"]) ? $args["table"] : "";		
		$sort = isset($args["sort"]) ? $args["sort"] : "";
		$prefilter = isset($args["prefilter"]) ? $args["prefilter"] : "";
		$filters = isset($args["filters"]) ? $args["filters"] : "";
		$page = isset($args["page"]) ? $args["page"] : "";
		$maxlines = isset($args["maxlines"]) ? $args["maxlines"] : "";

		#
		# make conditions for the query
		#
		$conditions='';
		#
		# translate filters to query conditions
		#
		#
		# first check prefilter
		#
		if($prefilter)
		{
			foreach($prefilter as $i => $value) 
			{
				if($conditions) {$conditions .= ' and '; }
				$conditions .= '('. $i . '="' . $value . '")';
			}
		}
		if($filters)
		{
			foreach($filters as $f => $value)
			{
				if($conditions) {$conditions .= ' and '; }
				#
				# If < or > before value search on <= resp >=
				#
				if(preg_match('/^>(.*)/',$value,$match))   
				{
					$value = $match[1];
					$conditions .= '('. $f . ' >= "' . $value . '")';
				}
				#
				# when prefix of filter is max_ then the key  the maximum value of a field.
				#
				elseif(preg_match('/^<(.*)/',$value,$match))   
				{
					$value = $match[1];
					$conditions .= '('. $f . ' <= "' . $value . '")';
				}
				# if key numerical search on full field or word in field
				#
				#
				elseif(is_numeric($value))
				{
					$conditions .= '('. $f . ' = "' . $value . '"';
					$conditions .= ' or ';
					$key = '"' . $value . '" ';
					$conditions .= $f . ' LIKE ' . $key;
					$conditions .= " or ";
					$key = ' "' . $value . '" ';
					$conditions .= $f . " LIKE " . $key;
					$conditions .= " or ";
					$key = ' "' . $value . '"';
					$conditions .= $f . ' LIKE ' . $key . ')';
				}
				else
				{
					if(preg_match("/#/",$value))
					{
						$key=substr($value,1);   #search on full content
					}
					else
					{
						$key = "%" . $value . "%"; #match on content
					}
					$conditions .= '('. $f . ' LIKE "' . $key . '")';
				}
			}
		}
		#
		# start the query
		#
		#echo "<br>conditions=" . $conditions;
		global $wpdb;
		$wptable = $wpdb->prefix . $table;
		$query='SELECT * FROM '. $wptable;
		if($conditions) { $query .= ' WHERE ' . $conditions;}
		#
		# sort argument
		# translate to query sort field
		#
		#echo "<br>sort=" . $sort;
		if($sort &&  $sort != "no")
		{
			$query .= ' ORDER BY ' . $sort;
		}
		#
		# $limit is maximum number of rows to be displayed
		# $page = current pagenumber
		# so calculate offset
		#
		if($maxlines)
		{
			$offset=0;
			if(is_numeric($maxlines)) { $offset=($page-1)*$maxlines; }
			$query .= ' LIMIT '.$offset.','. $maxlines;
		}
		#
		#echo '<br>' . $query;
		$rows=$wpdb->get_results( $query );
		return($rows);
	}
	#
	# create a record
	# the fields created and modified are set to the current date
	#
	public function CreateRecord($args)
	{
		global $wpdb;
		$wptable = $wpdb->prefix . $args["table"];
		$query = 'INSERT INTO ' . $wptable . '(';
		foreach ($args["fields"] as $f =>$value)
		{
			$query .= $f .',';
		}
		$query = rtrim($query,',');	#remove last komma
		$query .= ')';
		$query .= ' VALUES (';
		foreach ($args["fields"] as $f =>$value)
		{
			if($f == "created") { $value = date("Y-m-d H:i:s"); }
			if($f == "modified") { $value = date("Y-m-d H:i:s"); }
			$query .= '"' . $value . '",';
		}
		$query = rtrim($query,',');	#remove last komma
		$query .= ')';
		echo $query;
		$sql=$wpdb->prepare($query);
		#print_r($sql);
		$result=$wpdb->query($sql);
		#print_r($result);
		return($result);
	}
	public function ModifyRecord($args)
	{
		global $wpdb;
		$wptable = $wpdb->prefix . $args["table"];
		$query = 'UPDATE('.$wptable . ')';
		$query .= ' SET';
		foreach ($args["fields"] as $f =>$value)
		{
			if($f == "modified") { $value = date("Y-m-d H:i:s"); }
			$query .= ' ' . $f . '="' .$value . '",';
		}
		$query = rtrim($query,',');	#remove last komma
		$query .= ' WHERE id='.$args["id"].";";
		#echo $query;
		
		$result=$wpdb->query($wpdb->prepare($query));
		return($result);
	}
	public function DeleteRecord($args)
	{
		global $wpdb;
		$wptable = $wpdb->prefix . $args["table"];
		$result=$wpdb->delete( $wptable, array( 'id' => $args["id"] ) );
		return($result);
	}
	#
	# display all fields of a record
	#
	public function DisplayAllFields($args)
	{
		global $wp;
		global $wpdb;
		$table = isset($args["table"]) ? $args["table"] : "";
		$wptable = $wpdb->prefix . $table;
		$id = isset($args["id"]) ? $args["id"] : "";
		$html = '';
		#
		# get the column names in the table
		#
		$columns = $wpdb->get_col("DESC {$wptable}", 0);
		$p=$this->ReadRecord($args);
		/*
		$html .= '<table class="pranatable">';
		foreach($columns as $c)
		{
			$x=$f->Field;
			$form .= '<tr>';
			$form .= '<td>'. $c . '</td><td>' . $p->$c . '</td></tr>';
		}
		$form .= '</table>';
		*/
		#
		# display content of all fields
		#
		foreach($columns as $c)
		{
			$x=$f->Field;
			$html .= '<div class="form-group row" style="margin-bottom:2px;">';
			$html .= 	'<div class="col-md-2">';
			$html .= $c;
			$html .= '</div>';
			$html .= 	'<div class="col-md-6">';
			$html .= $p->$c;
			$html .= '</div>';
			$html .= '</div>';
		}
		return($html);
	}
}
?>
