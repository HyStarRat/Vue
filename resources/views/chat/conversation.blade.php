@extends('_layouts.auth')

@section('page_title', 'Conversation box')

@section('page_description', 'This is the conversation box')

@section('stylesheets')
	<link href="/assets/pages/css/profile.css" rel="stylesheet" type="text/css"/>
	<link rel="stylesheet" type="text/css" href="/css/sweetalert.css" />
	<link href="https://cdnjs.cloudflare.com/ajax/libs/fancybox/2.1.5/jquery.fancybox.min.css" type="text/css" rel="stylesheet" />
@stop

	<style type="text/css">
	div.gallery ul {
	list-style-type: none;
	margin-left: 5px;

	}
	.photosup {
    position: relative;
	}
	div.gallery ul li {
	position: relative;
	float: left;
	width: 130px;
	height: 130px;
	margin: 5px;
	padding: 5px;
	z-index: 0;
	}
	div.gallery ul li input[type="image" i] {
	position: absolute;
	left: 0;
	top: 0;
	border: 1px solid #dddddd;
	padding: 5px;
	width: 130px;
	height: 130px;
	background: #f0f0f0;
	}
	.gallery {
	clear: both;
    height: 150px;
    overflow: overlay;
    padding: 6px 10px 7px 10px;
    background-color: #fff;
	box-shadow: 0px 2px 5px 2px rgba(0, 0, 0, 0.1);
    border: 0 !important;    
    margin-bottom: 10px;
	}
	div.gallery ul {
    list-style-type: none;
    margin-left: -44px;
	}
	.privbtn {
    margin-bottom: 5px;
	}
	</style>

@section('content')
	{!! Breadcrumbs::render('chat:conversation', $website, $conversation) !!}
	@if(count($photo_album))
			<div id="hidden">
				<button class="btn btn-primary privbtn" onclick="PrivPhotos()">Show Private Gallery</button><br /><br />
			</div>
			<div  id="hidden2" style="display: none;">
				<button class="btn red-pink privbtn" onclick="PrivPhotos()">Hide Private Gallery</button><br /><br />
				<div class="gallery">
					<ul>			
					@foreach($photo_album as $data)

					 	<li><input type="image" src="http://my365love.com/ow_userfiles/plugins/photo/photo_{{$data->photo_id}}_{{$data->hash}}.jpg" id="btnSeven" value="{{$data->photo_id}}" onclick="copypriv_{{$data->photo_id}}()" />

					 	<input style="width: 1px;height: 1px" type="text" value="<a href='/ow_userfiles/plugins/photo/photo_{{$data->photo_id}}_{{$data->hash}}.jpg' target='_blank'><img style ='max-width: 100%' src='/ow_userfiles/plugins/photo/photo_{{$data->photo_id}}_{{$data->hash}}.jpg' /></a>" id="myInput_{{$data->photo_id}}"></li>


					 		<script>
							function copypriv_{{$data->photo_id}}() {
									// I need you to take this and send the value that is generated into the text field for the chat system. 
							 alert("We are currently working on this feature. please try again later. Photo selected:{{$data->photo_id}}");	 			
							}
							</script>
					@endforeach
					</ul>
				</div>
			</div>	
	@endif	
	<conversation :website="{{ $website }}" :conversation="{{ $conversation }}"></conversation>
@stop



@section('scripts')
	<script src="https://cdnjs.cloudflare.com/ajax/libs/fancybox/2.1.5/jquery.fancybox.min.js" type="text/javascript" charset="utf-8"></script>

	<script type="text/javascript">
		$('a.attachment').fancybox();
	</script>


	<script>
	function PrivPhotos() {
    var x = document.getElementById("hidden");
    if (x.style.display === "none") {
        x.style.display = "block";
    } else {
        x.style.display = "none";
    }
    var y = document.getElementById("hidden2");
    if (y.style.display === "none") {
        y.style.display = "block";
    } else {
        y.style.display = "none";
    }
}
</script>
@stop
