<!DOCTYPE html>
<html>
<head>
	<title>{{ $data->title }}</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<style type="text/css">
		body{
			background: url("{{ asset('application/storage/app/'.$data->background)  }}") no-repeat center center fixed; 
			-webkit-background-size: cover;
			-moz-background-size: cover;
			-o-background-size: cover;
			background-size: cover;
		}

		div#contenido{
			position: absolute;
			top: 35%;
			left: 10%;
			height: auto;
			overflow: hidden;
			width: 80%;
			margin: 0px auto;
		}
	</style>
</head>
 <body>
 	<div id="contenido">
 		{!! $content !!}
 	</div>
 </body>
</html>