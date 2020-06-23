<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if ( ! function_exists('query_to_xls')){

	 function query_to_xls($query, $headers = TRUE, $download = ""){

		 if ( ! is_object($query) OR ! method_exists($query, 'list_fields')){

			 show_error('invalid query');

		 }

		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header("Content-Disposition: attachment; filename=\"$download.xls\"");
		header("Content-Type: application/vnd.ms-excel");

		 $array = array();

			$i=1;

			if ($headers){
				/*
				foreach ($query->list_fields() as $name){

					 if($i!=1&&$i!=10) echo $name."\t";
					 $i++;
				}
				*/
				echo "BRAND\tSTOCKID\tDESCRIPTION\tCOST\tPRICE\tBARCODE\tQTY\tCATEGORY\n";

			}

			foreach ($query->result_array() as $row){

				 $j=1;
				 $str="";

				 foreach ($row as $item){

					 if($j==2){

						 $item = preg_replace("/\t/", "", $item);
	    	 		 $item = preg_replace("/\r?\n/", "", $item);
						 $str=$item."\t";
					 }
					 else if($j==3){

						 $item = preg_replace("/\t/", "", $item);
	    	 		 $item = preg_replace("/\r?\n/", "", $item);
						 $str=$item."\t".$str;
					 }

					 if($j>3 && $j<10){

						 $item = preg_replace("/\t/", "", $item);
	    	 		 $item = preg_replace("/\r?\n/", "", $item);
						 $str=$str.$item."\t";
					 }
					 $j++;
				}
				echo $str."\n";
			}
	 }
}
if ( ! function_exists('query_to_xls_age')){

	 function query_to_xls_age($query, $headers = TRUE, $download = ""){

		 if ( ! is_object($query) OR ! method_exists($query, 'list_fields')){

			 show_error('invalid query');

		 }

		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header("Content-Disposition: attachment; filename=\"$download.xls\"");
		header("Content-Type: application/vnd.ms-excel");

		 $array = array();

			$i=1;

			if ($headers){
				/*
				foreach ($query->list_fields() as $name){

					 if($i!=1&&$i!=10) echo $name."\t";
					 $i++;
				}
				*/
				echo "BRAND\tSTOCKID\tDESCRIPTION\tCOST\tPRICE\tBARCODE\tCURRENT_STOCK\tCATEGORY\tINDATE\tQTY\n";

			}

			foreach ($query->result_array() as $row){

				 $j=1;
				 $str="";

				 foreach ($row as $item){

						 $item = preg_replace("/\t/", "", $item);
	    	 		 $item = preg_replace("/\r?\n/", "", $item);
						 $str=$str.$item."\t";

					 	 $j++;
				}
				echo $str."\n";
			}
	 }
}
