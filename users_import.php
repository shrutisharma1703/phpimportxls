<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/wp-load.php' );
include 'Classes/PHPExcel/IOFactory.php';
$inputFileName = 'motorcode.xls';

//  Read your Excel workbook
try {
    $inputFileType = PHPExcel_IOFactory::identify($inputFileName);
    $objReader = PHPExcel_IOFactory::createReader($inputFileType);
    $objPHPExcel = $objReader->load($inputFileName);
} catch (Exception $e) {
    die('Error loading file "' . pathinfo($inputFileName, PATHINFO_BASENAME) 
    . '": ' . $e->getMessage());
}

//  Get worksheet dimensions
$sheet = $objPHPExcel->getSheet(0);
$highestRow = $sheet->getHighestRow();
$highestColumn = $sheet->getHighestColumn();


//  Loop through each row of the worksheet in turn

for ($row = 2; $row <= $highestRow; $row++) {
    //  Read a row of data into an array
    $rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, 
    NULL, TRUE, FALSE);    
    $motordata[] = $rowData[0];
	
}

foreach ( $motordata as $k=>$v )
{
  $nickName = $motordata[$k][0];
  $email = $motordata[$k][1];
  $password = $motordata[$k][2];
  $motorId = $motordata[$k][3];
  //Username Unique Check
  $userExists = username_exists( $nickName );
  //Username and Email Unique Check
	if ( !$userExists && email_exists($email) == false ) {		
					
		$userData = array(
								'user_login' 		=>  $nickName,
								'user_pass'	  		=>  $password,
								'user_nicename'	 	=>  $nickName,
								'user_email'	    =>  $email,					
								'display_name'  	=>  $nickName			 
							);
		$userId = wp_insert_user( $userData ) ;
		if(!empty($userId)){					
			update_user_meta( $userId, 'motorcode_member_id', $motorId);
			echo $email." inserted ".$userId ."<br>" ;
		}	
	}else{
		echo $nickName." record already exists ". $email."<br>" ;
	}
 
}
?>
