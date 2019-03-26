<!--testing website testrestapp132.herokuapp.com -->
<head>
<title>UI</title>
</head>
<body>
<center>
<table style="width:100%">
  <tr>
    <td>
	<form method = "post">
		Select restaurant option:
		<select name ="option">
			<option value="taco">Taco</option>
			<option value="pizza">Pizza</option>
		</select>
		<br><br>
		Select price range:
		<div class="range">
			<input type="range" name ="price" min="0" max="4" steps="1" value="0">
		</div>
		Cheap&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Expensive<br><br><br>
		<input type = "submit">
	</form>
	</td>
    <td rowspan="2"><iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d11998.018217782852!2d-96.01344799760741!3d41.254348900000004!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x87938db043742f41%3A0x1c6f28dd6f040e06!2sUniversity+of+Nebraska+Omaha!5e0!3m2!1sen!2sus!4v1551744692846" width="600" height="450" frameborder="0" style="border:0" allowfullscreen></iframe></th>
  </tr>
  <!-- getting all the Post arguments -->
  Arguments:
  <?php echo $_POST["option"]; ?>
  <?php echo $_POST["price"]; ?>
</table>
</center>
</body>