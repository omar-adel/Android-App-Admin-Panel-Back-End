<?php
	include_once('connect_database.php');  
	include_once('functions.php'); 
?>

<div id="content">
	<?php 
		if(isset($_POST['btnChange'])){
			$tax = $_POST['tax'];
			$currency = $_POST['currency'];
			
			// create array variable to handle error
			$error = array();
			
			if(empty($tax)){
				$tax = 0;
			}else if(!is_numeric($tax)){
				$error['tax'] = "*Tax should be in numeric.";
			}
				
			if(empty($currency)){
				$currency = "USD";
			}else{
				
				// update currency symbol in setting table
				$sql_query = "UPDATE tbl_setting
						SET Value = ? 
						WHERE Variable = 'Currency'";
				
				$stmt = $connect->stmt_init();
				if($stmt->prepare($sql_query)) {	
					// Bind your variables to replace the ?s
					$stmt->bind_param('s', $currency);
					// Execute query
					$stmt->execute();
					// store result 
					$update_result = $stmt->store_result();
					$stmt->close();
				}
				
			}
			
			if(is_numeric($tax)){
			
				// update tax in setting table
				$sql_query = "UPDATE tbl_setting 
						SET Value = ? 
						WHERE Variable = 'Tax'";
				
				$stmt = $connect->stmt_init();
				if($stmt->prepare($sql_query)) {	
					// Bind your variables to replace the ?s
					$stmt->bind_param('s', $tax);
					// Execute query
					$stmt->execute();
					// store result 
					$update_result = $stmt->store_result();
					$stmt->close();
				}
			}
			
			
				// check update result
			if($update_result){
				$error['update_setting'] = "*Setting data has been successfully updated.";
			}else{
				$error['update_setting'] = "*Failed updating setting data.";
			}
			
			
		}		
		
		// get previous tax from setting table
		$sql_query = "SELECT Value 
				FROM tbl_setting 
				WHERE Variable = 'Tax'";
		
		$stmt = $connect->stmt_init();
		if($stmt->prepare($sql_query)) {	
			// Execute query
			$stmt->execute();
			// store result 
			$stmt->store_result();
			$stmt->bind_result($previous_tax);
			$stmt->fetch();
			$stmt->close();
		}	
		
		// get previous currency symbol from setting table
		$sql_query = "SELECT Value 
				FROM tbl_setting 
				WHERE Variable = 'Currency'";
		
		$stmt = $connect->stmt_init();
		if($stmt->prepare($sql_query)) {	
			// Execute query
			$stmt->execute();
			// store result 
			$stmt->store_result();
			$stmt->bind_result($previous_currency);
			$stmt->fetch();
			$stmt->close();
		}	
		
	?>
	<h1>Setting</h1>
	<hr />
		<form method="post">
			<p>Tax(%):</p>
			<input type="text" name="tax" value="<?php echo $previous_tax;?>" />
			<p class="alert"><?php echo isset($error['tax']) ? $error['tax'] : '';?></p>
			<p>Currency:</p>
			<select name="currency">
			<?php 
				$function = new functions;
				$arr_currency = $function->currency_info;
				$size = count($arr_currency);
				for($i=0;$i<$size;$i++){
				if($previous_currency == $arr_currency[$i]['code']){?>
					<option value="<?php echo $arr_currency[$i]['code']; ?>" selected="<?php echo $arr_currency[$i]['code']; ?>" ><?php echo $arr_currency[$i]['code']." - ".$arr_currency[$i]['name']; ?></option>
				<?php }else{ ?>
					<option value="<?php echo $arr_currency[$i]['code']; ?>" ><?php echo $arr_currency[$i]['code']." - ".$arr_currency[$i]['name']; ?></option>
				<?php }} ?>
			</select>
			<br />
			<input type="submit" value="Change" name="btnChange"/>
			<p class="alert"><?php echo isset($error['update_setting']) ? $error['update_setting'] : '';?></p>
		</form>
	<div class="separator"> </div>
</div>
			
<?php include_once('close_database.php'); ?>