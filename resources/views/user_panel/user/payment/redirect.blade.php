<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<title>{{ __(basicControl()->site_title) }}</title>
</head>
<body>
<form action="{{$data->url}}" method="{{$data->method}}" target="some_iframe" id="auto_submit">
	@foreach($data->val as $k => $v)
		<input type="hidden" name="{{ $k }}" id="{{ $k }}" value="{{ $v }}"/>
	@endforeach
</form>
<script>
	document.getElementById("auto_submit").submit();
</script>
</body>
</html>

